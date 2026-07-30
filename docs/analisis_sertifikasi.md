# Panduan Analisis & Presentasi Proyek Sertifikasi: SIPAS-Hub (Sistem Informasi Pembayaran Sekolah)

Dokumen ini disusun untuk membantu Anda menjawab pertanyaan dan mempresentasikan proyek web app **SIPAS-Hub** secara terstruktur, padat, dan detail sesuai dengan persyaratan sertifikasi Analis Program (FR.IA.04A). 

*(Catatan: Studi kasus SM Sport Center pada soal ujian adalah contoh standar, namun karena Anda menggunakan aplikasi riil **SIPAS-Hub** sebagai portofolio/tugas akhir, analisis di bawah ini telah disesuaikan 100% untuk SIPAS-Hub agar presentasi Anda jauh lebih memukau asesor).*

---

## 📁 DAFTAR DOKUMEN FISIK (BUKTI ASESMEN)
Sebagai persiapan ujian, Anda wajib menyiapkan 8 bukti fisik. 7 Dokumen teknis telah disiapkan dan dirangkum secara khusus untuk aplikasi SIPAS-Hub Anda. Silakan klik tautan di bawah ini untuk melihat dan mencetak masing-masing dokumen:

1. **[Dokumen 1: Analisis Skalabilitas](file:///C:/Users/HYPE%20AMD/.gemini/antigravity/brain/bd3f001a-5120-4616-bb3b-53858988af8c/dokumen_analisis_skalabilitas.md)**
2. **[Dokumen 2: ERD Lengkap & SQL Script](file:///C:/Users/HYPE%20AMD/.gemini/antigravity/brain/bd3f001a-5120-4616-bb3b-53858988af8c/dokumen_erd_lengkap.md)**
3. **[Dokumen 3: UI & Source Code (Hero Features)](file:///C:/Users/HYPE%20AMD/.gemini/antigravity/brain/bd3f001a-5120-4616-bb3b-53858988af8c/dokumen_ui_dan_source_code.md)**
4. **[Dokumen 4: Dokumentasi Kode Program](file:///C:/Users/HYPE%20AMD/.gemini/antigravity/brain/bd3f001a-5120-4616-bb3b-53858988af8c/dokumen_dokumentasi_kode_program.md)**
5. **[Dokumen 5: Laporan Debugging (Race Condition)](file:///C:/Users/HYPE%20AMD/.gemini/antigravity/brain/bd3f001a-5120-4616-bb3b-53858988af8c/dokumen_laporan_debugging.md)**
6. **[Dokumen 6: Laporan Hasil Profiling (N+1 Query)](file:///C:/Users/HYPE%20AMD/.gemini/antigravity/brain/bd3f001a-5120-4616-bb3b-53858988af8c/dokumen_hasil_profiling.md)**
7. **[Dokumen 7: Laporan Hasil Unit & Integration Testing](file:///C:/Users/HYPE%20AMD/.gemini/antigravity/brain/bd3f001a-5120-4616-bb3b-53858988af8c/dokumen_hasil_testing.md)**
*(Catatan: Bukti ke-8 adalah Demonstrasi Sistem & Presentasi Lisan Anda saat ujian).*

---

## 📌 Latar Belakang & Skenario Masalah
Proses pengelolaan pembayaran sekolah (SPP, kegiatan, dll) seringkali dilakukan secara manual sehingga menimbulkan masalah:
1. **Inefisiensi Penagihan & Pencatatan:** Proses penagihan SPP bulanan secara manual sangat memakan waktu dan rentan terjadi kesalahan (*human error*) dalam pencatatan transaksi.
2. **Rumitnya Rekonsiliasi:** Pihak sekolah (Admin) kesulitan dalam melakukan rekonsiliasi (pencocokan) data transfer bank dengan catatan keuangan sekolah.
3. **Keterbatasan Informasi:** Orang tua kesulitan memantau riwayat pembayaran secara mandiri dan sering terlambat menerima informasi terkait tagihan baru maupun konfirmasi pembayaran sukses.

**Solusi (SIPAS-Hub):** Membangun platform manajemen keuangan sekolah modern berbasis web yang secara langsung menjawab ketiga masalah di atas:
1. **Otomatisasi Penagihan:** Sistem mampu meng-*generate* ribuan tagihan SPP secara otomatis dan mencatat seluruh transaksi keuangan lengkap dengan jejak auditnya (menjawab masalah ke-1).
2. **Sistem Pembayaran *Hybrid* (Terpusat):** Menyediakan pembayaran *online* instan (via Midtrans) maupun pembayaran tunai/manual (via Kasir Admin). Keduanya terpusat di satu sistem yang akan memperbarui status tagihan secara otomatis, sehingga sekolah tidak lagi perlu melakukan rekonsiliasi mutasi bank manual yang rumit (menjawab masalah ke-2).
3. **Portal Orang Tua & Notifikasi WA:** Menyediakan antarmuka *mobile-first* bagi orang tua untuk mengecek riwayat, sekaligus mengirimkan Notifikasi WhatsApp (WA) secara otomatis saat tagihan baru terbit maupun saat pembayaran berhasil (menjawab masalah ke-3).

---

## ⚙️ Kelompok 1: Analisis Kebutuhan & Skalabilitas

### 1. Analisis Kebutuhan (Kebutuhan Fungsional & Non-Fungsional)
**Identifikasi Aktor:**
- **Orang Tua / Wali Murid:** Pengguna portal untuk melihat tagihan dan melakukan pembayaran.
- **Admin (Sekolah / Tata Usaha):** Pengelola data akademik dan pembuat tagihan massal.

**Kebutuhan Fungsional (Berdasarkan Aktor):**
1. **Aktor: Admin (Sekolah / Tata Usaha)**
   - Mengelola **Data Master Akademik** (Siswa, Kelas, Tahun Ajaran) sebagai basis pembuatan tagihan.
   - Melakukan **Otomatisasi SPP** (*generate* ribuan tagihan bulanan secara massal dalam sekali klik).
   - Mencatat **pembayaran manual (Tunai/Kasir)** bagi orang tua yang menitipkan uang langsung ke sekolah.
   - Melihat **Dashboard Analitik** untuk memantau ringkasan pemasukan dan total tunggakan.

2. **Aktor: Orang Tua / Wali Murid**
   - Mengakses **Portal Orang Tua** (*mobile-first*) untuk memantau rincian tagihan anak.
   - Melakukan **pembayaran *online* instan** (VA, QRIS) melalui integrasi *Payment Gateway* (Midtrans Snap).
   - Mengunduh **bukti pembayaran (Kwitansi PDF)** secara mandiri.

3. **Sistem (Otomatisasi Latar Belakang)**
   - Mengirimkan **Notifikasi WhatsApp (WA) otomatis** kepada orang tua (misal: saat tagihan baru terbit & saat lunas).
   - Mencatat **Jejak Audit (*Audit Trail*)** pada setiap perubahan/transaksi data keuangan demi keamanan.

**Kebutuhan Non-Fungsional:**
- **Keamanan Tinggi (*Security*):** Proteksi terhadap *Race Condition* (pembayaran ganda) dan *Parameter Tampering* pada integrasi *payment gateway*.
- **Kinerja & Skalabilitas (*Performance*):** Menggunakan sistem *Background Queue* agar proses *generate* ribuan tagihan massal tidak membuat server kelebihan beban (*down*).
- **Kenyamanan Pengguna (*Usability*):** Antarmuka dirancang dengan pendekatan *Mobile-First* (sangat responsif di layar HP) mengingat mayoritas orang tua akan mengakses portal pembayaran melalui *smartphone*.

### 2. Analisis Skalabilitas
- **Potensi *Bottleneck*:** 
  1) Proses *generate* tagihan massal untuk seluruh siswa secara bersamaan di awal bulan. 
  2) *Load* pada *dashboard* admin saat menghitung total tunggakan dari ribuan baris data transaksi.
  3) **Limitasi / Timeout pada API Provider WhatsApp** ketika sistem mencoba melakukan *blast* ribuan pesan WA secara sinkron (*synchronous*).
- **Estimasi Pertumbuhan Data:** Jika ada 1000 siswa, maka setiap bulan akan ada penambahan 1000 data tagihan. Dalam setahun ada 12.000 data tagihan baru, belum termasuk data pembayaran.
- **Rekomendasi Peningkatan Performa (Yang sudah diterapkan):**
  - Menggunakan **Background Queue** (via Redis/Database) untuk memproses tagihan massal **serta mengirimkan pesan WA** di latar belakang. Dengan *queue*, pesan WA dikirimkan secara bertahap tanpa membuat UI admin lambat atau terkena *rate limit* dari *provider* API.
  - Menerapkan *Database Indexing* pada kolom status pembayaran, `student_id`, dan `created_at`.
- **Output yang perlu disiapkan (Bentuk Dokumen Fisik/PDF):** Anda perlu membuat dokumen "Analisis Skalabilitas" berisi 3 poin inti berikut:
  1. **Tabel Estimasi Data:** Hitungan matematis pertumbuhan data (contoh: 1000 siswa x 12 bulan = 12.000 data tagihan/tahun).
  2. **Identifikasi Bottleneck:** Penjelasan bahwa menekan tombol "*Generate* 1000 Tagihan sekaligus Kirim WA" akan membuat browser Admin *freeze*/*timeout* jika dieksekusi secara normal (*synchronous*).
  3. **Solusi Arsitektur:** Penjelasan implementasi *Background Queue* (memindahkan proses berat ke latar belakang) dan *Database Indexing* (mempercepat kueri baca pada jutaan baris data).

---

## 💻 Kelompok 2: Penulisan Kode Sumber

### 1. Rancangan Basis Data
**Tabel Utama & Relasi (Disesuaikan dengan SIPAS-Hub):**
1. `users`: Menyimpan data admin dan orang tua murid.
2. `students`: Menyimpan data siswa (berelasi dengan kelas dan wali murid/users).
3. `invoices` (Tagihan): Menyimpan data tagihan (id, student_id, nominal, status, due_date).
4. `payments` (Pembayaran): Menyimpan data transaksi (id, invoice_id, midtrans_transaction_id, payment_type, gross_amount, status).

**Relasi (ERD):**
- User (Orang Tua) memiliki banyak Student (`1-to-Many`).
- Student memiliki banyak Invoices (`1-to-Many`).
- Invoice berelasi dengan Payment.
- **Output yang perlu Anda siapkan:** *Entity Relationship Diagram (ERD)* sistem SIPAS-Hub dan Skrip SQL (DDL).

### 2. Implementasi Program & UI
Sistem dibangun menggunakan **Laravel 13, Livewire 4, dan Tailwind CSS 4**.
- **Portal Orang Tua:** UI yang *mobile-first* dengan tombol "Bayar Sekarang" yang memanggil Midtrans Snap.
- **Admin Dashboard:** Menampilkan grafik analitik dan tabel data master dengan fitur validasi ketat.
- **Output yang perlu Anda siapkan:** *Screenshot UI* dan cuplikan *Source Code* (terutama integrasi Livewire dan Midtrans).

### 3. Pembuatan Dokumen Kode Program
Penjelasan mengenai arsitektur Laravel (MVC/Action), struktur komponen Livewire, konfigurasi Midtrans & API WhatsApp di `.env`, serta logika integrasi *Webhook Security*.
- **Output yang perlu Anda siapkan:** Dokumen Kode Program (Manual/Dokumentasi Teknis).

### 4. *Debugging* & Perbaikan (Kasus Menarik untuk Asesor)
**Skenario Masalah (Sangat relevan untuk SIPAS-Hub):** Terjadinya *Race Condition* saat orang tua membayar tagihan yang sama dua kali hampir bersamaan, menyebabkan satu tagihan terbayar ganda (*Double Payment*).
- **Penyebab Masalah (*Root Cause*):** Jeda waktu saat sistem memproses *webhook* Midtrans sehingga status tagihan belum sempat berubah menjadi "Lunas" ketika *request* kedua masuk.
- **Solusi & Perbaikan Kode:** Mengimplementasikan **Transaction Locking Logic** (misalnya menggunakan *Pessimistic Locking* `lockForUpdate()` di Laravel) atau memastikan logika *idempotent* pada penanganan *Webhook* Midtrans, sehingga *webhook* dengan Order ID yang sama tidak akan diproses dua kali.
- **Output yang perlu Anda siapkan:** Laporan Debugging (Bug report *Race Condition*, cuplikan kode *locking/webhook*, dan hasil perbaikan).

---

## 🔍 Kelompok 3: *Review* Kode Sumber & *Profiling*

### 1. *Profiling* Program
- **Identifikasi Bagian Lambat:** Mengukur *response time* pada *Dashboard Admin* saat memuat tabel riwayat pembayaran yang diurutkan berdasarkan siswa dan kelas. Terjadi masalah kinerja karena kueri N+1 (memanggil data siswa berulang-ulang untuk setiap baris pembayaran).
- **Rekomendasi Perbaikan:** Mengoptimalkan *query* dengan metode *Eager Loading* di Laravel Eloquent. Mengubah kueri `Payment::all()` menjadi `Payment::with(['bill.student.user'])->paginate(20)`.
- **Output yang perlu Anda siapkan:** Hasil *Profiling* (Tangkapan layar *query execution time* dari *debugger* seperti Laravel Telescope atau Clockwork, sebelum dan sesudah *Eager Loading*).

---

## ✅ Kelompok 4: Pengujian Perangkat Lunak (*Testing*)

### 1. Pengujian Unit (*Unit Testing* dengan Pest PHP)
Skenario wajib untuk diuji secara programatis:
1. **Otomatisasi Tagihan & Notifikasi:** Menguji fungsi *job/queue* apakah berhasil membuat tagihan sekaligus memastikan bahwa aksi pengiriman Notifikasi WA (*Job*) ter-*dispatch* masuk ke dalam antrean (*queue*).
2. **Kunci Transaksi:** Menguji apakah sistem menolak pengubahan nominal pada tagihan yang sudah memiliki status "Lunas" (Transaction Locking Logic).
3. **Download PDF:** Memastikan *route* cetak kwitansi mengembalikan *response* file bertipe PDF.
- **Output yang perlu Anda siapkan:** Dokumen Pengujian Unit (Kode *Test Cases* Pest PHP).

### 2. Pengujian Integrasi (*Integration Testing*)
Menguji aliran sistem secara penuh (End-to-End).
- **Skenario Alur:** `Admin Buat Tagihan` -> `Ortu Login` -> `Ortu Klik Bayar (Midtrans Mock)` -> `Webhook Diterima` -> `Status Lunas` -> **`Notifikasi WA Terkirim Otomatis`** -> `Ortu Download Kwitansi PDF`.
- **Evaluasi:** Memastikan seluruh rantai proses (dari pembuatan *invoice*, pembayaran, hingga pengiriman notif WA dan pencatatan jejak audit) berjalan mulus tanpa intervensi manual.
- **Output yang perlu Anda siapkan:** Laporan *Integration Testing*.

---

## 💡 Tips Strategi Presentasi Memukau (Untuk SIPAS-Hub)
1. **Gunakan Istilah Profesional Anda:** Saat presentasi, sebutkan fitur unggulan Anda seperti *"Mobile-First Portal"*, *"Automated SPP Generation via Queue"*, dan *"Transaction Locking Logic"*. Asesor sangat menyukai analis program yang memikirkan skenario dunia nyata (seperti mencegah *race condition* pada pembayaran).
2. **Justifikasi Pilihan Teknologi:** Jika ditanya mengapa memilih Livewire 4, jelaskan bahwa ia memberikan *reactive UI* seperti SPA (Single Page Application) tanpa harus membuat API terpisah, sehingga proses *development* lebih cepat dan kode lebih mudah di-*maintain*.
3. **Tunjukkan Hasil Cetak:** Siapkan 8 bukti fisik (Dokumen Analisis, ERD, Laporan Testing, dll). Karena Anda menggunakan SIPAS-Hub, lampirkan juga contoh hasil cetak Kwitansi PDF sebagai nilai tambah yang kuat.

---

## 📚 Glosarium & Penjelasan Tambahan (Buku Pintar Anda)

Bagian ini berisi pendalaman materi dari poin-poin di atas agar Anda benar-benar memahami konsep teknisnya saat ditanya oleh asesor.

### 1. Apa yang dimaksud dengan "Proses Rekonsiliasi Pembayaran yang Rumit"?
**Rekonsiliasi pembayaran** adalah proses mencocokkan catatan keuangan sekolah dengan mutasi/rekening koran dari bank. 

Dalam **sistem manual (sebelum ada SIPAS-Hub):**
- Tata Usaha (Admin Sekolah) harus mengecek mutasi bank satu per satu setiap hari. 
- Jika ada uang masuk sebesar Rp 250.000, Admin harus mencari tahu: *"Ini uang dari siapa? SPP untuk bulan apa? Anaknya kelas berapa?"*
- Hal ini sangat rumit karena nama rekening pentransfer seringkali berbeda dengan nama siswa (misalnya rekening atas nama kakek/paman), atau orang tua lupa mencantumkan berita transfer. Jika ada ratusan siswa, proses ini bisa memakan waktu berhari-hari dan rawan kesalahan.

Dalam **sistem SIPAS-Hub (Solusi Otomatis via Midtrans):**
- Setiap tagihan yang dibuat akan memiliki nomor **Virtual Account (VA) atau QRIS yang unik**.
- Saat orang tua membayar secara *online*, Midtrans secara *real-time* akan mengirimkan sinyal (*Webhook*) ke server SIPAS-Hub kita.
- Sistem secara otomatis akan mengubah status tagihan menjadi **"Lunas"** tanpa perlu Admin mengecek mutasi bank lagi (rekonsiliasi berjalan **100% otomatis** di latar belakang).

### 2. Jika Sudah Ada Midtrans, Mengapa Masih Ada Fitur Pembayaran Manual?
Ini adalah konsep **Hybrid Payment System**. Di dunia nyata (terutama lingkungan sekolah), tidak semua orang tua memiliki akses ke* mobile banking* atau terbiasa membayar secara *online*. Beberapa orang tua akan tetap datang ke sekolah untuk menitipkan uang SPP secara **tunai (cash)**. 

Oleh karena itu, sistem SIPAS-Hub mengakomodasi *edge case* (kondisi khusus) ini dengan menyediakan fitur **Pembayaran Manual oleh Admin**.
- **Alurnya:** Orang tua bayar tunai -> Admin mencari tagihan di *dashboard* -> Admin klik tombol "Bayar Tunai".
- **Kelebihannya:** Meskipun pembayarannya manual, sistem tetap akan mengunci transaksi (*Transaction Locking*), mencatat jejak audit (*Audit Trail*), dan mengirimkan Notifikasi WA serta Kwitansi PDF secara otomatis persis seperti pembayaran *online*. Ini memastikan data keuangan tetap terpusat dan konsisten.

### 3. Mengapa Ada "Manajemen Akademik" Jika Ini Aplikasi Pembayaran?
Walaupun fokus utama SIPAS-Hub adalah keuangan/pembayaran, sebuah sistem penagihan tidak akan bisa berjalan tanpa adanya **Data Master**. 
Sistem harus tahu *"Siapa yang ditagih?"* (Data Siswa), *"Berapa nominalnya?"* (Berdasarkan Data Kelas/Angkatan), dan *"Untuk periode kapan?"* (Data Tahun Ajaran). 

Oleh karena itu, Manajemen Akademik dimasukkan ke dalam kebutuhan fungsional hanya sebagai **pendukung pondasi sistem** (CRUD dasar), bukan sebagai fokus utama seperti sistem e-learning. Saat presentasi, Anda bisa menyebutnya sebagai pengelolaan **"Data Master"**.
