<?php

namespace App\Exports\Sheets;

use App\Models\SchoolClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class ClassReferenceSheet implements FromCollection, WithEvents, WithHeadings, WithMapping, WithTitle
{
    protected $academicYearId;

    public function __construct($academicYearId)
    {
        $this->academicYearId = $academicYearId;
    }

    public function collection()
    {
        return SchoolClass::where('academic_year_id', $this->academicYearId)->get();
    }

    public function headings(): array
    {
        return [
            'ID Kelas Tujuan',
            'Nama Kelas',
        ];
    }

    public function map($class): array
    {
        return [
            $class->id,
            $class->name,
        ];
    }

    public function title(): string
    {
        return 'Daftar Referensi ID Kelas';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);

                // Protect this sheet fully so users don't edit it
                $sheet->getProtection()->setPassword('pestpay2026');
                $sheet->getProtection()->setSheet(true);
            },
        ];
    }
}
