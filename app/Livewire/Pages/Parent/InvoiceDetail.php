<?php

namespace App\Livewire\Pages\Parent;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.parent')]
class InvoiceDetail extends Component
{
    use WithFileUploads;

    public Invoice $invoice;

    public string $paymentMethod = 'manual_transfer';

    public bool $showConfirmationModal = false;

    public bool $showManualTransferModal = false;

    public $proofFile;

    public function mount(Invoice $invoice)
    {
        // Ensure the invoice belongs to a student of the current parent
        $user = Auth::user();
        $studentIds = $user->students()->pluck('students.id')->toArray();

        if (! in_array($invoice->student_id, $studentIds)) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        $this->invoice = $invoice->load(['student', 'feeType', 'payments']);
    }

    public function initiatePayment()
    {
        if ($this->invoice->status === 'paid') {
            \Flux::toast(__('Tagihan ini sudah lunas.'), variant: 'warning');

            return;
        }

        $this->showConfirmationModal = true;
    }

    public function startPayment()
    {
        if ($this->invoice->status === 'paid') {
            \Flux::toast(__('Tagihan ini sudah lunas.'), variant: 'warning');

            return;
        }

        if ($this->paymentMethod === 'manual_transfer') {
            $this->showConfirmationModal = false;
            $this->showManualTransferModal = true;
        } else {
            $this->pay();
        }
    }

    public function pay()
    {
        if ($this->invoice->status === 'paid') {
            return;
        }

        if ($this->paymentMethod === 'manual_transfer') {
            $this->validate([
                'proofFile' => 'required|image|max:2048',
            ]);

            try {
                DB::beginTransaction();

                $invoice = Invoice::where('id', $this->invoice->id)
                    ->whereIn('status', ['unpaid', 'inactive'])
                    ->lockForUpdate()
                    ->firstOrFail();

                // Generate receipt number (SCH-PEND-YYYYMM-XXXX)
                $prefix = 'SCH-PEND-'.date('Ym').'-';
                $lastPayment = Payment::where('receipt_number', 'like', $prefix.'%')
                    ->orderBy('id', 'desc')
                    ->first();

                $sequence = 1;
                if ($lastPayment) {
                    $lastSequence = (int) substr($lastPayment->receipt_number, -4);
                    $sequence = $lastSequence + 1;
                }
                $receiptNumber = $prefix.str_pad($sequence, 4, '0', STR_PAD_LEFT);

                // Store file (with WebP compression)
                $path = $this->convertToWebp($this->proofFile);

                // Create Payment Record with pending status
                Payment::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $invoice->amount,
                    'method' => 'transfer',
                    'status' => 'pending',
                    'proof_file' => $path,
                    'paid_at' => now(),
                    'receipt_number' => $receiptNumber,
                ]);

                // Update Invoice
                $invoice->update([
                    'status' => 'pending',
                ]);

                DB::commit();

                // WhatsApp Redirect Message
                $adminNumber = config('services.whatsapp.admin_number', '6281234567890');
                $studentName = $invoice->student->name;
                $className = $invoice->student->schoolClass ? $invoice->student->schoolClass->name : 'N/A';
                $billingDetail = $invoice->billing_detail;
                $amountFormatted = number_format($invoice->amount, 0, ',', '.');

                $message = "Halo Admin Keuangan, saya telah melakukan transfer bank untuk pembayaran:\n".
                           "- Siswa: {$studentName}\n".
                           "- Kelas: {$className}\n".
                           "- Tagihan: {$billingDetail}\n".
                           "- Nominal: Rp {$amountFormatted}\n\n".
                           'Saya telah mengunggah bukti pembayaran di Portal SIPAS-Hub. Mohon bantuannya untuk memverifikasi transaksi ini. Terima kasih.';

                $waUrl = "https://wa.me/{$adminNumber}?text=".rawurlencode($message);

                $this->showConfirmationModal = false;
                $this->showManualTransferModal = false;
                $this->reset('proofFile');

                \Flux::toast(__('Bukti transfer berhasil diunggah. Menghubungi admin via WhatsApp...'), variant: 'success');

                return redirect()->away($waUrl);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Manual Transfer Detail Error: '.$e->getMessage());
                \Flux::toast(__('Gagal mengunggah bukti transfer. Silakan coba lagi.'), variant: 'danger');

                return;
            }
        }

        try {
            $midtransService = app(MidtransService::class);
            $snapToken = $midtransService->getSnapToken($this->invoice, $this->paymentMethod);

            $this->showConfirmationModal = false;
            $this->dispatch('show-snap-popup', snapToken: $snapToken);
        } catch (\Exception $e) {
            \Log::error('Midtrans Snap Error: '.$e->getMessage());
            \Flux::toast(__('Gagal memulai pembayaran. Silakan coba lagi nanti.'), variant: 'danger');
        }
    }

    public function render()
    {
        $midtransService = app(MidtransService::class);
        $serviceFee = $midtransService->calculateFee((float) $this->invoice->amount, $this->paymentMethod);
        $totalToPay = $this->invoice->amount + $serviceFee;

        return view('livewire.pages.parent.invoice-detail', [
            'serviceFee' => $serviceFee,
            'totalToPay' => $totalToPay,
        ])->title(__('Detail Tagihan'));
    }

    /**
     * Compress and convert uploaded image to WebP format.
     * Falls back to normal upload if GD extension is not available.
     */
    private function convertToWebp($uploadedFile): string
    {
        try {
            if (function_exists('imagewebp')) {
                $path = $uploadedFile->getRealPath();

                // Determine image type and create image resource
                $image = match (strtolower($uploadedFile->getClientOriginalExtension())) {
                    'jpg', 'jpeg' => @imagecreatefromjpeg($path),
                    'png' => @imagecreatefrompng($path),
                    'webp' => @imagecreatefromwebp($path),
                    'gif' => @imagecreatefromgif($path),
                    default => false,
                };

                if ($image !== false) {
                    // Preserve transparency for PNG
                    if (strtolower($uploadedFile->getClientOriginalExtension()) === 'png') {
                        imagealphablending($image, false);
                        imagesavealpha($image, true);
                    }

                    // Capture WebP output using output buffering
                    ob_start();
                    if (imagewebp($image, null, 75)) {
                        $webpData = ob_get_clean();
                        imagedestroy($image);

                        $filename = 'payment-proofs/'.uniqid().'.webp';
                        Storage::disk('public')->put($filename, $webpData);

                        return $filename;
                    }
                    ob_end_clean();
                    imagedestroy($image);
                }
            }
        } catch (\Exception $e) {
            \Log::error('WebP Compression Failed, falling back: '.$e->getMessage());
        }

        // Fallback to standard Laravel store
        return $uploadedFile->store('payment-proofs', 'public');
    }
}
