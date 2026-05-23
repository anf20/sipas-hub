<?php

namespace App\Livewire\Pages\Parent;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.parent')]
class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $students = $user->students()->with(['schoolClass'])->get();
        $studentIds = $students->pluck('id');

        $totalUnpaidBalance = Invoice::whereIn('student_id', $studentIds)
            ->where('status', 'unpaid')
            ->sum('amount');

        $upcomingInvoices = Invoice::with(['student', 'feeType'])
            ->whereIn('student_id', $studentIds)
            ->where('status', 'unpaid')
            ->orderBy('due_date', 'asc')
            ->take(3)
            ->get();

        return view('livewire.pages.parent.dashboard', [
            'user' => $user,
            'students' => $students,
            'totalUnpaidBalance' => $totalUnpaidBalance,
            'upcomingInvoices' => $upcomingInvoices,
        ])->title(__('Dashboard Orang Tua'));
    }
}
