<?php

namespace App\Livewire\Pages\Finance;

use App\Jobs\GenerateInvoices;
use App\Models\FeeType;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FeeTypeCreate extends Component
{
    public $name = '';

    public $category = 'kegiatan';

    public $default_amount = 0;

    public $is_recurring = 'sekali';

    public $month;

    public $year;

    public $due_date;

    public $target_type = 'all';

    public $target_grade;

    public $target_class;

    public $target_students = [];

    public $preview_students = [];

    public function mount()
    {
        $this->month = (int) date('n');
        $this->year = (int) date('Y');
        $this->due_date = date('Y-m-d', strtotime('+10 days'));
        $this->updatePreview();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['target_type', 'target_grade', 'target_class', 'target_students'])) {
            $this->updatePreview();
        }
    }

    public function updatePreview()
    {
        $query = Student::where('status', 'aktif');

        if ($this->target_type === 'grade' && $this->target_grade) {
            $query->whereHas('schoolClass', function ($q) {
                $q->where('grade', $this->target_grade);
            });
        } elseif ($this->target_type === 'class' && $this->target_class) {
            $query->where('school_class_id', $this->target_class);
        } elseif ($this->target_type === 'student' && ! empty($this->target_students)) {
            $query->whereIn('id', $this->target_students);
        }

        $this->preview_students = $query->with('schoolClass')->get();
    }

    public function save()
    {
        Log::info('Save method triggered', [
            'name' => $this->name,
            'category' => $this->category,
            'amount' => $this->default_amount,
            'target' => $this->target_type,
        ]);

        try {
            $this->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|in:kegiatan,seragam,lain',
                'default_amount' => 'required|numeric|min:0',
                'is_recurring' => 'required|in:sekali,rutin',
                'month' => 'required_if:is_recurring,rutin',
                'year' => 'required|integer',
                'due_date' => 'required|date',
                'target_type' => 'required|in:all,grade,class,student',
                'target_grade' => 'required_if:target_type,grade',
                'target_class' => 'required_if:target_type,class',
                'target_students' => 'required_if:target_type,student|array',
            ]);

            // 1. Create the billing event record
            $feeType = FeeType::create([
                'name' => $this->name,
                'category' => $this->category,
                'default_amount' => $this->default_amount,
                'is_recurring' => $this->is_recurring === 'rutin',
                'recurrence' => $this->is_recurring === 'rutin' ? 'bulanan' : 'sekali',
                'applicable_grades' => $this->target_type === 'grade' ? [$this->target_grade] : null,
                'is_active' => true,
            ]);

            // 2. Dispatch background generation
            $targetValue = match ($this->target_type) {
                'grade' => $this->target_grade,
                'class' => $this->target_class,
                'student' => $this->target_students,
                default => null,
            };

            Log::info('Dispatching invoice generation (Sync)', [
                'fee_type_id' => $feeType->id,
                'month' => $this->is_recurring === 'rutin' ? (int) $this->month : (int) date('n'),
                'year' => (int) $this->year,
            ]);

            GenerateInvoices::dispatchSync(
                $feeType->id,
                $this->is_recurring === 'rutin' ? (int) $this->month : (int) date('n'),
                (int) $this->year,
                $this->due_date,
                ['type' => $this->target_type, 'value' => $targetValue],
                auth()->id()
            );

            \Flux::toast(__('Tagihan non-SPP berhasil dibuat dan invoice sedang diproses.'), variant: 'success');

            return redirect()->route('finance.fee-types.index');
        } catch (ValidationException $e) {
            Log::error('Validation failed', $e->errors());
            throw $e;
        } catch (\Exception $e) {
            Log::error('Save failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.pages.finance.fee-type-create', [
            'classes' => SchoolClass::with('academicYear')->orderBy('academic_year_id', 'desc')->orderBy('grade')->get(),
            'grades' => SchoolClass::select('grade')->distinct()->orderBy('grade')->pluck('grade'),
            'all_students' => Student::where('status', 'aktif')->with('schoolClass')->orderBy('name')->get(),
            'months' => [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ],
        ]);
    }
}
