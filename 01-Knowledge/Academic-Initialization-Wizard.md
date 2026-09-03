---
date: 2026-09-03
type: knowledge-base
status: Approved
tags:
  - architecture
  - academic-initialization
---

# Feature & Architecture: Academic Initialization Wizard

## 🎯 Konteks & Tujuan
- Fitur ini dirancang sebagai "Wizard Inisialisasi Tahun Ajaran" yang dijalankan dari awal atau ketika tahun ajaran baru dimulai.
- Tujuannya adalah mengizinkan admin/pengelola untuk melakukan input masal atau terstruktur dari kondisi data kosong (kelas, pengelola, santri + wali, jenis tagihan non-SPP) dan pada langkah terakhir secara atomik men-generate seluruh tagihan (12 bulan SPP, tunggakan awal, dan tagihan non-spp) agar terhindar dari partial data (sistem half-state).

## 🛠️ Logika Bisnis & Struktur File Utama
- **Penjelasan Alur**:
  Proses dilakukan dalam 5 step (Tahun Ajaran, Pengelola, Santri, Tagihan, dan Submit/Preview). Setiap langkah didukung dengan 2 metode pengisian: entry manual form dan unggah Excel masal.
  Data dikumpulkan ke dalam memori Livewire (valid vs invalid) dan memungkinkan pengguna memperbaiki baris yang invalid secara *inline*. 
  Setelah step terakhir disubmit, `AcademicInitializationService` membungkus seluruh pembuatan Model (`AcademicYear`, `SchoolClass`, `User`, `Student`, `FeeType`, `Invoice`) ke dalam satu `DB::transaction`. Jika salah satu gagal, seluruh inisialisasi di-rollback.

- **File Kunci**:
  - `app/Livewire/Pages/Academic/AcademicInitializationWizard.php`: Komponen state manager untuk wizard 5 tahap, upload excel, validation rendering.
  - `resources/views/livewire/pages/academic/academic-initialization-wizard.blade.php`: Antarmuka utama dengan Flux UI, memuat *view partials* import, error table, valid table.
  - `app/Services/InitializationParserService.php`: Layanan pemisah parsing & validasi universal dari form atau excel row array.
  - `app/Services/AcademicInitializationService.php`: Core executor (transactional DB operations) untuk menyimpan semua entity ke tabel masing-masing secara massal dan meng-generate 12 bulan SPP + tagihan lain.
  - `database/migrations/2026_09_03_163218_add_spp_amount_and_initial_arrears_to_students_table.php`: Penambahan custom spp per siswa & bawaan tunggakan dari tahun sebelumnya.

## ⚠️ Batasan & Edge Cases (Gotchas)
- **Email Conflict**: Validasi memblokir import jika mendeteksi email yang sudah pernah terdaftar, termasuk duplicate email di dalam file excel saat upload, agar tidak conflict di `users` table.
- **Rollback Behaviour**: Harap selalu gunakan `AcademicInitializationService` saat mengeksekusi inisialisasi agar fitur `DB::transaction` bekerja. Jika terjadi error di step 4 (generating invoices), step 1, 2, 3 tidak akan "tersangkut" di database.
- **Memory Limit on Excel**: Hati-hati saat import file data besar (>5000 row) di server resource kecil karena Maatwebsite/Excel memuat semua row ke memori Livewire dalam variabel array.

## 🔗 Referensi & Graph Links
- Log Terkait: [[dev-log-2026-09-03]]
