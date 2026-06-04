<?php

namespace App\Livewire\Pages\Parent;

use App\Models\Invoice;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.parent')]
class InvoiceDetail extends Component
{
    public Invoice $invoice;

    public string $paymentMethod = 'bca_va';

    public bool $showConfirmationModal = false;

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

    public function pay()
    {
        if ($this->invoice->status === 'paid') {
            return;
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
}
