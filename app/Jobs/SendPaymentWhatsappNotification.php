<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\User;
use App\Models\WhatsappLog;
use App\Services\Contracts\WhatsappServiceInterface;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPaymentWhatsappNotification implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;

    public $invoiceIds;

    public $customMessageTemplate;

    /**
     * Tentukan berapa kali job ini di-retry jika gagal.
     */
    public $tries = 3;

    /**
     * Tentukan jeda waktu antar retry (dalam detik) jika terkena rate limit atau gagal.
     */
    public $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct($userId, array $invoiceIds, string $customMessageTemplate)
    {
        $this->userId = $userId;
        $this->invoiceIds = $invoiceIds;
        $this->customMessageTemplate = $customMessageTemplate;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsappServiceInterface $whatsappService): void
    {
        // Jika batch telah dibatalkan dari luar, stop eksekusi.
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        $user = User::with(['students.invoices' => function ($query) {
            $query->whereIn('id', $this->invoiceIds);
        }, 'students.invoices.feeType'])->find($this->userId);

        if (! $user || ! $user->phone) {
            $this->logFailed($user && $user->phone ? $user->phone : 'unknown', 'User tidak ditemukan atau nomor HP kosong.');

            return;
        }

        // --- GROUPING LOGIC ---
        // Menggabungkan seluruh tagihan milik semua anak ke dalam 1 pesan WA saja.
        $studentDetails = '';
        $totalAll = 0;
        $hasInvoices = false;
        $invoiceDetails = [];
        $feeName = 'Tagihan';

        foreach ($user->students as $student) {
            $invoices = $student->invoices;
            if ($invoices->isEmpty()) {
                continue;
            }

            $hasInvoices = true;
            $studentDetails .= "\n🎓 *".$student->name."*\n";
            $subTotal = 0;

            foreach ($invoices as $invoice) {
                $feeName = $invoice->feeType->name; // Ambil dari invoice pertama
                $subTotal += $invoice->amount;
                $invoiceDetails[] = [
                    'id' => $invoice->id,
                    'student' => $student->name,
                    'fee' => $invoice->feeType->name,
                    'amount' => $invoice->amount,
                ];
                $studentDetails .= '- '.$invoice->feeType->name.' (Rp '.number_format($invoice->amount, 0, ',', '.').")\n";
            }
            $totalAll += $subTotal;
        }

        if (! $hasInvoices) {
            $this->logFailed($user->phone, 'Tagihan tidak ditemukan berdasarkan ID yang diberikan.');

            return;
        }

        $formattedTotal = 'Rp '.number_format($totalAll, 0, ',', '.');

        $firstInvoice = $user->students->first()->invoices->first();
        $monthNumber = $firstInvoice ? $firstInvoice->period_month : null;
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $monthName = $monthNumber ? ($months[$monthNumber] ?? '') : '';

        // Memasukkan data ke dalam template dinamis
        $message = str_replace(
            ['{fee_name}', '{student_details}', '{total_amount}', '{month_name}'],
            [$feeName, trim($studentDetails), $formattedTotal, $monthName],
            $this->customMessageTemplate
        );

        // --- MENGIRIM PESAN MELALUI SERVICE (Gateway) ---
        $response = $whatsappService->sendMessage($user->phone, $message);

        // --- MENCATAT HASIL PENGIRIMAN KE TABEL WHATSAPP_LOGS ---
        $status = $response['success'] ? 'sent' : 'failed';
        $errorMessage = $response['success'] ? null : ($response['error'] ?? 'Unknown Gateway Error');

        WhatsappLog::create([
            'user_id' => $this->userId,
            'fee_type_id' => $firstInvoice ? $firstInvoice->fee_type_id : null,
            'batch_id' => $this->batchId, // Mengambil ID dari Job Batch
            'phone' => $response['target'] ?? $user->phone,
            'status' => $status,
            'payload' => $invoiceDetails,
            'error_message' => $errorMessage,
        ]);

        // Jika gateway menolak/gagal, lempar Exception agar Laravel me-retry job ini (berdasarkan $tries & $backoff)
        if (! $response['success']) {
            throw new \Exception('WhatsApp Gateway Error: '.$errorMessage);
        }
    }

    private function logFailed(string $phone, string $reason)
    {
        $feeTypeId = Invoice::whereIn('id', $this->invoiceIds)->value('fee_type_id');

        WhatsappLog::create([
            'user_id' => $this->userId,
            'fee_type_id' => $feeTypeId,
            'batch_id' => $this->batchId ?? null,
            'phone' => $phone,
            'status' => 'failed',
            'payload' => $this->invoiceIds,
            'error_message' => $reason,
        ]);
    }
}
