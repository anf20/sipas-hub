<?php

namespace App\Livewire\Pages\Parent;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.parent')]
class Invoices extends Component
{
    public $filter = 'all';

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function render()
    {
        $user = Auth::user();
        $students = $user->students()->with(['schoolClass'])->get();
        $studentIds = $students->pluck('id');

        $query = Invoice::with(['student', 'feeType'])
            ->whereIn('student_id', $studentIds);

        if ($this->filter === 'unpaid') {
            $query->where('status', 'unpaid');
        } elseif ($this->filter === 'paid') {
            $query->where('status', 'paid');
        }

        $invoices = $query->orderBy('due_date', 'desc')->get();

        $totalUnpaidBalance = Invoice::whereIn('student_id', $studentIds)
            ->where('status', 'unpaid')
            ->sum('amount');

        // Group invoices by student name
        $groupedInvoices = $invoices->groupBy(fn ($invoice) => $invoice->student->name);

        return view('livewire.pages.parent.invoices', [
            'groupedInvoices' => $groupedInvoices,
            'totalUnpaidBalance' => $totalUnpaidBalance,
        ])->title(__('Tagihan Anak'));
    }
}
