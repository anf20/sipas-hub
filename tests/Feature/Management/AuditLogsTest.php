<?php

namespace Tests\Feature\Management;

use App\Livewire\Pages\Management\AuditLogs;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('renders the audit logs page successfully for a super admin', function () {
    Role::create(['name' => 'Super Admin']);
    $admin = User::factory()->create(['email_verified_at' => now()]);
    $admin->assignRole('Super Admin');

    $this->actingAs($admin)
        ->get(route('management.audit_logs'))
        ->assertStatus(200);
});

it('prevents non-super-admins from accessing the audit logs page', function () {
    Role::create(['name' => 'Orang Tua']);
    $parent = User::factory()->create(['email_verified_at' => now()]);
    $parent->assignRole('Orang Tua');

    $this->actingAs($parent)
        ->get(route('management.audit_logs'))
        ->assertStatus(403);
});

it('can search audit logs by IP, action, or target class name', function () {
    Role::create(['name' => 'Super Admin']);
    $admin = User::factory()->create(['email_verified_at' => now()]);
    $admin->assignRole('Super Admin');

    // Create some dummy audit logs
    AuditLog::create([
        'user_id' => $admin->id,
        'action' => 'created',
        'model_type' => 'App\Models\Invoice',
        'model_id' => 1,
        'old_values' => null,
        'new_values' => ['amount' => 250000],
        'ip' => '192.168.1.1',
    ]);

    AuditLog::create([
        'user_id' => $admin->id,
        'action' => 'deleted',
        'model_type' => 'App\Models\Payment',
        'model_id' => 5,
        'old_values' => ['amount' => 100000],
        'new_values' => null,
        'ip' => '10.0.0.1',
    ]);

    // Test search by IP
    Livewire::actingAs($admin)
        ->test(AuditLogs::class)
        ->set('search', '192.168.1.1')
        ->assertSee('Invoice')
        ->assertSee('CREATED')
        ->assertDontSee('Payment');

    // Test search by action
    Livewire::actingAs($admin)
        ->test(AuditLogs::class)
        ->set('actionFilter', 'deleted')
        ->assertSee('Payment')
        ->assertSee('DELETED')
        ->assertDontSee('Invoice');
});
