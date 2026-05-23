<?php

namespace App\Livewire\Pages\Finance;

use App\Models\FeeType;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FeeTypeEdit extends Component
{
    public FeeType $feeType;

    public $name;

    public $category;

    public $default_amount;

    public $is_active;

    public bool $isLocked = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'category' => 'required|in:SPP,kegiatan,seragam,lain',
        'default_amount' => 'required|numeric|min:0',
        'is_active' => 'boolean',
    ];

    public function mount(FeeType $feeType)
    {
        $this->feeType = $feeType;
        $this->name = $feeType->name;
        $this->category = $feeType->category;
        $this->default_amount = $feeType->default_amount;
        $this->is_active = (bool) $feeType->is_active;

        // Lock if there is at least one paid invoice OR if it's a batch with generated invoices
        $this->isLocked = $feeType->invoices()->where('status', 'paid')->exists();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'is_active' => $this->is_active,
        ];

        // Only allow editing category and amount if NOT locked
        if (! $this->isLocked) {
            $data['category'] = $this->category;
            $data['default_amount'] = $this->default_amount;
        }

        $this->feeType->update($data);

        \Flux::toast(__('Tagihan berhasil diperbarui.'), variant: 'success');

        return redirect()->route('finance.hub', ['tab' => $this->feeType->category === 'SPP' ? 'spp' : 'fees']);
    }

    public function render()
    {
        return view('livewire.pages.finance.fee-type-edit');
    }
}
