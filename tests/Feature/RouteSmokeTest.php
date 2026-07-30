<?php

use App\Models\AcademicYear;
use App\Models\FeeType;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->user = User::factory()->create(['email_verified_at' => now()]);
    $this->user->assignRole(['Super Admin', 'Orang Tua']);

    // Create some data to avoid 500s on index pages that might expect data
    $this->academicYear = AcademicYear::create([
        'name' => '2024/2025',
        'start_date' => '2024-07-01',
        'end_date' => '2025-06-30',
        'is_active' => true,
    ]);

    $this->schoolClass = SchoolClass::create([
        'name' => 'X-IPA-1',
        'grade' => '10',
        'academic_year_id' => $this->academicYear->id,
    ]);

    $this->student = Student::create([
        'nis' => '12345',
        'name' => 'Test Student',
        'school_class_id' => $this->schoolClass->id,
        'gender' => 'L',
        'entry_year' => '2024',
        'status' => 'aktif',
    ]);

    $this->feeType = FeeType::create([
        'name' => 'SPP',
        'category' => 'SPP',
        'default_amount' => 500000,
        'is_recurring' => true,
        'recurrence' => 'bulanan',
        'is_active' => true,
    ]);
});

$routes = [
    'home' => '/',
    'dashboard' => '/dashboard',
    'academic.classes.index' => '/academic/classes',
    'academic.classes.create' => '/academic/classes/create',
    'academic.students.index' => '/academic/students',
    'academic.students.create' => '/academic/students/create',
    'academic.years.index' => '/academic/years',
    'academic.years.create' => '/academic/years/create',
    'finance.fee-types.index' => '/finance/fee-types',
    'finance.fee-types.create' => '/finance/fee-types/create',
    'management.users.index' => '/management/users',
    'management.users.create' => '/management/users/create',
    'parent.dashboard' => '/parent/dashboard',
    'parent.history' => '/parent/history',
    'parent.invoices' => '/parent/invoices',
    'parent.students' => '/parent/students',
];

foreach ($routes as $name => $path) {
    it("can access $name ($path)", function () use ($name, $path) {
        $response = actingAs($this->user)->get($path);

        // These routes are now redirects to Hubs or Login
        $redirectRoutes = [
            'home',
            'dashboard',
        ];

        if (in_array($name, $redirectRoutes)) {
            $response->assertRedirect();
        } else {
            $response->assertOk();
        }
    });
}

it('can access academic.classes.edit', function () {
    actingAs($this->user)
        ->get(route('academic.classes.edit', $this->schoolClass))
        ->assertOk();
});

it('can access academic.students.edit', function () {
    actingAs($this->user)
        ->get(route('academic.students.edit', $this->student))
        ->assertOk();
});

it('can access academic.years.edit', function () {
    actingAs($this->user)
        ->get(route('academic.years.edit', $this->academicYear))
        ->assertOk();
});

it('can access finance.fee-types.edit', function () {
    actingAs($this->user)
        ->get(route('finance.fee-types.edit', $this->feeType))
        ->assertOk();
});

it('can access management.users.edit', function () {
    actingAs($this->user)
        ->get(route('management.users.edit', $this->user))
        ->assertOk();
});
