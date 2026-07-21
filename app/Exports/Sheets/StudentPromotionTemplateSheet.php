<?php

namespace App\Exports\Sheets;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class StudentPromotionTemplateSheet implements FromCollection, WithEvents, WithHeadings, WithMapping, WithTitle
{
    protected $schoolClassId;

    public function __construct($schoolClassId)
    {
        $this->schoolClassId = $schoolClassId;
    }

    public function collection()
    {
        return Student::where('school_class_id', $this->schoolClassId)
            ->with('schoolClass')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Siswa',
            'NIS',
            'Nama Siswa',
            'Kelas Saat Ini',
            'ID Kelas Tujuan',
        ];
    }

    public function map($student): array
    {
        return [
            $student->id,
            $student->nis,
            $student->name,
            $student->schoolClass->name ?? '-',
            '', // Kosong untuk diisi user
        ];
    }

    public function title(): string
    {
        return 'Template Kenaikan Kelas';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Hide ID Siswa column (A)
                $sheet->getColumnDimension('A')->setVisible(false);

                // Auto size other columns
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet->getColumnDimension('E')->setAutoSize(true);

                // Protect the sheet
                $sheet->getProtection()->setPassword('pestpay2026'); // Password proteksi
                $sheet->getProtection()->setSheet(true);

                // Unlock Column E so users can edit it
                $highestRow = $sheet->getHighestRow();
                $lastRow = $highestRow == 1 ? 1000 : $highestRow;

                $sheet->getStyle('E2:E'.$lastRow)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

                // Highlight the unprotected column to show it's editable
                $sheet->getStyle('E2:E'.$lastRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2EFDA'], // Light green
                    ],
                ]);
            },
        ];
    }
}
