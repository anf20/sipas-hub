<?php

use App\Livewire\Pages\Finance\FeeTypeCreate;
use App\Livewire\Pages\Finance\FeeTypeEdit;
use App\Livewire\Pages\Finance\FeeTypeIndex;
use App\Models\FeeType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can render fee type index (via redirect)', function () {
    FeeType::create([
        'name' => 'Test Fee',
        'category' => 'SPP',
        'default_amount' => 100000,
        'is_recurring' => true,
        'recurrence' => 'bulanan',
    ]);

    $this->actingAs($this->user)
        ->get(route('finance.fee-types.index'))
        ->assertRedirect();
});

it('can create a fee type', function () {
    Livewire::actingAs($this->user)
        ->test(FeeTypeCreate::class)
        ->set('name', 'New Fee Type')
        ->set('category', 'kegiatan')
        ->set('default_amount', 50000)
        ->set('is_recurring', 'sekali')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('finance.fee-types.index'));

    $this->assertDatabaseHas('fee_types', [
        'name' => 'New Fee Type',
        'category' => 'kegiatan',
        'default_amount' => 50000,
    ]);
});

it('can update a fee type', function () {
    $feeType = FeeType::create([
        'name' => 'Old Name',
        'category' => 'SPP',
        'default_amount' => 100000,
    ]);

    Livewire::actingAs($this->user)
        ->test(FeeTypeEdit::class, ['feeType' => $feeType])
        ->set('name', 'Updated Name')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('finance.hub', ['tab' => 'spp']));

    expect($feeType->fresh()->name)->toBe('Updated Name');
});

it('can delete a fee type', function () {
    $feeType = FeeType::create([
        'name' => 'To Be Deleted',
        'category' => 'SPP',
        'default_amount' => 100000,
    ]);

    Livewire::actingAs($this->user)
        ->test(FeeTypeIndex::class)
        ->call('delete', $feeType->id);

    $this->assertDatabaseMissing('fee_types', ['id' => $feeType->id]);
});
