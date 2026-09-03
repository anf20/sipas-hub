# 📋 Panduan Wawancara Analisis Fit-Gap Sistem SIPAS-Hub

Dokumen ini disusun sebagai instrumen wawancara analisis kesenjangan (*Fit-Gap Analysis*) antara kebutuhan operasional sekolah/pesantren dengan fungsionalitas sistem **SIPAS-Hub**. 

Setiap level wawancara **diawali dengan investigasi kondisi data riil eksisting yang dimiliki pesantren saat ini**, dengan fokus utama pada 4 kelompok data inti:
1. **Data Santri** (NIS, Nama, Kelas/Rombel, Gender, Status Aktif)
2. **Data Wali Santri** (Nama Orang Tua, Nomor WhatsApp Aktif, Kontak)
3. **Data Tagihan Non-SPP** (Uang Gedung/Pembangunan, Daftar Ulang, Uang Makan/Asrama, Ujian/Kegiatan)
4. **Data Pemasukan, Pengeluaran, & Laporan Keuangan/Tunggakan** (Penerimaan Kas, Laporan Tunggakan Rinci Santri, dan Buku Kas Pengeluaran)

---

## 🗺️ Peta Alur Hierarki Wawancara (*Interview Roadmap*)

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│  LEVEL 1: AUDIT DATA MASTER SANTRI & DATA WALI SANTRI (Narasumber: Staf Tata Usaha)              │
│  ├─ 1.1 Investigasi Kondisi Data Santri & Wali Santri Eksisting di Pesantren                     │
│  ├─ 1.2 Format Kontak WhatsApp & Saluran Komunikasi Wali Murid                                   │
│  └─ 1.3 Siklus Kenaikan Kelas, Kelulusan, & Santri Mutasi                                        │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│  LEVEL 2: AUDIT DATA TARIF SPP, TAGIHAN NON-SPP, & KASIR LOKET (Narasumber: Kasir & Bendahara)   │
│  ├─ 2.1 Investigasi Kondisi Data Tarif SPP & Tagihan Non-SPP (Uang Gedung, Asrama, Ujian)       │
│  ├─ 2.2 Akun Kasir, Struk Fisik Thermal (58mm/80mm), & Rekap Kas Harian                         │
│  ├─ 2.3 Pembayaran Parsial (Cicilan Tagihan Non-SPP) & Pembayaran di Muka                       │
│  └─ 2.4 Rekap Saldo Tunggakan Tahun Ajaran Lalu & Skema Keringanan/Diskon                        │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│  LEVEL 3: KANAL PEMBAYARAN ONLINE (MIDTRANS), REKENING BANK & WA (Narasumber: Bendahara & Humas) │
│  ├─ 3.1 Investigasi Kondisi Data Rekening Bank Yayasan & Alur Verifikasi Bukti Transfer         │
│  ├─ 3.2 Payment Gateway Online (Midtrans Snap), Skema Biaya Admin, & Saluran Bayar (QRIS/VA)    │
│  └─ 3.3 Jadwal & Format Notifikasi Pengingat Tagihan WhatsApp (Broadcast)                        │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│  LEVEL 4: AUDIT ARUS KAS & KEBUTUHAN LAPORAN KEUANGAN/TUNGGAKAN (Narasumber: Bendahara & Yayasan)│
│  ├─ 4.1 Investigasi Kondisi Data & Alur Pencatatan Uang Masuk-Keluar Saat Ini (As-Is Flow)       │
│  ├─ 4.2 Komponen Wajib Laporan Keuangan & Kebutuhan Laporan Rinci Santri Menunggak              │
│  ├─ 4.3 Pos Kategori Beban Operasional Lembaga & Pagu Anggaran [Fase 2 / GAP]                   │
│  └─ 4.4 Kas Kecil (Petty Cash) & Pertanggungjawaban Kas Bon (LPJ) [Fase 2 / GAP]                │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│  LEVEL 5: STRATEGI TRANSISI, ROADMAP ROLLOUT, & CHECKLIST EXCEL (Narasumber: Seluruh Tim)        │
│  ├─ 5.1 Masa Transisi Pembukuan Ganda (Parallel Run 30 Hari) & Roadmap 4 Tahap                  │
│  └─ 5.2 Checklist 5 File Excel Kunci & Pencocokan Kolom Database (Database Mapping)             │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---
---

# 🏛️ LEVEL 1: AUDIT DATA MASTER SANTRI & DATA WALI SANTRI (Untuk Staf TU)

```
                               ┌──────────────────────────────────────────────┐
                               │  Modul 1: Data Santri & Wali Santri (TU)     │
                               └──────────────────────┬───────────────────────┘
                                                      │
         ┌────────────────────────┬───────────────────┴────────────────┬────────────────────────┐
         ▼                        ▼                                    ▼                        ▼
┌───────────────────┐    ┌───────────────────┐                ┌───────────────────┐    ┌───────────────────┐
│ 1.1 Kondisi Data  │    │ 1.2 Validitas No. │                │ 1.3 Siklus Mutasi │    │ 1.4 Kerapian File │
│     Santri & Wali │    │     WhatsApp Wali │                │     & Kelulusan   │    │     Excel Master  │
└───────────────────┘    └───────────────────┘                └───────────────────┘    └───────────────────┘
```

---

### 1.1. Kondisi & Kelengkapan Data Santri dan Wali Santri Eksisting
> **Pertanyaan Investigasi Awal:**  
> *"Bagaimana kondisi kelengkapan dan format data Santri serta data Wali Santri yang saat ini tersimpan di pihak pesantren? Apakah data identitas santri (NIS, Nama, Kelas, Jenis Kelamin) dan data wali santri (Nama Orang Tua, No. WhatsApp) sudah tercatat lengkap dan rapi dalam satu file terpadu?"*

#### 🔍 Latar Belakang & Urgensi
Sistem SIPAS-Hub membutuhkan 2 entitas data utama: **Data Santri** (`nis`, `name`, `gender`, `school_class_id`, `status`) dan **Data Wali Santri** (`parent_name`, `parent_phone`). Kerapian data awal ini menjadi syarat mutlak agar portal wali murid dan sistem penagihan otomatis bisa berjalan tepat sasaran.

#### 📌 Turunan Pertanyaan Penggali (*Probing Questions*):
1. **Keunikan & Format NIS Santri:**
   - *Apakah setiap santri dijamin memiliki Nomor Induk Santri (NIS) yang unik dan tidak ada nomor ganda?*
   - *Jika ada santri baru yang belum memiliki NIS resmi, format penomoran sementara apa yang digunakan TU?*
2. **Kesesuaian Data Wali dengan Santri:**
   - *Apakah setiap baris data santri sudah terhubung langsung dengan nama orang tua/wali penanggung jawabnya?*
   - *Jika satu orang tua memiliki lebih dari 1 anak di pesantren ini, apakah nomor kontak wali dicatat sama di setiap data santri?*
3. **Keseragaman Format Kelas/Rombel:**
   - *Apakah penulisan nama kelas sudah konsisten di semua tingkatan (contoh seragam `VII A`, atau masih tercampur antara `7A`, `VII-A`, `Kelas 7.A`)?*

#### 💻 Implikasi Teknis & Database SIPAS-Hub:
* **Tabel `students` & `school_classes`:** Penyelarasan kolom `nis`, `name`, `gender`, `school_class_id`, `parent_name`, `parent_phone`, `status`.

---

### 1.2. Format Kontak WhatsApp & Saluran Komunikasi Wali Murid
> **Pertanyaan Utama:**  
> *"Apakah nomor kontak wali santri yang tercatat saat ini mayoritas sudah berupa nomor WhatsApp aktif, atau sebagian masih nomor telepon rumah/tidak aktif?"*

#### 🔍 Latar Belakang & Urgensi
SIPAS-Hub mengandalkan pengiriman notifikasi tagihan bulanan dan konfirmasi pembayaran melalui WhatsApp. Format nomor yang tidak valid akan menggagalkan pengiriman pesan pengingat.

#### 📌 Turunan Pertanyaan Penggali (*Probing Questions*):
1. **Standarisasi Penulisan Nomor HP:**
   - *Bagaimana penulisan nomor HP wali di buku/arsip TU saat ini (diawali `08...`, `+628...`, atau sering menggunakan strip/spasi seperti `0812-3456-7890`)?*
2. **Ketersediaan WhatsApp Wali:**
   - *Berapa perkiraan persentase wali santri yang aktif menggunakan WhatsApp di smartphone?*
   - *Bagaimana penanganan wali santri yang sepuh/tidak memiliki smartphone (apakah menggunakan nomor pengurus/wali asrama)?*

#### 💻 Implikasi Teknis & Database SIPAS-Hub:
* **Sanitizer Otomatis:** Sistem backend otomatis mengonversi format awalan `08...` menjadi format WhatsApp internasional `628...`.

---

### 1.3. Siklus Kenaikan Kelas, Kelulusan, & Santri Mutasi
> **Pertanyaan Utama:**  
> *"Bagaimana pihak TU memperlakukan data santri yang tinggal kelas, mutasi pindah pondok di tengah semester, atau santri yang telah lulus?"*

#### 📌 Turunan Pertanyaan Penggali (*Probing Questions*):
1. **Pemisahan Santri Non-Aktif:**
   - *Apakah data santri yang sudah keluar/pindah/lulus sudah dipisahkan dari daftar santri aktif di arsip TU saat ini?*
2. **Otomatisasi Tagihan Santri Mutasi:**
   - *Jika santri berhenti di pertengahan bulan, apakah tagihannya harus dibatalkan agar tidak terbit tagihan baru di bulan berikutnya?*

#### 💻 Implikasi Teknis & Database SIPAS-Hub:
* **Enum Status Santri:** Kolom `status` bernilai `aktif`, `lulus`, `mutasi`, sehingga proses generate tagihan massal hanya menargetkan santri berstatus `aktif`.

---
---

# 💰 LEVEL 2: AUDIT DATA TARIF SPP, TAGIHAN NON-SPP, & KASIR LOKET (Untuk Kasir & Bendahara)

```
                               ┌──────────────────────────────────────────────┐
                               │ Modul 2: Tarif SPP, Tagihan Non-SPP & Kasir  │
                               └──────────────────────┬───────────────────────┘
                                                      │
         ┌────────────────────────┬───────────────────┴────────────────┬────────────────────────┐
         ▼                        ▼                                    ▼                        ▼
┌───────────────────┐    ┌───────────────────┐                ┌───────────────────┐    ┌───────────────────┐
│ 2.1 Kondisi Data  │    │ 2.2 Akun Kasir &  │                │ 2.3 Cicilan       │    │ 2.4 Saldo Awal    │
│     SPP & Non-SPP │    │     Struk Thermal │                │     Tagihan Non-SP│    │     Tunggakan Lalu│
└───────────────────┘    └───────────────────┘                └───────────────────┘    └───────────────────┘
```

---

### 2.1. Kondisi & Format Data Tagihan SPP serta Tagihan Non-SPP Eksisting
> **Pertanyaan Investigasi Awal:**  
> *"Bagaimana kondisi dan rincian data tarif SPP bulanan serta Tagihan Non-SPP yang saat ini berlaku di pesantren? Apa saja jenis tagihan non-SPP yang wajib dibayar santri (Uang Gedung/Pembangunan, Daftar Ulang, Uang Asrama/Makan, Ujian, Seragam/Buku) dan bagaimana struktur tarifnya?"*

#### 🔍 Latar Belakang & Urgensi
SIPAS-Hub mengelola dua kategori besar iuran: **Iuran Rutin Bulanan (SPP/Uang Makan)** dan **Tagihan Non-SPP (Uang Gedung, Daftar Ulang, Kegiatan)**. Pemetaan jenis tarif dan periode penagihan memastikan seluruh pos pemasukan tercatat rapi.

#### 📌 Turunan Pertanyaan Penggali (*Probing Questions*):
1. **Daftar Lengkap Tagihan Non-SPP:**
   - *Sebutkan seluruh jenis tagihan non-SPP yang ada di pesantren:*
     - *Uang Pembangunan / Gedung (Nominal & Periode Bayar).*
     - *Biaya Pendaftaran Ulang / Masuk Santri Baru.*
     - *Iuran Asrama / Uang Makan Bulanan.*
     - *Biaya Ujian / Kitab / Seragam.*
2. **Karakteristik Penagihan Pos Non-SPP:**
   - *Apakah tagihan non-SPP ditagihkan sekali bayar selama mondok (`one_time`), tahunan (`annual`), atau bertahap per semester?*
   - *Apakah besaran SPP sama rata untuk seluruh tingkatan atau santri baru memiliki tarif berbeda dari santri lama?*

#### 💻 Implikasi Teknis & Database SIPAS-Hub:
* **Tabel `fee_types`:** Master jenis tagihan dengan atribut `name`, `amount`, `type` (`monthly`, `annual`, `one_time`), dan `academic_year_id`.

---

### 2.2. Akun Petugas Kasir, Struk Fisik Thermal, & Rekap Kas Harian
> **Pertanyaan Utama:**  
> *"Bagaimana operasional penerimaan kas di loket saat ini? Siapa saja staf yang melayani loket dan apakah loket menggunakan printer kasir mini (thermal 58mm/80mm) untuk bukti bayar fisik?"*

#### 📌 Turunan Pertanyaan Penggali (*Probing Questions*):
1. **Jumlah Petugas Loket Kasir:**
   - *Berapa staf yang bertugas menerima uang tunai di loket? Apakah mereka memerlukan akun login terpisah untuk audit harian?*
2. **Kebutuhan Bukti Cetak Struk:**
   - *Apakah loket membutuhkan cetak struk kasir mini (thermal 58mm/80mm) yang cepat dan hemat kertas, atau cetak kuitansi ukuran PDF A4?*
   - *Jika wali santri membayar SPP + Uang Gedung sekaligus, apakah struk dicetak gabungan dalam 1 lembar?*

#### 💻 Implikasi Teknis & Database SIPAS-Hub:
* **Audit Kasir & Print Layout:** Pencatatan `created_by` / `cashier_id` pada tabel `payments` dan layout CSS print khusus printer thermal 58mm/80mm.

---

### 2.3. Kebijakan Pembayaran Parsial (Cicilan Tagihan Non-SPP) & Pembayaran di Muka
> **Pertanyaan Utama:**  
> *"Bagaimana perlakuan pencatatan jika ada wali santri yang mencicil tagihan bernilai besar (misal: Uang Gedung Rp 2.000.000 dicicil 4x), atau sebaliknya membayar SPP 6 bulan sekaligus di muka?"*

#### 📌 Turunan Pertanyaan Penggali (*Probing Questions*):
1. **Skema Cicilan Tagihan Non-SPP:**
   - *Apakah tagihan seperti Uang Gedung/Daftar Ulang boleh dicicil bertahap dan apakah kuitansi sementara diterbitkan setiap kali cicilan masuk?*
2. **Pembayaran SPP di Muka:**
   - *Jika wali membayar SPP beberapa bulan ke depan, apakah sistem langsung menerbitkan invoice bulan-bulan tersebut dan menandainya lunas?*

#### 💻 Implikasi Teknis & Database SIPAS-Hub:
* **Status `partial` & Multi-Payment:** Tabel `invoices` mendukung status `unpaid`, `partial`, `paid`, di mana satu tagihan dapat menampung beberapa riwayat cicilan `payments`.

---

### 2.4. Rekap Saldo Tunggakan Tahun Lalu & Skema Keringanan/Diskon
> **Pertanyaan Utama:**  
> *"Bagaimana kondisi data tunggakan/piutang santri dari tahun-tahun sebelumnya yang masih tercatat di buku bendahara saat ini? Dan apakah ada kebijakan tarif khusus (diskon santri yatim, anak staf, beasiswa, atau potongan saudara kandung)?"*

#### 📌 Turunan Pertanyaan Penggali (*Probing Questions*):
1. **Format Data Tunggakan Lama:**
   - *Apakah data saldo tunggakan lama tercatat rinci per bulan atau angka total gelondongan per santri?*
2. **Kategori Keringanan Tarif / Diskon:**
   - *Apa saja kategori keringanan yang ada (misal: Santri Yatim gratis 100%, Anak Guru diskon 50%, Potongan Saudara Kandung / Sibling Discount)?*

#### 💻 Implikasi Teknis & Database SIPAS-Hub:
* **Master Saldo Awal & Diskon:** Pembuatan pos tagihan `Saldo Awal Tunggakan` di tabel `fee_types` dan integrasi tabel relasi `student_discounts`.

---
---

# 🌐 LEVEL 3: KANAL PEMBAYARAN ONLINE (MIDTRANS), REKENING BANK & WA (Untuk Bendahara & Humas)

```
                               ┌──────────────────────────────────────────────┐
                               │ Modul 3: Midtrans, Rekening Bank, & WA Blast │
                               └──────────────────────┬───────────────────────┘
                                                      │
         ┌────────────────────────┬───────────────────┴────────────────┬────────────────────────┐
         ▼                        ▼                                    ▼                        ▼
┌───────────────────┐    ┌───────────────────┐                ┌───────────────────┐    ┌───────────────────┐
│ 3.1 Kondisi Data  │    │ 3.2 Midtrans Fee  │                │ 3.2 Pilihan Salur-│    │ 3.3 Jadwal WA     │
│     Rekening Bank │    │     Sharing Rule  │                │     an Bayar VA/QR│    │     Reminder SPP  │
└───────────────────┘    └───────────────────┘                └───────────────────┘    └───────────────────┘
```

---

### 3.1. Kondisi Data Rekening Bank Yayasan & Alur Verifikasi Transfer Manual
> **Pertanyaan Investigasi Awal:**  
> *"Bagaimana kondisi dan daftar nomor rekening bank resmi yayasan yang saat ini digunakan untuk menerima transfer manual dari wali santri? Bagaimana proses bendahara memeriksa dan menyetujui mutasi transfer yang masuk?"*

#### 📌 Turunan Pertanyaan Penggali (*Probing Questions*):
1. **Daftar Rekening Bank Penampung:**
   - *Berapa nomor rekening bank resmi yang aktif (Nama Bank, Nomor Rekening, dan Atas Nama Rekening)?*
   - *Apakah rekening penerimaan SPP terpisah dengan rekening penerimaan Uang Gedung/Pembangunan?*
2. **Alur Verifikasi Bukti Bayar (*Approval Flow*):**
   - *Bagaimana bendahara memverifikasi bukti transfer yang diunggah wali santri (apakah mencocokkan lewat m-Banking/Internet Banking)?*
   - *Berapa batas waktu maksimal verifikasi bukti bayar (misal: maksimal 1x24 jam)?*

#### 💻 Implikasi Teknis & Database SIPAS-Hub:
* **Tabel `bank_accounts` & Kompresi Bukti WebP:** Menampilkan rekening bank dinamis di modal bayar dan menyimpan bukti transfer yang otomatis dikompresi ke WebP 75%.

---

### 3.2. Payment Gateway Online (Midtrans Snap), Skema Biaya Admin, & Pilihan Saluran Bayar
> **Pertanyaan Utama:**  
> *"Jika orang tua membayar secara instan via Payment Gateway (QRIS atau Virtual Account Bank), bagaimana kebijakan pembebanan biaya admin transaksi (MDR/Fee) dan saluran pembayaran apa saja yang ingin dibuka untuk wali santri?"*

#### 🔍 Latar Belakang & Urgensi
Integrasi Midtrans Snap memungkinkan pembayaran otomatis terverifikasi tanpa perlu dicek manual oleh bendahara. Kebijakan biaya transaksi (apakah dibebankan ke wali santri atau disubsidi yayasan) harus disepakati di awal.

#### 📌 Turunan Pertanyaan Penggali (*Probing Questions*):
1. **Skema Beban Biaya Admin (*Fee Sharing Policy*):**
   - *Apakah biaya transaksi (misal QRIS 0,7% atau Virtual Account Rp 2.000–Rp 4.000) ditambahkan ke tagihan wali santri, atau dipotong dari penerimaan bersih yayasan?*
   - *Apakah biaya admin wajib tertera terpisah di kuitansi resmi?*
2. **Pilihan Saluran Pembayaran (*Payment Channels*):**
   - *Saluran apa saja yang wajib dibuka: Apakah QRIS (semua e-wallet & mobile banking), Virtual Account (BSI, BCA, BRI, BNI, Mandiri), atau gerai retail (Indomaret/Alfamart)?*

#### 💻 Implikasi Teknis & Database SIPAS-Hub:
* **Konfigurasi Midtrans & Payload Snap:** Parameter `convenience_fee` pada payload transaksi dan filter `enabled_payments` di konfigurasi backend.

---

### 3.3. Jadwal & Format Notifikasi Pengingat Tagihan WhatsApp (Broadcast)
> **Pertanyaan Utama:**  
> *"Kapan jadwal pesan pengingat tagihan otomatis sebaiknya dikirimkan ke nomor WhatsApp orang tua (misal: saat tagihan baru terbit, H-3 sebelum jatuh tempo, dan saat pembayaran berhasil dikonfirmasi)?"*

#### 📌 Turunan Pertanyaan Penggali (*Probing Questions*):
1. **Pemicu Notifikasi WA:**
   - *Kapan pesan WA wajib terkirim (Tagihan Baru Terbit, Pengingat Jatuh Tempo, Notifikasi Lunas & Link Kwitansi Digital)?*
2. **Etika Jam Pengiriman:**
   - *Jam berapa batasan waktu broadcast pesan otomatis yang diperbolehkan (misal: hanya boleh pukul 09.00 – 16.00 WIB)?*

---
---

# 📉 LEVEL 4: AUDIT ARUS KAS & KEBUTUHAN LAPORAN KEUANGAN/TUNGGAKAN (Untuk Bendahara & Yayasan)

> [!NOTE]
> **Batasan Ruang Lingkup Sistem (Scope Boundary):**  
> Sistem SIPAS-Hub saat ini fokus 100% pada **Penerimaan Pembayaran, Billing, & Rekap Laporan Piutang (Fase 1)**. Modul pengeluaran kas operasional dan kas kecil pada subbab 4.3 & 4.4 diposisikan sebagai **Rencana Pengembangan Fase 2 (Future Scope)** agar sistem tidak menjadi rumit saat awal rilis.

```
                               ┌──────────────────────────────────────────────┐
                               │  Modul 4: Arus Kas & Laporan Keuangan/Tungg. │
                               └──────────────────────┬───────────────────────┘
                                                      │
         ┌────────────────────────┬───────────────────┼───────────────────┬────────────────────────┐
         ▼                        ▼                   ▼                   ▼                        ▼
┌───────────────────┐    ┌───────────────────┐┌───────────────────┐┌───────────────────┐    ┌───────────────────┐
│ 4.1 Alur Catat    │    │ 4.2 Format Laporan││ 4.2 Rincian Santri││ 4.3 Pos Beban     │    │ 4.4 Kas Kecil &   │
│     Masuk-Keluar  │    │     Keuangan Wajib││     Menunggak     ││     [Fase 2 / GAP]│    │     Kas Bon (LPJ) │
└───────────────────┘    └───────────────────┘└───────────────────┘└───────────────────┘    └───────────────────┘
```

---

### 4.1. Kondisi Data & Alur Pencatatan Uang Masuk dan Keluar Saat Ini (*As-Is Cash Recording Flow*)
> **Pertanyaan Investigasi Awal:**  
> *"Bagaimana persisnya alur dan kebiasaan pencatatan uang masuk dan uang keluar yang berjalan dari hari ke hari di pesantren saat ini? Siapa yang mencatat transaksi harian, apakah dicatat di buku nota fisik, file Excel Buku Kas Umum (BKU) gabungan Masuk-Keluar-Saldo, atau file terpisah, dan bagaimana alur fisik uang kas diserahkan ke bendahara yayasan?"*

#### 🔍 Latar Belakang & Urgensi
Mengetahui titik awal alur pencatatan harian kasir/bendahara membantu memastikan sistem baru tidak mengubah kebiasaan alur uang fisik lembaga secara drastis.

#### 📌 Turunan Pertanyaan Penggali (*Probing Questions*):
1. **Kebiasaan Pencatatan Kasir Harian:**
   - *Apakah setiap transaksi penerimaan langsung dicatat di Excel pada saat wali santri membayar di loket, atau dikumpulkan di buku nota tulis tangan terlebih dahulu lalu direkap di sore hari?*
   - *Apakah pencatatan uang keluar untuk belanja operasional dicatat di lembar/file yang sama tepat di bawah uang masuk secara urut tanggal (Buku Kas Umum kronologis)?*
2. **Alur Serah Terima Fisik Kas:**
   - *Kapan kasir loket menyetorkan uang tunai fisik ke bendahara utama/brankas yayasan (setiap jam 14.00, setiap sore, atau mingguan)?*
   - *Apakah ada buku tanda terima serah terima kas fisik harian?*

---

### 4.2. Komponen Wajib Laporan Keuangan & Kebutuhan Laporan Rinci Santri Menunggak
> **Pertanyaan Utama:**  
> *"Informasi apa saja yang wajib tersaji di dalam Laporan Keuangan bulanan untuk Pimpinan/Yayasan? Dan apakah di laporan keuangan perlu menampilkan **laporan detail santri-santri yang menunggak** (rinci per nama santri, kelas, dan bulan apa saja yang belum lunas), atau cukup disajikan berupa angka total saldo piutang gelondongan?"*

#### 🔍 Latar Belakang & Urgensi
Pimpinan yayasan membutuhkan dua sudut pandang: **Laporan Rekapitulasi Eksekutif** (Total Uang Masuk, Total Tunggakan Global) untuk melihat kesehatan finansial lembaga, dan **Laporan Rincian Tunggakan Operasional** untuk penagihan wali kelas kepada orang tua santri.

#### 📌 Turunan Pertanyaan Penggali (*Probing Questions*):
1. **Komponen Wajib Laporan Keuangan Lembaga:**
   - *Komponen tabel apa saja yang wajib dilihat oleh Pimpinan Yayasan setiap bulannya?*
     - *Total Realisasi Penerimaan SPP & Non-SPP (Uang Gedung, Asrama, Ujian).*
     - *Rekapitulasi Penerimaan berdasarkan Metode Pembayaran (Berapa Tunai Kasir Loket, Berapa Transfer Bank).*
     - *Total Piutang / Tunggakan yang Belum Tertagih.*
2. **Tingkat Kedalaman Laporan Santri Menunggak (*Arrears Breakdown*):**
   - *Apakah pimpinan/wali kelas membutuhkan cetak lembar rincian per kelas yang memuat daftar nama santri, nomor WhatsApp wali, dan daftar bulan yang belum dibayar?*
   - *Kapan laporan daftar santri menunggak ini biasanya ditarik (setiap tanggal cut-off bulanan atau menjelang pekan ujian semester)?*
3. **Format Dokumen Laporan yang Diharapkan:**
   - *Apakah laporan cukup dilihat secara real-time di Dashboard SIPAS-Hub atau wajib bisa di-ekspor/dicetak dalam format **PDF Resmi bertanda tangan** dan **File Excel (`.xlsx`)**?*

#### 💻 Implikasi Teknis & Database SIPAS-Hub:
* **Halaman Analitik & Laporan Keuangan:** View Livewire yang menyajikan rekapitulasi omset penerimaan bulanan, filter rentang tanggal, serta tombol ekspor Laporan Tunggakan Rinci per Kelas ke format Excel/PDF.

---

### 4.3. Pos Kategori Beban Operasional Lembaga & Batas Pagu Anggaran [Fase 2 / GAP]
> **Pertanyaan Utama:**  
> *"Apa saja kategori pos beban pengeluaran rutin bulanan di pesantren (Gaji Guru/Staf, Dapur Santri, Listrik/PLN, ATK/Ujian, Pemeliharaan Gedung) dan apakah ada batas pagu anggarannya?"*

---

### 4.4. Kas Kecil (*Petty Cash*) & Pertanggungjawaban Kas Bon (*Cash Advance / LPJ*) [Fase 2 / GAP]
> **Pertanyaan Utama:**  
> *"Bagaimana pengelolaan uang kas kecil di loket (apakah sistem dana tetap yang diisi ulang sebesar nota riil) dan bagaimana aturan batas waktu penyerahan nota LPJ kas bon bagi staf/guru yang belanja kegiatan?"*

---
---

# 🚀 LEVEL 5: STRATEGI TRANSISI, ROADMAP ROLLOUT, & CHECKLIST EXCEL (Untuk Seluruh Tim)

```
                               ┌──────────────────────────────────────────────┐
                               │  Modul 5: Transisi & Checklist Excel DB      │
                               └──────────────────────┬───────────────────────┘
                                                      │
         ┌────────────────────────┬───────────────────┴────────────────┬────────────────────────┐
         ▼                        ▼                                    ▼                        ▼
┌───────────────────┐    ┌───────────────────┐                ┌───────────────────┐    ┌───────────────────┐
│ 5.1 Masa Transisi │    │ 5.2 Roadmap       │                │ 5.3 SOP Darurat   │    │ 5.4 Checklist 5   │
│     Parallel Run  │    │     4 Tahap       │                │     Offline Loket │    │     File Master DB│
└───────────────────┘    └───────────────────┘                └───────────────────┘    └───────────────────┘
```

---

### 5.1. Masa Transisi Pembukuan Ganda (*Parallel Run 30 Hari*)
> **Pertanyaan Utama:**  
> *"Apakah pihak bendahara dan kasir siap menjalankan masa transisi pembukuan ganda (Parallel Run) selama 30 hari (1 siklus penagihan), di mana kasir menginput transaksi di SIPAS-Hub sekaligus tetap membuat rekap cadangan di Excel hingga datanya 100% klop?"*

---

### 5.2. Roadmap Peluncuran Bertahap (4 Tahap)
```
┌─────────────────────────┐      ┌─────────────────────────┐      ┌─────────────────────────┐      ┌─────────────────────────┐
│ TAHAP 1: MINGGU 1-2     │      │ TAHAP 2: BULAN KE-1     │      │ TAHAP 3: BULAN KE-2     │      │ TAHAP 4: BULAN KE-3     │
├─────────────────────────┤      ├─────────────────────────┤      ├─────────────────────────┤      ├─────────────────────────┤
│ • Impor Master Data     │ ───► │ • Kasir Loket Offline   │ ───► │ • Pilot Class Portal    │ ───► │ • Full Go-Live          │
│ • Pelatihan Staf Kasir  │      │ • Cetak Struk Thermal   │      │ • Transfer Manual       │      │ • Semua Santri & Rombel │
│ • Verifikasi Saldo Awal │      │ • Parallel Run Excel    │      │ • Notifikasi WA Pilot   │      │ • Stop File Excel Harian│
└─────────────────────────┘      └─────────────────────────┘      └─────────────────────────┘      └─────────────────────────┘
```

---

### 5.3. Checklist 5 File Excel Kunci & Pencocokan Kolom Database (*Database Mapping*)

Berikut adalah daftar 5 file data Excel yang perlu disiapkan oleh pihak pesantren untuk dicocokkan ke database SIPAS-Hub:

| No | File Excel yang Dibutuhkan | Tabel Database Target | Kolom-Kolom Kunci yang Wajib Tersedia |
|:---|:---|:---|:---|
| **1** | **Master Data Santri & Wali** | `students`<br>`school_classes` | • **NIS** (Unik)<br>• **Nama Santri**<br>• **Kelas/Rombel** (Contoh: `VII A`)<br>• **Jenis Kelamin** (`L`/`P`)<br>• **Nama Wali Santri**<br>• **No. WhatsApp Wali** (Format: `08...`) |
| **2** | **Master Tarif SPP & Tagihan Non-SPP** | `fee_types` | • **Nama Tagihan** (SPP, Uang Gedung, Daftar Ulang, Uang Makan)<br>• **Nominal Tarif** (Angka murni)<br>• **Tipe Periode** (`monthly` / `annual` / `one_time`) |
| **3** | **Rekap Tunggakan Lalu (Saldo Awal)** | `invoices` (Saldo Awal) | • **NIS Santri**<br>• **Jenis Tagihan Menunggak**<br>• **Total Sisa Piutang**<br>• **Keterangan Periode Bulan** |
| **4** | **Data Rekening Bank Yayasan** | `bank_accounts` | • **Nama Bank** (BSI, BRI, Mandiri, dll)<br>• **Nomor Rekening**<br>• **Atas Nama Rekening** |
| **5** | **Data Akun Petugas Loket & Staf** | `users` | • **Nama Petugas**<br>• **Email Login**<br>• **Peran / Role** (`kasir`, `bendahara`, `tu`, `yayasan`) |

---
---

## 📊 Matriks Kesimpulan Analisis Fit-Gap Lengkap (Untuk Diisi Pasca Wawancara)

| Level / Modul | Parameter Kebutuhan | Kondisi Eksisting Sekolah (As-Is) | Solusi Sistem SIPAS-Hub (To-Be) | Status (Fit / Gap) | Rekomendasi Tindak Lanjut |
|:---|:---|:---|:---|:---|:---|
| **L1. Data Santri** | Kelengkapan Data Santri & Wali | *[Catat hasil wawancara]* | Kolom Standar `students` & Wali | `[ ] Fit  [ ] Gap` | Validasi kelengkapan NIS & No WA |
| **L1. Data Santri** | Standarisasi Kontak WA | *[Catat hasil wawancara]* | Auto-Sanitize Awalan `628...` | `[ ] Fit  [ ] Gap` | Konversi otomatis nomor HP di sistem |
| **L1. Data Santri** | Mutasi & Kelulusan | *[Catat hasil wawancara]* | Status Enum + Scoping Tagihan Aktif | `[ ] Fit  [ ] Gap` | Pengecualian santri non-aktif |
| **L2. Kasir Loket** | Multi-Akun & Rekap Kas Harian | *[Catat hasil wawancara]* | Audit Trail `cashier_id` + Laporan Harian | `[ ] Fit  [ ] Gap` | Pisahkan role Kasir vs Bendahara |
| **L2. Kasir Loket** | Struk Kasir Cetak Thermal | *[Catat hasil wawancara]* | CSS Print 58mm/80mm Thermal & PDF A4 | `[ ] Fit  [ ] Gap` | Tombol cetak struk thermal di loket |
| **L2. Tagihan Non-SPP**| Cicilan Biaya Gedung/Daftar Ulang| *[Catat hasil wawancara]* | Multi-Payment per Invoice (Status Partial)| `[ ] Fit  [ ] Gap` | Dukungan cicilan tagihan non-SPP |
| **L2. Tagihan Non-SPP**| Saldo Awal Tunggakan Lalu | *[Catat hasil wawancara]* | Pos Tagihan Khusus "Saldo Awal Tunggakan" | `[ ] Fit  [ ] Gap` | Impor data tunggakan lama |
| **L2. Tagihan Non-SPP**| Kategori Diskon & Keringanan | *[Catat hasil wawancara]* | Master `student_discounts` Dinamis | `[ ] Fit  [ ] Gap` | Auto-potong tagihan anak yatim/staf |
| **L3. Kanal Bayar** | Rekening Bank Penampung | *[Catat hasil wawancara]* | Master `bank_accounts` + Dynamic UI | `[ ] Fit  [ ] Gap` | Tampilkan rekening bank di modal transfer |
| **L3. Kanal Bayar** | Verifikasi Bukti Transfer | *[Catat hasil wawancara]* | Uploader Bukti Bayar WebP Kompresi 75% | `[ ] Fit  [ ] Gap` | Approval transfer manual 1-klik |
| **L3. Kanal Bayar** | Payment Gateway: Biaya Admin | *[Catat hasil wawancara]* | Fee Sharing Policy di Payload Midtrans | `[ ] Fit  [ ] Gap` | Atur penanggung biaya admin transaksi |
| **L3. Kanal Bayar** | Payment Gateway: Saluran Bayar| *[Catat hasil wawancara]* | Filter `enabled_payments` (QRIS, Bank VA)| `[ ] Fit  [ ] Gap` | Aktifkan QRIS & Bank VA pilihan |
| **L3. Kanal Bayar** | Broadcast WA Pengingat SPP | *[Catat hasil wawancara]* | Scheduled Cron Job + Template Dinamis | `[ ] Fit  [ ] Gap` | Reminder otomatis tagihan bulanan |
| **L4. Laporan & Kas**| Alur Catat Masuk-Keluar As-Is | *[Catat hasil wawancara]* | Integrasi Kasir Loket & Laporan Terpusat | `[ ] Fit  [ ] Gap` | Standarisasi jam serah terima kas |
| **L4. Laporan & Kas**| Laporan Rinci Santri Menunggak| *[Catat hasil wawancara]* | Filter Tunggakan per Kelas & Export PDF/XLS| `[ ] Fit  [ ] Gap` | Halaman rekap piutang rinci per rombel |
| **L4. [F2] Pengeluaran**| Pos Beban Operasional & Budget | *[Catat hasil wawancara]* | Roadmap Fase 2: Master Expense Categories| `[x] GAP (Fase 2)` | Ditunda ke rilis lanjutan |
| **L4. [F2] Pengeluaran**| Kas Kecil & Kas Bon (LPJ) | *[Catat hasil wawancara]* | Roadmap Fase 2: Petty Cash Module | `[x] GAP (Fase 2)` | Ditunda ke rilis lanjutan |
| **L5. Transisi** | Parallel Run (Pembukuan Ganda)| *[Catat hasil wawancara]* | Masa Transisi 30 Hari Pencatatan Ganda | `[ ] Fit  [ ] Gap` | Uji konsistensi saldo 100% klop |
| **L5. Transisi** | Roadmap Rollout 4 Tahap | *[Catat hasil wawancara]* | Tahap 1 s/d 4 (Internal -> Pilot -> Go-Live)| `[ ] Fit  [ ] Gap` | Mulai pilot dari santri baru |
| **L5. Data Mapping** | Kesiapan 5 File Master Excel | *[Catat hasil wawancara]* | 5 Skema Tabel Master SIPAS-Hub Lengkap | `[ ] Fit  [ ] Gap` | Berikan format template kosong ke TU/Kasir |
