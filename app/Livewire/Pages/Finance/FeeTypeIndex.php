<?php

namespace App\Livewire\Pages\Finance;

use App\Models\FeeType;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FeeTypeIndex extends Component
{
    public function toggleStatus($id)
    {
        $feeType = FeeType::findOrFail($id);
        $feeType->update(['is_active' => ! $feeType->is_active]);

        \Flux::toast(__('Status jenis tagihan diperbarui.'), variant: 'success');
    }

    public function delete($id)
    {
        $feeType = FeeType::findOrFail($id);

        // In the future, check if there are invoices linked to this fee type
        // if ($feeType->invoices()->exists()) { ... }

        $feeType->delete();
        \Flux::toast(__('Jenis tagihan berhasil dihapus.'), variant: 'success');
    }

    public function render()
    {
        return view('livewire.pages.finance.fee-type-index', [
            'feeTypes' => FeeType::latest()->get(),
        ]);
    }
}
