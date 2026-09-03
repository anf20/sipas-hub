<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Executes the full academic initialization in a single DB transaction.
 * Creates Academic Year, Classes, Staff accounts, Student + Parent accounts,
 * and generates all invoices (12-month SPP + Non-SPP + Initial Arrears).
 */
class AcademicInitializationService
{
    /**
     * Execute the full initialization process.
     *
     * @param  array{
     *     academic_year_name: string,
     *     start_date: string,
     *     end_date: string,
     *     classes: Collection,
     *     staff: Collection,
     *     students: Collection,
     *     fee_types: Collection,
     * }  $data
     * @return array{academic_year_id: int, classes_count: int, staff_count: int, students_count: int, invoices_count: int}
     */
    public function execute(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Step 1: Create Academic Year & Classes
            $academicYear = $this->createAcademicYear($data);
            $classMap = $this->createClasses($data['classes'], $academicYear);

            // Step 2: Create Staff Accounts
            $staffCount = $this->createStaff($data['staff']);

            // Step 3: Create Students & Parent Accounts
            $students = $this->createStudents($data['students'], $classMap);

            // Step 4: Create Non-SPP Fee Types & Generate All Invoices
            $this->createFeeTypes($data['fee_types']);
            $invoicesCount = $this->generateAllInvoices($students, $academicYear, $data['fee_types']);

            return [
                'academic_year_id' => $academicYear->id,
                'classes_count' => $classMap->count(),
                'staff_count' => $staffCount,
                'students_count' => $students->count(),
                'invoices_count' => $invoicesCount,
            ];
        });
    }

    /**
     * Create or activate an Academic Year.
     */
    private function createAcademicYear(array $data): AcademicYear
    {
        // Deactivate all existing academic years
        AcademicYear::query()->update(['is_active' => false]);

        return AcademicYear::create([
            'name' => $data['academic_year_name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'is_active' => true,
        ]);
    }

    /**
     * Create School Classes for the academic year.
     *
     * @return Collection<string, SchoolClass> Map of class name => SchoolClass model
     */
    private function createClasses(Collection $classes, AcademicYear $academicYear): Collection
    {
        $classMap = collect();

        foreach ($classes as $row) {
            $schoolClass = SchoolClass::create([
                'name' => $row['name'],
                'grade' => $row['grade'],
                'academic_year_id' => $academicYear->id,
                'capacity' => $row['capacity'] ?? 30,
            ]);

            $classMap->put($row['name'], $schoolClass);
        }

        return $classMap;
    }

    /**
     * Create Staff user accounts (Admin/Asatidz).
     */
    private function createStaff(Collection $staff): int
    {
        $count = 0;

        foreach ($staff as $row) {
            $user = User::firstOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'phone' => $row['phone'] ?: null,
                    'password' => Hash::make('password123'),
                ]
            );

            if (! $user->hasRole($row['role'])) {
                $user->assignRole($row['role']);
            }

            $count++;
        }

        return $count;
    }

    /**
     * Create Students and their Parent (Wali) user accounts.
     *
     * @return Collection<int, Student> Created students
     */
    private function createStudents(Collection $students, Collection $classMap): Collection
    {
        $createdStudents = collect();

        foreach ($students as $row) {
            // Create or find parent user
            $parentUser = null;
            $identifier = $row['parent_email'] ?: $row['parent_phone'];

            if ($identifier) {
                $query = User::query();
                if ($row['parent_email']) {
                    $query->where('email', $row['parent_email']);
                } else {
                    $query->where('phone', $row['parent_phone']);
                }

                $parentUser = $query->first();

                if (! $parentUser) {
                    $parentUser = User::create([
                        'name' => $row['parent_name'],
                        'email' => $row['parent_email'] ?: $row['parent_phone'].'@wali.sipashub.id',
                        'phone' => $row['parent_phone'] ?: null,
                        'password' => Hash::make('password123'),
                    ]);
                }

                if (! $parentUser->hasRole('Orang Tua')) {
                    $parentUser->assignRole('Orang Tua');
                }
            }

            // Resolve class
            $schoolClass = $classMap->get($row['class_name']);

            $student = Student::create([
                'name' => $row['name'],
                'gender' => $row['gender'],
                'school_class_id' => $schoolClass->id,
                'parent_user_id' => $parentUser?->id,
                'entry_year' => date('Y'),
                'status' => 'aktif',
                'spp_amount' => $row['spp_amount'] > 0 ? $row['spp_amount'] : null,
                'initial_arrears' => $row['initial_arrears'] ?? 0,
            ]);

            $createdStudents->push($student);
        }

        return $createdStudents;
    }

    /**
     * Create Non-SPP FeeTypes (Daftar Ulang, Seragam, etc.).
     */
    private function createFeeTypes(Collection $feeTypes): void
    {
        foreach ($feeTypes as $row) {
            FeeType::firstOrCreate(
                ['name' => $row['name'], 'category' => $row['category']],
                [
                    'default_amount' => $row['amount'],
                    'is_recurring' => false,
                    'recurrence' => 'sekali',
                    'applicable_grades' => $row['target_grades'] === 'semua' ? null : json_decode($row['target_grades'], true),
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Generate all invoices: 12-month SPP + Non-SPP + Initial Arrears.
     */
    private function generateAllInvoices(Collection $students, AcademicYear $academicYear, Collection $feeTypesData): int
    {
        $count = 0;
        $generatedBy = auth()->id();

        // Ensure SPP FeeType exists
        $sppFeeType = FeeType::firstOrCreate(
            ['name' => 'SPP Bulanan', 'category' => 'SPP'],
            [
                'default_amount' => 0,
                'is_recurring' => true,
                'recurrence' => 'bulanan',
                'is_active' => true,
            ]
        );

        // Determine academic year start month (default July)
        $startDate = Carbon::parse($academicYear->start_date);
        $startMonth = $startDate->month;
        $startYear = $startDate->year;

        foreach ($students as $student) {
            // Generate 12-month SPP invoices
            $sppAmount = $student->spp_amount ?? $sppFeeType->default_amount;

            if ($sppAmount > 0) {
                for ($i = 0; $i < 12; $i++) {
                    $invoiceDate = Carbon::create($startYear, $startMonth, 1)->addMonths($i);

                    Invoice::create([
                        'student_id' => $student->id,
                        'fee_type_id' => $sppFeeType->id,
                        'amount' => $sppAmount,
                        'due_date' => $invoiceDate->copy()->day(10),
                        'period_month' => $invoiceDate->month,
                        'period_year' => $invoiceDate->year,
                        'status' => 'unpaid',
                        'generated_by' => $generatedBy,
                        'notes' => 'Auto-generated by Initialization Wizard',
                    ]);

                    $count++;
                }
            }

            // Generate Initial Arrears invoice
            if ($student->initial_arrears > 0) {
                $arrearsFeeType = FeeType::firstOrCreate(
                    ['name' => 'Tunggakan Awal', 'category' => 'lain'],
                    [
                        'default_amount' => 0,
                        'is_recurring' => false,
                        'recurrence' => 'sekali',
                        'is_active' => true,
                    ]
                );

                Invoice::create([
                    'student_id' => $student->id,
                    'fee_type_id' => $arrearsFeeType->id,
                    'amount' => $student->initial_arrears,
                    'due_date' => $startDate->copy()->day(10),
                    'period_month' => $startDate->month,
                    'period_year' => $startDate->year,
                    'status' => 'unpaid',
                    'generated_by' => $generatedBy,
                    'notes' => 'Tunggakan bawaan dari tahun ajaran sebelumnya',
                ]);

                $count++;
            }

            // Generate Non-SPP invoices (Daftar Ulang, Seragam, etc.)
            foreach ($feeTypesData as $feeData) {
                $feeType = FeeType::where('name', $feeData['name'])->where('category', $feeData['category'])->first();

                if (! $feeType) {
                    continue;
                }

                // Check grade targeting
                $targetGrades = $feeData['target_grades'] ?? 'semua';
                if ($targetGrades !== 'semua') {
                    $grades = is_array($targetGrades) ? $targetGrades : json_decode($targetGrades, true);
                    $studentGrade = $student->schoolClass?->grade;
                    if (is_array($grades) && ! in_array($studentGrade, $grades)) {
                        continue;
                    }
                }

                Invoice::create([
                    'student_id' => $student->id,
                    'fee_type_id' => $feeType->id,
                    'amount' => $feeData['amount'],
                    'due_date' => $startDate->copy()->day(10),
                    'period_month' => $startDate->month,
                    'period_year' => $startDate->year,
                    'status' => 'unpaid',
                    'generated_by' => $generatedBy,
                    'notes' => 'Auto-generated by Initialization Wizard',
                ]);

                $count++;
            }
        }

        return $count;
    }
}
