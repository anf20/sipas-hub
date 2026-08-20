<?php

namespace App\Livewire\Pages\Finance;

use App\Jobs\SendGeneralWhatsappNotification;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PaymentVerification extends Component
{
    use WithPagination;

    public $search = '';

    public $selectedPaymentId = null;

    public $rejectionReason = '';

    public $showRejectionModal = false;

    public $showProofModal = false;

    public $proofFileUrl = null;

    protected $queryString = ['search' => ['except' => '']];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewProof($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $this->proofFileUrl = asset('storage/'.$payment->proof_file);
        $this->showProofModal = true;
    }

    public function approve($paymentId)
    {
        try {
            DB::beginTransaction();

            $payment = Payment::where('id', $paymentId)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->firstOrFail();

            $invoice = Invoice::where('id', $payment->invoice_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Generate official receipt number (SCH-YYYYMM-XXXX)
            $prefix = 'SCH-'.date('Ym').'-';
            $lastPayment = Payment::where('receipt_number', 'like', $prefix.'%')
                ->where('status', 'success')
                ->orderBy('receipt_number', 'desc')
                ->first();

            $sequence = 1;
            if ($lastPayment) {
                $lastSequence = (int) substr($lastPayment->receipt_number, -4);
                $sequence = $lastSequence + 1;
            }
            $receiptNumber = $prefix.str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // Update Payment
            $payment->update([
                'status' => 'success',
                'receipt_number' => $receiptNumber,
                'recorded_by' => auth()->id(),
            ]);

            // Update Invoice
            $invoice->update([
                'status' => 'paid',
            ]);

            DB::commit();

            // Send WhatsApp Notification to Parent if exists
            $student = $invoice->student;
            $parent = $student ? $student->parent : null;
            if ($parent && $parent->phone) {
                $amountFormatted = number_format($payment->amount, 0, ',', '.');
                $className = $student->schoolClass ? $student->schoolClass->name : 'N/A';
                $messageText = "Halo Bapak/Ibu {$parent->name},\n\n".
                               "Pembayaran transfer manual sebesar *Rp {$amountFormatted}* untuk anak Anda *{$student->name}* (Kelas: {$className}) pada tagihan *{$invoice->billing_detail}* telah berhasil diverifikasi dan kini berstatus *LUNAS*.\n\n".
                               "Kwitansi resmi dapat diunduh di portal SIPAS-Hub Anda.\n\n".
                               'Terima kasih.';

                SendGeneralWhatsappNotification::dispatch($parent->id, $messageText);
            }

            \Flux::toast(__('Pembayaran berhasil disetujui.'), variant: 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment Approval Failed: '.$e->getMessage());
            \Flux::toast(__('Gagal menyetujui pembayaran. Silakan coba lagi.'), variant: 'danger');
        }
    }

    public function openRejectionModal($paymentId)
    {
        $this->selectedPaymentId = $paymentId;
        $this->rejectionReason = '';
        $this->showRejectionModal = true;
    }

    public function reject()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:5|max:255',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::where('id', $this->selectedPaymentId)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->firstOrFail();

            $invoice = Invoice::where('id', $payment->invoice_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Update Payment to rejected
            $payment->update([
                'status' => 'rejected',
                'recorded_by' => auth()->id(),
            ]);

            // Revert Invoice status back to unpaid
            $invoice->update([
                'status' => 'unpaid',
            ]);

            DB::commit();

            // Send WhatsApp Notification to Parent
            $student = $invoice->student;
            $parent = $student ? $student->parent : null;
            if ($parent && $parent->phone) {
                $amountFormatted = number_format($payment->amount, 0, ',', '.');
                $className = $student->schoolClass ? $student->schoolClass->name : 'N/A';
                $messageText = "Halo Bapak/Ibu {$parent->name},\n\n".
                               "Mohon maaf, verifikasi pembayaran transfer manual sebesar *Rp {$amountFormatted}* untuk anak Anda *{$student->name}* (Kelas: {$className}) pada tagihan *{$invoice->billing_detail}* ditolak oleh admin dengan alasan:\n".
                               "\"{$this->rejectionReason}\"\n\n".
                               'Silakan lakukan pembayaran ulang atau unggah bukti transfer yang valid melalui portal SIPAS-Hub. Terima kasih.';

                SendGeneralWhatsappNotification::dispatch($parent->id, $messageText);
            }

            $this->showRejectionModal = false;
            $this->selectedPaymentId = null;
            $this->rejectionReason = '';

            \Flux::toast(__('Pembayaran berhasil ditolak.'), variant: 'warning');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment Rejection Failed: '.$e->getMessage());
            \Flux::toast(__('Gagal menolak pembayaran. Silakan coba lagi.'), variant: 'danger');
        }
    }

    public function render()
    {
        $payments = Payment::with(['invoice.student.schoolClass', 'invoice.feeType'])
            ->where('status', 'pending')
            ->whereHas('invoice.student', function ($query) {
                if ($this->search) {
                    $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('nis', 'like', '%'.$this->search.'%');
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.pages.finance.payment-verification', [
            'payments' => $payments,
        ])->title(__('Verifikasi Pembayaran Manual'));
    }
}
