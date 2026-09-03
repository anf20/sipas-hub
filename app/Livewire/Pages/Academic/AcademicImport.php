<?php

namespace App\Livewire\Pages\Academic;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class AcademicImport extends Component
{
    use WithFileUploads;

    #[Url(as: 'type')]
    public $importType = 'students'; // 'students' or 'classes'

    public function mount()
    {
        if (auth()->user()->hasRole('Asatidz')) {
            abort(403, 'Akses tidak diizinkan untuk role Asatidz.');
        }
    }

    // For Student Import
    public $school_class_id;

    // For Class Import
    public $academic_year_id;

    public $file;

    public $previewData = [];

    public $isPreviewing = false;

    public $importErrors = [];

    public function rules()
    {
        $rules = [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'importType' => 'required|in:students,classes',
        ];

        if ($this->importType === 'students') {
            $rules['school_class_id'] = 'required|exists:school_classes,id';
        } else {
            $rules['academic_year_id'] = 'required|exists:academic_years,id';
        }

        return $rules;
    }

    public function updatedFile()
    {
        $this->reset(['previewData', 'isPreviewing', 'importErrors']);
    }

    public function updatedImportType()
    {
        $this->reset(['file', 'previewData', 'isPreviewing', 'importErrors', 'school_class_id', 'academic_year_id']);
    }

    public function processPreview()
    {
        $this->validate();

        try {
            $path = $this->file->getRealPath();
            $data = Excel::toArray(new \stdClass, $path);

            if (empty($data) || empty($data[0])) {
                throw new \Exception('File Excel kosong atau format tidak sesuai.');
            }

            $rows = $data[0];
            // Remove header
            array_shift($rows);

            $this->previewData = [];
            $this->importErrors = [];

            foreach ($rows as $index => $row) {
                // Skip empty rows
                if (! array_filter($row)) {
                    continue;
                }

                if ($this->importType === 'students') {
                    $this->previewData[] = [
                        '_id' => $index, // For frontend tracking
                        'nis' => $row[0] ?? '',
                        'name' => $row[1] ?? '',
                        'gender' => strtoupper($row[2] ?? 'L'),
                        'parent_name' => $row[3] ?? '',
                        'parent_email' => $row[4] ?? '',
                        'parent_phone' => $row[5] ?? '',
                        'address' => $row[6] ?? '',
                        'entry_year' => $row[7] ?? date('Y'),
                    ];
                } else {
                    $this->previewData[] = [
                        '_id' => $index,
                        'name' => $row[0] ?? '',
                        'level' => $row[1] ?? '',
                        'homeroom_email' => $row[2] ?? '',
                    ];
                }
            }

            $this->isPreviewing = true;

        } catch (\Exception $e) {
            \Flux::toast('Gagal membaca file: '.$e->getMessage(), variant: 'danger');
        }
    }

    public function importData()
    {
        if (empty($this->previewData)) {
            \Flux::toast('Tidak ada data untuk diimpor.', variant: 'warning');

            return;
        }

        DB::beginTransaction();

        try {
            if ($this->importType === 'students') {
                $this->importStudents();
            } else {
                $this->importClasses();
            }

            DB::commit();

            \Flux::toast('Data berhasil diimpor.', variant: 'success');

            $this->reset(['file', 'previewData', 'isPreviewing', 'importErrors']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Flux::toast('Gagal melakukan impor: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function importStudents()
    {
        foreach ($this->previewData as $index => $row) {
            // Validation per row
            if (empty($row['name'])) {
                throw new \Exception('Baris '.($index + 1).': Nama Siswa wajib diisi.');
            }

            $parentUserId = null;

            // Handle Parent Creation/Linking
            if (! empty($row['parent_name']) && ! empty($row['parent_email'])) {
                $parent = User::firstOrCreate(
                    ['email' => $row['parent_email']],
                    [
                        'name' => $row['parent_name'],
                        'phone' => $row['parent_phone'] ?? null,
                        'password' => Hash::make('password123'),
                    ]
                );

                if (! $parent->hasRole('Orang Tua')) {
                    $parent->assignRole('Orang Tua');
                }

                $parentUserId = $parent->id;
            }

            Student::create([
                'nis' => $row['nis'] ?: null, // Boot will auto-generate if null
                'name' => $row['name'],
                'school_class_id' => $this->school_class_id,
                'gender' => in_array($row['gender'], ['L', 'P']) ? $row['gender'] : 'L',
                'parent_user_id' => $parentUserId,
                'address' => $row['address'] ?? null,
                'entry_year' => $row['entry_year'] ?: date('Y'),
                'status' => 'aktif',
            ]);
        }
    }

    private function importClasses()
    {
        foreach ($this->previewData as $index => $row) {
            if (empty($row['name']) || empty($row['level'])) {
                throw new \Exception('Baris '.($index + 1).': Nama Kelas dan Tingkat wajib diisi.');
            }

            $homeroomId = null;
            if (! empty($row['homeroom_email'])) {
                $teacher = User::where('email', $row['homeroom_email'])->first();
                if ($teacher) {
                    $homeroomId = $teacher->id;
                } else {
                    throw new \Exception('Baris '.($index + 1).": Guru dengan email {$row['homeroom_email']} tidak ditemukan.");
                }
            }

            SchoolClass::create([
                'academic_year_id' => $this->academic_year_id,
                'name' => $row['name'],
                'level' => $row['level'],
                'homeroom_id' => $homeroomId,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.pages.academic.academic-import', [
            'classes' => SchoolClass::with('academicYear')->whereHas('academicYear', function ($query) {
                $query->where('is_active', true);
            })->get(),
            'academicYears' => AcademicYear::orderBy('start_year', 'desc')->get(),
        ]);
    }
}
