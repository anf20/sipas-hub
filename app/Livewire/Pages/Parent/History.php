<?php

namespace App\Livewire\Pages\Parent;

use App\Models\FeeType;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.parent')]
class History extends Component
{
    public $selectedCategory = '';

    public $selectedMonth = '';

    public $selectedYear = '';

    public function render()
    {
        $user = Auth::user();
        $studentIds = $user->students()->pluck('id');

        $query = Invoice::with(['student', 'feeType'])
            ->whereIn('student_id', $studentIds)
            ->where('status', 'paid');

        if ($this->selectedCategory) {
            $query->whereHas('feeType', function ($q) {
                $q->where('category', $this->selectedCategory);
            });
        }

        if ($this->selectedMonth) {
            $query->whereMonth('updated_at', $this->selectedMonth);
        }

        if ($this->selectedYear) {
            $query->whereYear('updated_at', $this->selectedYear);
        }

        $paidInvoices = $query->orderBy('updated_at', 'desc')->get();

        // Get unique categories dynamically
        $categories = FeeType::select('category')->distinct()->pluck('category')->filter();

        // Get unique years from successful payments (in PHP to support both SQLite and MySQL)
        $years = Invoice::whereIn('student_id', $studentIds)
            ->where('status', 'paid')
            ->pluck('updated_at')
            ->map(fn($date) => $date->year)
            ->unique()
            ->sortDesc()
            ->values();

        if ($years->isEmpty()) {
            $years = collect([now()->year, now()->year - 1]);
        }

        return view('livewire.pages.parent.history', [
            'paidInvoices' => $paidInvoices,
            'categories' => $categories,
            'years' => $years,
        ])->title(__('Riwayat Pembayaran'));
    }
}
