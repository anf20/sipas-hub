<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * Universal parser & validator for each initialization wizard step.
 * Validates both manual form entries and Excel-imported rows.
 *
 * Returns separated valid/invalid row collections with per-field error messages.
 */
class InitializationParserService
{
    /**
     * Validate Step 1 data: Academic Year classes.
     *
     * @param  array<int, array{name: string, grade: string, capacity: string}>  $rows
     * @return array{valid: Collection, invalid: Collection}
     */
    public function validateClasses(array $rows): array
    {
        $valid = collect();
        $invalid = collect();
        $seenNames = [];

        foreach ($rows as $index => $row) {
            $errors = [];

            $name = trim($row['name'] ?? '');
            $grade = trim($row['grade'] ?? '');
            $capacity = trim($row['capacity'] ?? '30');

            if ($name === '') {
                $errors['name'] = 'Nama kelas wajib diisi.';
            } elseif (isset($seenNames[strtolower($name)])) {
                $errors['name'] = 'Nama kelas duplikat.';
            }

            if ($grade === '') {
                $errors['grade'] = 'Tingkat wajib diisi.';
            }

            if (! is_numeric($capacity) || (int) $capacity < 1) {
                $errors['capacity'] = 'Kapasitas harus angka > 0.';
            }

            $parsed = [
                '_index' => $index,
                'name' => $name,
                'grade' => $grade,
                'capacity' => (int) ($capacity ?: 30),
            ];

            if (! empty($errors)) {
                $parsed['_errors'] = $errors;
                $invalid->push($parsed);
            } else {
                $seenNames[strtolower($name)] = true;
                $valid->push($parsed);
            }
        }

        return ['valid' => $valid, 'invalid' => $invalid];
    }

    /**
     * Validate Step 2 data: Staff accounts (Admin & Asatidz).
     *
     * @param  array<int, array{name: string, email: string, phone: string, role: string}>  $rows
     * @return array{valid: Collection, invalid: Collection}
     */
    public function validateStaff(array $rows): array
    {
        $valid = collect();
        $invalid = collect();
        $seenEmails = [];

        foreach ($rows as $index => $row) {
            $errors = [];

            $name = trim($row['name'] ?? '');
            $email = trim($row['email'] ?? '');
            $phone = trim($row['phone'] ?? '');
            $role = trim($row['role'] ?? 'Asatidz');

            if ($name === '') {
                $errors['name'] = 'Nama wajib diisi.';
            }

            if ($email === '') {
                $errors['email'] = 'Email wajib diisi.';
            } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Format email tidak valid.';
            } elseif (isset($seenEmails[strtolower($email)])) {
                $errors['email'] = 'Email duplikat.';
            }

            if ($phone !== '' && ! preg_match('/^(\+?62|08)\d{8,13}$/', preg_replace('/[\s\-]/', '', $phone))) {
                $errors['phone'] = 'Format No. WA tidak valid.';
            }

            $allowedRoles = ['Super Admin', 'Admin Keuangan', 'Admin Akademik', 'Asatidz'];
            if (! in_array($role, $allowedRoles)) {
                $errors['role'] = 'Role tidak valid. Pilih: '.implode(', ', $allowedRoles);
            }

            $parsed = [
                '_index' => $index,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'role' => $role,
            ];

            if (! empty($errors)) {
                $parsed['_errors'] = $errors;
                $invalid->push($parsed);
            } else {
                $seenEmails[strtolower($email)] = true;
                $valid->push($parsed);
            }
        }

        return ['valid' => $valid, 'invalid' => $invalid];
    }

    /**
     * Validate Step 3 data: Students with parent & billing info.
     *
     * @param  array<int, array{name: string, gender: string, class_name: string, parent_name: string, parent_phone: string, parent_email: string, spp_amount: string, initial_arrears: string}>  $rows
     * @param  array<string>  $validClassNames  Class names available from Step 1
     * @return array{valid: Collection, invalid: Collection}
     */
    public function validateStudents(array $rows, array $validClassNames): array
    {
        $valid = collect();
        $invalid = collect();

        foreach ($rows as $index => $row) {
            $errors = [];

            $name = trim($row['name'] ?? '');
            $gender = strtoupper(trim($row['gender'] ?? ''));
            $className = trim($row['class_name'] ?? '');
            $parentName = trim($row['parent_name'] ?? '');
            $parentPhone = trim($row['parent_phone'] ?? '');
            $parentEmail = trim($row['parent_email'] ?? '');
            $sppAmount = trim($row['spp_amount'] ?? '0');
            $initialArrears = trim($row['initial_arrears'] ?? '0');

            if ($name === '') {
                $errors['name'] = 'Nama santri wajib diisi.';
            }

            if (! in_array($gender, ['L', 'P'])) {
                $errors['gender'] = 'Gender harus L atau P.';
            }

            if ($className === '') {
                $errors['class_name'] = 'Kelas wajib diisi.';
            } elseif (! empty($validClassNames) && ! in_array($className, $validClassNames)) {
                $errors['class_name'] = 'Kelas tidak ditemukan di Step 1.';
            }

            if ($parentName === '') {
                $errors['parent_name'] = 'Nama wali santri wajib diisi.';
            }

            if ($parentPhone === '') {
                $errors['parent_phone'] = 'No. WA wali wajib diisi.';
            } elseif (! preg_match('/^(\+?62|08)\d{8,13}$/', preg_replace('/[\s\-]/', '', $parentPhone))) {
                $errors['parent_phone'] = 'Format No. WA tidak valid.';
            }

            if ($parentEmail !== '' && ! filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
                $errors['parent_email'] = 'Format email tidak valid.';
            }

            if (! is_numeric($sppAmount) || (float) $sppAmount < 0) {
                $errors['spp_amount'] = 'Nominal SPP harus angka >= 0.';
            }

            if (! is_numeric($initialArrears) || (float) $initialArrears < 0) {
                $errors['initial_arrears'] = 'Tunggakan awal harus angka >= 0.';
            }

            $parsed = [
                '_index' => $index,
                'name' => $name,
                'gender' => $gender ?: 'L',
                'class_name' => $className,
                'parent_name' => $parentName,
                'parent_phone' => $parentPhone,
                'parent_email' => $parentEmail,
                'spp_amount' => (float) ($sppAmount ?: 0),
                'initial_arrears' => (float) ($initialArrears ?: 0),
            ];

            if (! empty($errors)) {
                $parsed['_errors'] = $errors;
                $invalid->push($parsed);
            } else {
                $valid->push($parsed);
            }
        }

        return ['valid' => $valid, 'invalid' => $invalid];
    }

    /**
     * Validate Step 4 data: Non-SPP fee types (Daftar Ulang, etc.).
     *
     * @param  array<int, array{name: string, category: string, amount: string, target_grades: string}>  $rows
     * @return array{valid: Collection, invalid: Collection}
     */
    public function validateFeeTypes(array $rows): array
    {
        $valid = collect();
        $invalid = collect();

        foreach ($rows as $index => $row) {
            $errors = [];

            $name = trim($row['name'] ?? '');
            $category = trim($row['category'] ?? 'lain');
            $amount = trim($row['amount'] ?? '0');
            $targetGrades = trim($row['target_grades'] ?? 'semua');

            if ($name === '') {
                $errors['name'] = 'Nama tagihan wajib diisi.';
            }

            $allowedCategories = ['kegiatan', 'seragam', 'lain'];
            if (! in_array($category, $allowedCategories)) {
                $errors['category'] = 'Kategori harus: '.implode(', ', $allowedCategories);
            }

            if (! is_numeric($amount) || (float) $amount <= 0) {
                $errors['amount'] = 'Nominal harus angka > 0.';
            }

            $parsed = [
                '_index' => $index,
                'name' => $name,
                'category' => $category,
                'amount' => (float) ($amount ?: 0),
                'target_grades' => $targetGrades,
            ];

            if (! empty($errors)) {
                $parsed['_errors'] = $errors;
                $invalid->push($parsed);
            } else {
                $valid->push($parsed);
            }
        }

        return ['valid' => $valid, 'invalid' => $invalid];
    }

    /**
     * Parse Excel rows into structured arrays for each step type.
     *
     * @param  array<int, array<int, mixed>>  $excelRows  Raw Excel rows (header already removed)
     * @param  string  $stepType  One of: classes, staff, students, fee_types
     * @return array<int, array<string, mixed>>
     */
    public function parseExcelRows(array $excelRows, string $stepType): array
    {
        $parsed = [];

        foreach ($excelRows as $row) {
            if (! array_filter($row)) {
                continue;
            }

            $parsed[] = match ($stepType) {
                'classes' => [
                    'name' => trim($row[0] ?? ''),
                    'grade' => trim($row[1] ?? ''),
                    'capacity' => trim($row[2] ?? '30'),
                ],
                'staff' => [
                    'name' => trim($row[0] ?? ''),
                    'email' => trim($row[1] ?? ''),
                    'phone' => trim($row[2] ?? ''),
                    'role' => trim($row[3] ?? 'Asatidz'),
                ],
                'students' => [
                    'name' => trim($row[0] ?? ''),
                    'gender' => strtoupper(trim($row[1] ?? 'L')),
                    'class_name' => trim($row[2] ?? ''),
                    'parent_name' => trim($row[3] ?? ''),
                    'parent_phone' => trim($row[4] ?? ''),
                    'parent_email' => trim($row[5] ?? ''),
                    'spp_amount' => trim($row[6] ?? '0'),
                    'initial_arrears' => trim($row[7] ?? '0'),
                ],
                'fee_types' => [
                    'name' => trim($row[0] ?? ''),
                    'category' => trim($row[1] ?? 'lain'),
                    'amount' => trim($row[2] ?? '0'),
                    'target_grades' => trim($row[3] ?? 'semua'),
                ],
                default => [],
            };
        }

        return $parsed;
    }
}
