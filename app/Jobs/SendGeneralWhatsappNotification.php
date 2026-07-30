<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\WhatsappLog;
use App\Services\Contracts\WhatsappServiceInterface;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendGeneralWhatsappNotification implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $userId;

    public string $messageText;

    /**
     * Tentukan berapa kali job ini di-retry jika gagal.
     */
    public $tries = 3;

    /**
     * Tentukan jeda waktu antar retry (dalam detik).
     */
    public $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $messageText)
    {
        $this->userId = $userId;
        $this->messageText = $messageText;
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

        $user = User::find($this->userId, ['*']);

        if (! $user || ! $user->phone) {
            $this->logFailed($user && $user->phone ? $user->phone : 'unknown', 'User tidak ditemukan atau nomor HP kosong.');

            return;
        }

        // --- MENGIRIM PESAN MELALUI SERVICE (Gateway) ---
        $response = $whatsappService->sendMessage($user->phone, $this->messageText);

        // --- MENCATAT HASIL PENGIRIMAN KE TABEL WHATSAPP_LOGS ---
        $status = $response['success'] ? 'sent' : 'failed';
        $errorMessage = $response['success'] ? null : ($response['error'] ?? 'Unknown Gateway Error');

        WhatsappLog::create([
            'user_id' => $this->userId,
            'fee_type_id' => null,
            'batch_id' => $this->batchId, // Mengambil ID dari Job Batch jika ada
            'phone' => $response['target'] ?? $user->phone,
            'status' => $status,
            'payload' => ['message' => $this->messageText],
            'error_message' => $errorMessage,
        ]);

        // Jika gateway menolak/gagal, lempar Exception agar Laravel me-retry job ini (berdasarkan $tries & $backoff)
        if (! $response['success']) {
            throw new \Exception('WhatsApp Gateway Error: '.$errorMessage);
        }
    }

    private function logFailed(string $phone, string $reason)
    {
        WhatsappLog::create([
            'user_id' => $this->userId,
            'fee_type_id' => null,
            'batch_id' => $this->batchId ?? null,
            'phone' => $phone,
            'status' => 'failed',
            'payload' => ['message' => $this->messageText],
            'error_message' => $reason,
        ]);
    }
}
