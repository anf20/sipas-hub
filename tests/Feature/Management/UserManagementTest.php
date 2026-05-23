<?php

namespace Tests\Feature\Management;

use App\Livewire\Pages\Management\UserCreate;
use App\Livewire\Pages\Management\UserEdit;
use App\Livewire\Pages\Management\UserIndex;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can render user index page', function () {
    Role::create(['name' => 'Super Admin']);
    $admin = User::factory()->create(['email_verified_at' => now()]);
    $admin->assignRole('Super Admin');

    $this->actingAs($admin)
        ->get(route('management.users.index'))
        ->assertStatus(200);
});

it('can search users', function () {
    Role::create(['name' => 'Super Admin']);
    $admin = User::factory()->create(['name' => 'Admin User', 'email_verified_at' => now()]);
    $admin->assignRole('Super Admin');
    User::factory()->create(['name' => 'John Doe']);

    Livewire::actingAs($admin)
        ->test(UserIndex::class)
        ->set('search', 'John')
        ->assertSee('John Doe')
        ->assertDontSee('Admin User');
});

it('can create a user', function () {
    Role::create(['name' => 'Super Admin']);
    $admin = User::factory()->create(['email_verified_at' => now()]);
    $admin->assignRole('Super Admin');
    Role::create(['name' => 'Admin Keuangan']);

    Livewire::actingAs($admin)
        ->test(UserCreate::class)
        ->set('name', 'New User')
        ->set('email', 'new@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('selected_roles', ['Admin Keuangan'])
        ->call('save')
        ->assertRedirect(route('management.users.index'));

    $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    $user = User::where('email', 'new@example.com')->first();
    expect($user->hasRole('Admin Keuangan'))->toBeTrue();
});

it('can edit a user', function () {
    Role::create(['name' => 'Super Admin']);
    $admin = User::factory()->create(['email_verified_at' => now()]);
    $admin->assignRole('Super Admin');
    $user = User::factory()->create(['name' => 'Old Name']);
    Role::create(['name' => 'Admin Akademik']);

    Livewire::actingAs($admin)
        ->test(UserEdit::class, ['user' => $user])
        ->set('name', 'New Name')
        ->set('selected_roles', ['Admin Akademik'])
        ->call('save')
        ->assertRedirect(route('management.users.index'));

    expect($user->fresh()->name)->toBe('New Name');
    expect($user->fresh()->hasRole('Admin Akademik'))->toBeTrue();
});

it('can delete a user', function () {
    Role::create(['name' => 'Super Admin']);
    $admin = User::factory()->create(['email_verified_at' => now()]);
    $admin->assignRole('Super Admin');
    $user = User::factory()->create();

    Livewire::actingAs($admin)
        ->test(UserIndex::class)
        ->call('delete', $user->id);

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

it('cannot delete self', function () {
    Role::create(['name' => 'Super Admin']);
    $admin = User::factory()->create(['email_verified_at' => now()]);
    $admin->assignRole('Super Admin');

    Livewire::actingAs($admin)
        ->test(UserIndex::class)
        ->call('delete', $admin->id);

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});
