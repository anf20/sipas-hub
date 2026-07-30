<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

it('can access the dashboard as admin', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('Super Admin');

    actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Dashboard');
});

it('can access the students index page', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('Super Admin');

    actingAs($user)
        ->get(route('academic.students.index'))
        ->assertOk();
});

it('can access the classes index page', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('Super Admin');

    actingAs($user)
        ->get(route('academic.classes.index'))
        ->assertOk();
});

it('can access the fee types index page', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('Super Admin');

    actingAs($user)
        ->get(route('finance.fee-types.index'))
        ->assertOk();
});

it('displays the correct sidebar navigation groups for admin', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('Super Admin');

    actingAs($user)
        ->get(route('dashboard'))
        ->assertSee('Platform')
        ->assertSee('Akademik')
        ->assertSee('Keuangan')
        ->assertSee('Manajemen Pengguna')
        ->assertDontSee('Portal Orang Tua');
});

it('displays the correct navigation for parent', function () {
    $user = User::factory()->create(['name' => 'Test Parent', 'email_verified_at' => now()]);
    $user->assignRole('Orang Tua');

    actingAs($user)
        ->get(route('parent.dashboard'))
        ->assertSee('SIPAS-Hub')
        ->assertSee('Dashboard')
        ->assertSee('Tagihan')
        ->assertSee('Riwayat')
        ->assertSee('Siswa')
        ->assertSee('Halo, Test Parent');
});
