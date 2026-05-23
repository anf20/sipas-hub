<?php

namespace App\Livewire\Pages\Parent;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.parent')]
class History extends Component
{
    public function render()
    {
        $user = Auth::user();
        $studentIds = $user->students()->pluck('id');

        $paidInvoices = Invoice::with(['student', 'feeType'])
            ->whereIn('student_id', $studentIds)
            ->where('status', 'paid')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('livewire.pages.parent.history', [
            'paidInvoices' => $paidInvoices,
        ])->title(__('Riwayat Pembayaran'));
    }
}
