<?php

namespace Tests\Feature\Finance;

use App\Livewire\Pages\Finance\WhatsappBroadcastGeneral;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Admin Keuangan']);
    Role::firstOrCreate(['name' => 'Orang Tua']);
});

it('renders the whatsapp broadcast page for admin keuangan', function () {
    $admin = User::factory()->create(['email_verified_at' => now()]);
    $admin->assignRole('Admin Keuangan');

    $this->actingAs($admin)
        ->get(route('finance.whatsapp-broadcast.general'))
        ->assertStatus(200);
});

it('prevents non-financial-admins from accessing the broadcast page', function () {
    $parent = User::factory()->create(['email_verified_at' => now()]);
    $parent->assignRole('Orang Tua');

    $this->actingAs($parent)
        ->get(route('finance.whatsapp-broadcast.general'))
        ->assertStatus(403);
});

it('dispatches general whatsapp broadcast job for all parents', function () {
    Bus::fake();

    $admin = User::factory()->create(['email_verified_at' => now()]);
    $admin->assignRole('Admin Keuangan');

    // Create a parent and student
    $parent = User::factory()->create(['phone' => '628123456789']);
    $parent->assignRole('Orang Tua');

    Student::factory()->create([
        'parent_user_id' => $parent->id,
    ]);

    Livewire::actingAs($admin)
        ->test(WhatsappBroadcastGeneral::class)
        ->set('messageText', 'Pengumuman Penting: Besok Libur Pondok!')
        ->set('target', 'all')
        ->call('startBroadcast')
        ->assertHasNoErrors();

    Bus::assertBatched(function ($batch) use ($parent) {
        return $batch->name === 'Whatsapp General Broadcast' &&
               $batch->jobs->count() === 1 &&
               $batch->jobs->first()->userId === $parent->id &&
               $batch->jobs->first()->messageText === 'Pengumuman Penting: Besok Libur Pondok!';
    });
});

it('dispatches general whatsapp broadcast job for class specific parents', function () {
    Bus::fake();

    $admin = User::factory()->create(['email_verified_at' => now()]);
    $admin->assignRole('Admin Keuangan');

    $classA = SchoolClass::factory()->create();

    // Parent A (student in Class A)
    $parentA = User::factory()->create(['phone' => '62811111111']);
    $parentA->assignRole('Orang Tua');
    Student::factory()->create([
        'parent_user_id' => $parentA->id,
        'school_class_id' => $classA->id,
    ]);

    // Parent B (student in Class B)
    $parentB = User::factory()->create(['phone' => '62822222222']);
    $parentB->assignRole('Orang Tua');
    Student::factory()->create([
        'parent_user_id' => $parentB->id,
    ]);

    Livewire::actingAs($admin)
        ->test(WhatsappBroadcastGeneral::class)
        ->set('messageText', 'Pengumuman Penting: Hanya Kelas A Libur!')
        ->set('target', 'class')
        ->set('classId', $classA->id)
        ->call('startBroadcast')
        ->assertHasNoErrors();

    Bus::assertBatched(function ($batch) use ($parentA) {
        return $batch->name === 'Whatsapp General Broadcast' &&
               $batch->jobs->count() === 1 &&
               $batch->jobs->first()->userId === $parentA->id &&
               $batch->jobs->first()->messageText === 'Pengumuman Penting: Hanya Kelas A Libur!';
    });
});
