<?php

namespace App\Livewire\Pages\Parent;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.parent')]
class Help extends Component
{
    public function render()
    {
        return view('livewire.pages.parent.help')->title(__('Pusat Bantuan'));
    }
}
