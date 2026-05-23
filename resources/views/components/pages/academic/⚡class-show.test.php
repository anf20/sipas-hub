<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('pages.academic.class-show')
        ->assertStatus(200);
});
