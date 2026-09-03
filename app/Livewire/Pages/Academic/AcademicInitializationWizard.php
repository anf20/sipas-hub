<?php

namespace App\Livewire\Pages\Academic;

use App\Services\AcademicInitializationService;
use App\Services\InitializationParserService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
class AcademicInitializationWizard extends Component
{
    use WithFileUploads;

    /** Current wizard step (1-5) */
    public int $currentStep = 1;

    // --- Step 1: Academic Year & Classes ---
    public string $academicYearName = '';

    public string $startDate = '';

    public string $endDate = '';

    /** @var array<int, array{name: string, grade: string, capacity: int}> */
    public array $classesValid = [];

    /** @var array<int, array{name: string, grade: string, capacity: int, _errors: array}> */
    public array $classesInvalid = [];

    // Manual entry form for Step 1
    public string $newClassName = '';

    public string $newClassGrade = '';

    public int $newClassCapacity = 30;

    // --- Step 2: Staff Accounts ---
    /** @var array<int, array{name: string, email: string, phone: string, role: string}> */
    public array $staffValid = [];

    /** @var array<int, array{name: string, email: string, phone: string, role: string, _errors: array}> */
    public array $staffInvalid = [];

    // Manual entry form for Step 2
    public string $newStaffName = '';

    public string $newStaffEmail = '';

    public string $newStaffPhone = '';

    public string $newStaffRole = 'Asatidz';

    // --- Step 3: Students, Parents & Billing ---
    /** @var array<int, array> */
    public array $studentsValid = [];

    /** @var array<int, array> */
    public array $studentsInvalid = [];

    // Manual entry form for Step 3
    public string $newStudentName = '';

    public string $newStudentGender = 'L';

    public string $newStudentClassName = '';

    public string $newParentName = '';

    public string $newParentPhone = '';

    public string $newParentEmail = '';

    public float $newSppAmount = 0;

    public float $newInitialArrears = 0;

    // --- Step 4: Non-SPP Fee Types ---
    /** @var array<int, array> */
    public array $feeTypesValid = [];

    /** @var array<int, array> */
    public array $feeTypesInvalid = [];

    // Manual entry form for Step 4
    public string $newFeeName = '';

    public string $newFeeCategory = 'lain';

    public float $newFeeAmount = 0;

    public string $newFeeTargetGrades = 'semua';

    // --- File uploads ---
    public $uploadFile;

    // --- Step 5: Summary result ---
    public array $submissionResult = [];

    public bool $isSubmitting = false;

    public function mount(): void
    {
        if (! auth()->user()->hasAnyRole(['Super Admin', 'Admin Akademik'])) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }

    // ============================================================
    // NAVIGATION
    // ============================================================

    public function nextStep(): void
    {
        if ($this->currentStep === 1) {
            if ($this->academicYearName === '' || $this->startDate === '' || $this->endDate === '') {
                \Flux::toast('Mohon lengkapi data Tahun Ajaran.', variant: 'danger');

                return;
            }
            if (empty($this->classesValid) && empty($this->classesInvalid)) {
                \Flux::toast('Mohon tambahkan minimal 1 kelas.', variant: 'danger');

                return;
            }
            if (! empty($this->classesInvalid)) {
                \Flux::toast('Masih ada data kelas yang perlu diperbaiki.', variant: 'warning');

                return;
            }
        }

        if ($this->currentStep === 3) {
            if (empty($this->studentsValid) && empty($this->studentsInvalid)) {
                \Flux::toast('Mohon tambahkan minimal 1 santri.', variant: 'danger');

                return;
            }
            if (! empty($this->studentsInvalid)) {
                \Flux::toast('Masih ada data santri yang perlu diperbaiki.', variant: 'warning');

                return;
            }
        }

        if ($this->currentStep < 5) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= 5 && $step <= $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    // ============================================================
    // STEP 1: CLASSES — Manual Entry
    // ============================================================

    public function addClassManually(): void
    {
        $parser = app(InitializationParserService::class);
        $result = $parser->validateClasses([
            ['name' => $this->newClassName, 'grade' => $this->newClassGrade, 'capacity' => (string) $this->newClassCapacity],
        ]);

        // Also check against existing valid classes for duplicates
        $existingNames = array_map(fn ($c) => strtolower($c['name']), $this->classesValid);
        if (in_array(strtolower($this->newClassName), $existingNames)) {
            \Flux::toast('Nama kelas sudah ada di daftar.', variant: 'danger');

            return;
        }

        if ($result['invalid']->isNotEmpty()) {
            $errors = $result['invalid']->first()['_errors'];
            \Flux::toast(implode(' ', $errors), variant: 'danger');

            return;
        }

        $this->classesValid[] = $result['valid']->first();
        $this->reset(['newClassName', 'newClassGrade']);
        $this->newClassCapacity = 30;
        \Flux::toast('Kelas berhasil ditambahkan.', variant: 'success');
    }

    public function removeClassValid(int $index): void
    {
        unset($this->classesValid[$index]);
        $this->classesValid = array_values($this->classesValid);
    }

    // ============================================================
    // STEP 2: STAFF — Manual Entry
    // ============================================================

    public function addStaffManually(): void
    {
        $parser = app(InitializationParserService::class);
        $result = $parser->validateStaff([
            ['name' => $this->newStaffName, 'email' => $this->newStaffEmail, 'phone' => $this->newStaffPhone, 'role' => $this->newStaffRole],
        ]);

        $existingEmails = array_map(fn ($s) => strtolower($s['email']), $this->staffValid);
        if (in_array(strtolower($this->newStaffEmail), $existingEmails)) {
            \Flux::toast('Email sudah ada di daftar.', variant: 'danger');

            return;
        }

        if ($result['invalid']->isNotEmpty()) {
            $errors = $result['invalid']->first()['_errors'];
            \Flux::toast(implode(' ', $errors), variant: 'danger');

            return;
        }

        $this->staffValid[] = $result['valid']->first();
        $this->reset(['newStaffName', 'newStaffEmail', 'newStaffPhone']);
        $this->newStaffRole = 'Asatidz';
        \Flux::toast('Akun pengelola berhasil ditambahkan.', variant: 'success');
    }

    public function removeStaffValid(int $index): void
    {
        unset($this->staffValid[$index]);
        $this->staffValid = array_values($this->staffValid);
    }

    // ============================================================
    // STEP 3: STUDENTS — Manual Entry
    // ============================================================

    public function addStudentManually(): void
    {
        $parser = app(InitializationParserService::class);
        $validClassNames = array_column($this->classesValid, 'name');

        $result = $parser->validateStudents([
            [
                'name' => $this->newStudentName,
                'gender' => $this->newStudentGender,
                'class_name' => $this->newStudentClassName,
                'parent_name' => $this->newParentName,
                'parent_phone' => $this->newParentPhone,
                'parent_email' => $this->newParentEmail,
                'spp_amount' => (string) $this->newSppAmount,
                'initial_arrears' => (string) $this->newInitialArrears,
            ],
        ], $validClassNames);

        if ($result['invalid']->isNotEmpty()) {
            $errors = $result['invalid']->first()['_errors'];
            \Flux::toast(implode(' ', $errors), variant: 'danger');

            return;
        }

        $this->studentsValid[] = $result['valid']->first();
        $this->reset(['newStudentName', 'newStudentGender', 'newStudentClassName', 'newParentName', 'newParentPhone', 'newParentEmail']);
        $this->newStudentGender = 'L';
        $this->newSppAmount = 0;
        $this->newInitialArrears = 0;
        \Flux::toast('Data santri berhasil ditambahkan.', variant: 'success');
    }

    public function removeStudentValid(int $index): void
    {
        unset($this->studentsValid[$index]);
        $this->studentsValid = array_values($this->studentsValid);
    }

    // ============================================================
    // STEP 4: FEE TYPES — Manual Entry
    // ============================================================

    public function addFeeTypeManually(): void
    {
        $parser = app(InitializationParserService::class);
        $result = $parser->validateFeeTypes([
            ['name' => $this->newFeeName, 'category' => $this->newFeeCategory, 'amount' => (string) $this->newFeeAmount, 'target_grades' => $this->newFeeTargetGrades],
        ]);

        if ($result['invalid']->isNotEmpty()) {
            $errors = $result['invalid']->first()['_errors'];
            \Flux::toast(implode(' ', $errors), variant: 'danger');

            return;
        }

        $this->feeTypesValid[] = $result['valid']->first();
        $this->reset(['newFeeName', 'newFeeCategory']);
        $this->newFeeCategory = 'lain';
        $this->newFeeAmount = 0;
        $this->newFeeTargetGrades = 'semua';
        \Flux::toast('Tagihan berhasil ditambahkan.', variant: 'success');
    }

    public function removeFeeTypeValid(int $index): void
    {
        unset($this->feeTypesValid[$index]);
        $this->feeTypesValid = array_values($this->feeTypesValid);
    }

    // ============================================================
    // EXCEL UPLOAD & PARSE (Universal for all steps)
    // ============================================================

    public function uploadAndParse(): void
    {
        $this->validate([
            'uploadFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $path = $this->uploadFile->getRealPath();
            $data = Excel::toArray(new \stdClass, $path);

            if (empty($data) || empty($data[0])) {
                throw new \Exception('File Excel kosong atau format tidak sesuai.');
            }

            $rows = $data[0];
            array_shift($rows); // Remove header

            $parser = app(InitializationParserService::class);

            $stepType = match ($this->currentStep) {
                1 => 'classes',
                2 => 'staff',
                3 => 'students',
                4 => 'fee_types',
                default => throw new \Exception('Step tidak valid untuk import.'),
            };

            $parsedRows = $parser->parseExcelRows($rows, $stepType);

            $result = match ($this->currentStep) {
                1 => $parser->validateClasses($parsedRows),
                2 => $parser->validateStaff($parsedRows),
                3 => $parser->validateStudents($parsedRows, array_column($this->classesValid, 'name')),
                4 => $parser->validateFeeTypes($parsedRows),
            };

            // Merge with existing data
            match ($this->currentStep) {
                1 => $this->mergeImportedData('classesValid', 'classesInvalid', $result),
                2 => $this->mergeImportedData('staffValid', 'staffInvalid', $result),
                3 => $this->mergeImportedData('studentsValid', 'studentsInvalid', $result),
                4 => $this->mergeImportedData('feeTypesValid', 'feeTypesInvalid', $result),
            };

            $validCount = $result['valid']->count();
            $invalidCount = $result['invalid']->count();

            $this->reset('uploadFile');

            \Flux::toast("Import selesai: {$validCount} valid, {$invalidCount} perlu diperbaiki.", variant: $invalidCount > 0 ? 'warning' : 'success');

        } catch (\Exception $e) {
            \Flux::toast('Gagal membaca file: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function mergeImportedData(string $validProp, string $invalidProp, array $result): void
    {
        foreach ($result['valid'] as $row) {
            $this->{$validProp}[] = $row instanceof Collection ? $row->toArray() : $row;
        }
        foreach ($result['invalid'] as $row) {
            $this->{$invalidProp}[] = $row instanceof Collection ? $row->toArray() : $row;
        }
    }

    // ============================================================
    // INLINE FIX: Edit invalid row and re-validate
    // ============================================================

    public function fixInvalidRow(int $index): void
    {
        $parser = app(InitializationParserService::class);

        match ($this->currentStep) {
            1 => $this->revalidateRow($parser, 'classesInvalid', 'classesValid', $index, 'classes'),
            2 => $this->revalidateRow($parser, 'staffInvalid', 'staffValid', $index, 'staff'),
            3 => $this->revalidateRow($parser, 'studentsInvalid', 'studentsValid', $index, 'students'),
            4 => $this->revalidateRow($parser, 'feeTypesInvalid', 'feeTypesValid', $index, 'fee_types'),
        };
    }

    private function revalidateRow(InitializationParserService $parser, string $invalidProp, string $validProp, int $index, string $type): void
    {
        if (! isset($this->{$invalidProp}[$index])) {
            return;
        }

        $row = $this->{$invalidProp}[$index];
        unset($row['_errors'], $row['_index']);

        $result = match ($type) {
            'classes' => $parser->validateClasses([$row]),
            'staff' => $parser->validateStaff([$row]),
            'students' => $parser->validateStudents([$row], array_column($this->classesValid, 'name')),
            'fee_types' => $parser->validateFeeTypes([$row]),
        };

        if ($result['valid']->isNotEmpty()) {
            // Move from invalid to valid
            $this->{$validProp}[] = $result['valid']->first();
            unset($this->{$invalidProp}[$index]);
            $this->{$invalidProp} = array_values($this->{$invalidProp});
            \Flux::toast('Data berhasil diperbaiki!', variant: 'success');
        } else {
            // Update error messages
            $this->{$invalidProp}[$index] = $result['invalid']->first();
            \Flux::toast('Data masih ada kesalahan.', variant: 'danger');
        }
    }

    public function removeInvalidRow(string $type, int $index): void
    {
        $prop = match ($type) {
            'classes' => 'classesInvalid',
            'staff' => 'staffInvalid',
            'students' => 'studentsInvalid',
            'fee_types' => 'feeTypesInvalid',
        };

        unset($this->{$prop}[$index]);
        $this->{$prop} = array_values($this->{$prop});
    }

    // ============================================================
    // TEMPLATE DOWNLOAD
    // ============================================================

    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $headers = match ($this->currentStep) {
            1 => ['Nama Kelas', 'Tingkat (grade)', 'Kapasitas'],
            2 => ['Nama Lengkap', 'Email', 'No. WhatsApp', 'Role (Super Admin/Admin Keuangan/Admin Akademik/Asatidz)'],
            3 => ['Nama Santri', 'Gender (L/P)', 'Nama Kelas', 'Nama Wali', 'No. WA Wali', 'Email Wali', 'Nominal SPP', 'Tunggakan Awal'],
            4 => ['Nama Tagihan', 'Kategori (kegiatan/seragam/lain)', 'Nominal', 'Sasaran Tingkat (semua / 7,8,9)'],
            default => [],
        };

        foreach ($headers as $col => $header) {
            $sheet->setCellValue([$col + 1, 1], $header);
            $sheet->getColumnDimensionByColumn($col + 1)->setAutoSize(true);
        }

        // Style header row
        $headerStyle = $sheet->getStyle([1, 1, count($headers), 1]);
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()->setFillType(Fill::FILL_SOLID);
        $headerStyle->getFill()->getStartColor()->setARGB('FFE2EFDA');

        $filename = match ($this->currentStep) {
            1 => 'template_kelas.xlsx',
            2 => 'template_pengelola.xlsx',
            3 => 'template_santri.xlsx',
            4 => 'template_tagihan_non_spp.xlsx',
            default => 'template.xlsx',
        };

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ============================================================
    // STEP 5: SUBMIT
    // ============================================================

    public function submitWizard(): void
    {
        $this->isSubmitting = true;

        try {
            $service = app(AcademicInitializationService::class);

            $this->submissionResult = $service->execute([
                'academic_year_name' => $this->academicYearName,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'classes' => collect($this->classesValid),
                'staff' => collect($this->staffValid),
                'students' => collect($this->studentsValid),
                'fee_types' => collect($this->feeTypesValid),
            ]);

            $this->currentStep = 5;
            \Flux::toast('Inisialisasi berhasil! Semua data telah dibuat.', variant: 'success');

        } catch (\Exception $e) {
            \Flux::toast('Gagal menginisialisasi: '.$e->getMessage(), variant: 'danger');
        } finally {
            $this->isSubmitting = false;
        }
    }

    // ============================================================
    // COMPUTED HELPERS
    // ============================================================

    public function getSummaryProperty(): array
    {
        $totalSpp = collect($this->studentsValid)->sum('spp_amount');
        $totalArrears = collect($this->studentsValid)->sum('initial_arrears');
        $totalNonSpp = collect($this->feeTypesValid)->sum('amount');

        return [
            'classes' => count($this->classesValid),
            'staff' => count($this->staffValid),
            'students' => count($this->studentsValid),
            'fee_types' => count($this->feeTypesValid),
            'total_spp_projection' => $totalSpp * 12,
            'total_arrears' => $totalArrears,
            'total_non_spp' => $totalNonSpp * count($this->studentsValid),
        ];
    }

    public function render()
    {
        return view('livewire.pages.academic.academic-initialization-wizard', [
            'summary' => $this->getSummaryProperty(),
            'availableClasses' => array_column($this->classesValid, 'name'),
        ]);
    }
}
