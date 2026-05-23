<?php

namespace App\Livewire\Pages\Parent;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.parent')]
class PaymentSuccess extends Component
{
    public Payment $payment;

    public function mount(Payment $payment)
    {
        // Ensure the payment belongs to one of the user's students
        $studentIds = Auth::user()->students->pluck('id')->toArray();
        
        if (!in_array($payment->invoice->student_id, $studentIds)) {
            abort(403);
        }

        $this->payment = $payment->load(['invoice.student', 'invoice.feeType']);
    }

    public function render()
    {
        return view('livewire.pages.parent.payment-success')->title(__('Pembayaran Berhasil'));
    }
}
