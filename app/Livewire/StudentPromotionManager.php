<?php

namespace App\Livewire;

use App\Exports\StudentPromotionExport;
use App\Imports\StudentPromotionImport;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class StudentPromotionManager extends Component
{
    use WithFileUploads;

    public $file;

    public $errorLogs = [];

    public $successMessage = '';

    // For Download Template
    public $downloadClassId = '';

    public $downloadYearId = '';

    public function mount()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if ($activeYear) {
            $this->downloadYearId = $activeYear->id;
        }
    }

    public function downloadTemplate()
    {
        $this->validate([
            'downloadClassId' => 'required|exists:school_classes,id',
            'downloadYearId' => 'required|exists:academic_years,id',
        ], [
            'downloadClassId.required' => 'Pilih Kelas Asal terlebih dahulu.',
            'downloadYearId.required' => 'Pilih Tahun Ajaran Tujuan terlebih dahulu.',
        ]);

        return Excel::download(new StudentPromotionExport($this->downloadClassId, $this->downloadYearId), 'Template_Mutasi_Siswa.xlsx');
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes' => 'Format file harus berupa .xlsx atau .xls.',
        ]);

        $this->errorLogs = [];
        $this->successMessage = '';

        try {
            $import = new StudentPromotionImport;
            Excel::import($import, $this->file);

            $rows = $import->data;
            if (! $rows || $rows->isEmpty()) {
                throw new Exception('File Excel kosong atau format tidak sesuai.');
            }

            DB::transaction(function () use ($rows) {
                $affectedClassIds = [];

                foreach ($rows as $index => $row) {
                    $rowNum = $index + 2;

                    $studentId = $row['id_siswa'] ?? null;
                    $targetClassId = $row['id_kelas_tujuan'] ?? null;

                    if (empty($studentId) && empty($targetClassId)) {
                        continue;
                    }

                    if (empty($studentId) || empty($targetClassId)) {
                        throw new Exception("Baris {$rowNum}: ID Siswa atau ID Kelas Tujuan kosong.");
                    }

                    $student = Student::with('schoolClass')->find($studentId);
                    if (! $student) {
                        throw new Exception("Baris {$rowNum}: Siswa dengan ID {$studentId} tidak ditemukan.");
                    }

                    $targetClass = SchoolClass::find($targetClassId);
                    if (! $targetClass) {
                        throw new Exception("Baris {$rowNum}: Kelas tujuan dengan ID {$targetClassId} tidak ditemukan.");
                    }

                    $affectedClassIds[$targetClassId] = true;

                    $oldClassId = $student->school_class_id;
                    $oldAcademicYearId = $student->schoolClass ? $student->schoolClass->academic_year_id : null;

                    if ($oldClassId == $targetClassId) {
                        continue;
                    }

                    if ($oldClassId && $oldAcademicYearId) {
                        DB::table('student_class_history')->insert([
                            'student_id' => $student->id,
                            'school_class_id' => $oldClassId,
                            'academic_year_id' => $oldAcademicYearId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    $student->school_class_id = $targetClassId;
                    $student->save();
                }

                foreach (array_keys($affectedClassIds) as $classId) {
                    $class = SchoolClass::find($classId);

                    $currentTotal = Student::where('school_class_id', $classId)->count();

                    if ($class->capacity && $currentTotal > $class->capacity) {
                        throw new Exception("Kapasitas Kelas '{$class->name}' terlampaui. Kapasitas: {$class->capacity}, Total siswa setelah mutasi: {$currentTotal}.");
                    }
                }
            });

            $this->successMessage = 'Proses mutasi kelas berhasil diselesaikan dan riwayat tercatat dengan aman!';
            $this->reset('file');

        } catch (Exception $e) {
            $this->errorLogs[] = $e->getMessage();
            $this->reset('file');
        }
    }

    public function render()
    {
        return view('livewire.student-promotion-manager', [
            'classes' => SchoolClass::orderBy('name')->get(),
            'years' => AcademicYear::orderBy('name', 'desc')->get(),
        ]);
    }
}
