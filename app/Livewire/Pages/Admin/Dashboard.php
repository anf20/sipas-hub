<?php

namespace App\Livewire\Pages\Admin;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $totalStudents = Student::where('status', 'aktif')->count();

        $monthlyIncome = Payment::whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $totalUnpaid = Invoice::where('status', '!=', 'paid')
            ->sum('amount');

        $newInvoicesThisMonth = Invoice::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Income trend for the last 12 months (SQLite & MySQL compatible)
        $driver = DB::connection()->getDriverName();
        $monthSelector = $driver === 'sqlite'
            ? "strftime('%Y-%m', paid_at)"
            : "DATE_FORMAT(paid_at, '%Y-%m')";

        $incomeData = Payment::select(
            DB::raw('sum(amount) as total'),
            DB::raw("{$monthSelector} as month")
        )
            ->where('paid_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $chartLabels = [];
        $chartData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $label = $date->translatedFormat('M Y');
            $chartLabels[] = $label;

            $data = $incomeData->firstWhere('month', $monthKey);
            $chartData[] = $data ? (float) $data->total : 0;
        }

        $upcomingInvoices = Invoice::with(['student', 'feeType'])
            ->where('status', '!=', 'paid')
            ->where('due_date', '>=', now()->startOfDay())
            ->where('due_date', '<=', now()->addDays(7)->endOfDay())
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $recentPayments = Payment::with(['invoice.student', 'invoice.feeType'])
            ->latest('paid_at')
            ->limit(5)
            ->get();

        return view('livewire.pages.admin.dashboard', [
            'totalStudents' => $totalStudents,
            'monthlyIncome' => $monthlyIncome,
            'totalUnpaid' => $totalUnpaid,
            'newInvoicesThisMonth' => $newInvoicesThisMonth,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'upcomingInvoices' => $upcomingInvoices,
            'recentPayments' => $recentPayments,
        ])->title(__('Admin Dashboard'));
    }
}
