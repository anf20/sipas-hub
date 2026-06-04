<?php

use App\Livewire\Pages\Finance\FinanceHub;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
});

test('it can generate spp without carbon error', function () {
    Livewire::actingAs($this->user)
        ->test(FinanceHub::class)
        ->set('month', '5') // String value like from a form
        ->set('year', 2026)
        ->set('default_amount', 250000)
        ->set('due_date', '2026-05-30')
        ->call('generateSpp')
        ->assertHasNoErrors();
});
