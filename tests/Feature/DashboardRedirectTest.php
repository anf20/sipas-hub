<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

it('redirects parents to parent dashboard from main dashboard route', function () {
    $parent = User::factory()->create();
    $parent->assignRole('Orang Tua');

    actingAs($parent)
        ->get(route('dashboard'))
        ->assertRedirect(route('parent.dashboard'));
});

it('shows normal dashboard to non-parents', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk();
});
