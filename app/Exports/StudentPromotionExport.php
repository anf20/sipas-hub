<?php

namespace App\Exports;

use App\Exports\Sheets\ClassReferenceSheet;
use App\Exports\Sheets\StudentPromotionTemplateSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StudentPromotionExport implements WithMultipleSheets
{
    use Exportable;

    protected $schoolClassId;

    protected $academicYearId;

    public function __construct($schoolClassId, $academicYearId)
    {
        $this->schoolClassId = $schoolClassId;
        $this->academicYearId = $academicYearId;
    }

    public function sheets(): array
    {
        return [
            new StudentPromotionTemplateSheet($this->schoolClassId),
            new ClassReferenceSheet($this->academicYearId),
        ];
    }
}
