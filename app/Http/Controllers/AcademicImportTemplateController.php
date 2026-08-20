<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AcademicImportTemplateController extends Controller
{
    public function download($type)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        if ($type === 'students') {
            $sheet->setTitle('Template Import Siswa');

            // Set Headers
            $headers = ['NIS (Kosongkan jika otomatis)', 'Nama Siswa (Wajib)', 'Jenis Kelamin (L/P)', 'Nama Wali (Opsional, akan buat/link akun)', 'Email Wali (Wajib jika ada Nama)', 'No HP Wali (Opsional)', 'Alamat (Opsional)', 'Tahun Masuk (Opsional, Default Tahun ini)'];
            $sheet->fromArray($headers, null, 'A1');

            // Example Row
            $sheet->fromArray(['', 'Ahmad Budi', 'L', 'Bapak Budi', 'budi@example.com', '08123456789', 'Jl. Merdeka No. 1', date('Y')], null, 'A2');

            $filename = 'Template_Import_Siswa.xlsx';
        } elseif ($type === 'classes') {
            $sheet->setTitle('Template Import Kelas');

            // Set Headers
            $headers = ['Nama Kelas (Wajib, cth: 7A)', 'Tingkat (Wajib, cth: 7)', 'Email Wali Kelas (Opsional, dari akun Guru yang ada)'];
            $sheet->fromArray($headers, null, 'A1');

            // Example Row
            $sheet->fromArray(['7A', '7', 'guru@example.com'], null, 'A2');

            $filename = 'Template_Import_Kelas.xlsx';
        } else {
            abort(404);
        }

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }
}
