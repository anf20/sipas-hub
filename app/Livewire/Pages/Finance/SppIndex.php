<?php

namespace App\Livewire\Pages\Finance;

use App\Jobs\GenerateInvoices;
use App\Models\FeeType;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SppIndex extends Component
{
    public $month;

    public $year;

    public $default_amount = 0;

    public $due_date;

    public function mount()
    {
        $this->month = (int) date('n');
        $this->year = (int) date('Y');
        $this->due_date = date('Y-m-d', strtotime('+10 days'));
    }

    public function generateSpp()
    {
        $this->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
            'default_amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
        ]);

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $monthName = $months[(int) $this->month] ?? 'Unknown';
        $name = "SPP Bulan {$monthName} {$this->year}";

        // 1. Create the billing event record
        $feeType = FeeType::create([
            'name' => $name,
            'category' => 'SPP',
            'default_amount' => $this->default_amount,
            'is_recurring' => true,
            'recurrence' => 'bulanan',
            'applicable_grades' => null, // all
            'is_active' => true,
        ]);

        // 2. Dispatch background generation synchronously
        Log::info('Dispatching SPP generation (Sync)', [
            'fee_type_id' => $feeType->id,
            'month' => (int) $this->month,
            'year' => (int) $this->year,
        ]);

        GenerateInvoices::dispatchSync(
            $feeType->id,
            (int) $this->month,
            (int) $this->year,
            $this->due_date,
            ['type' => 'all', 'value' => null],
            auth()->id()
        );

        \Flux::toast(__('Tagihan SPP berhasil digenerate untuk seluruh siswa aktif.'), variant: 'success');

        // Reset inputs after success
        $this->month = (int) date('n');
        $this->due_date = date('Y-m-d', strtotime('+10 days'));

        $this->dispatch('close-modal', 'generate-spp-modal');
    }

    public function delete($id)
    {
        $feeType = FeeType::findOrFail($id);
        $feeType->delete();
        \Flux::toast(__('Catatan SPP berhasil dihapus.'), variant: 'success');
    }

    public function render()
    {
        return view('livewire.pages.finance.spp-index', [
            'sppBatches' => FeeType::where('category', 'SPP')->latest()->get(),
            'months' => [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ],
        ]);
    }
}
