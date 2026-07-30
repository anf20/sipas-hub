# 📸 PANDUAN PENGAMBILAN SCREENSHOT (TANGKAPAN LAYAR) UNTUK PROPOSAL
*Dokumen ini membantu Anda mengambil tangkapan layar (screenshot) dari aplikasi lokal (`127.0.0.1:8000`) untuk dilampirkan ke dalam proposal pondok pesantren.*

---

## 📱 BAGIAN A: PORTAL MANDIRI ORANG TUA (MOBILE-FIRST)

### 1. Dashboard Utama Wali Santri
*   **Judul / Tagline:** **Portal Mandiri Wali Santri — "Informasi Tagihan Anak dalam Satu Genggaman"**
*   **Penjelasan Singkat:** Halaman utama bagi wali santri untuk memantau status iuran anak mereka secara transparan. Menampilkan ringkasan total tagihan belum dibayar, riwayat pembayaran terakhir, dan pintasan menu pembayaran instan.
*   **Cara Mengambil Screenshot:**
    1.  Login ke aplikasi sebagai wali santri (contoh akun: email `wali1@test.com`, password `password`).
    2.  Buka menu dashboard orang tua di url `/parent/dashboard`.
    3.  Gunakan fitur "Inspect Element" di browser (F12) lalu aktifkan mode **Responsive Device / Toggle Device Toolbar** (pilih ukuran layar HP seperti iPhone 12/14 atau Samsung Galaxy) agar tampilan menjadi portrait/mobile.
    4.  Ambil screenshot tampilan HP tersebut.

### 2. Form Pembayaran Tagihan Anak (List & Status Iuran)
*   **Judul / Tagline:** **Daftar Tagihan Santri — "Pantau Semua Iuran Tanpa Ada yang Terlewat"**
*   **Penjelasan Singkat:** Halaman khusus yang menyajikan rincian seluruh tagihan aktif (SPP Bulanan, Uang Buku, Iuran Kegiatan) beserta status pelunasannya untuk setiap anak. Orang tua cukup mencentang iuran yang ingin dibayar lalu menekan tombol "Bayar Online".
*   **Cara Mengambil Screenshot:**
    1.  Dalam posisi login wali santri di HP view, masuk ke url `/parent/invoices`.
    2.  Ambil screenshot daftar iuran yang berstatus "Belum Dibayar" lengkap dengan tombol centang pembayaran.

### 3. Jendela Pembayaran Instan (Midtrans Snap Checkout)
*   **Judul / Tagline:** **Gerbang Pembayaran Instan — "Bayar Mudah dengan QRIS, Virtual Account, & E-Wallet"**
*   **Penjelasan Singkat:** Integrasi Payment Gateway Midtrans (Snap Checkout) yang otomatis terbuka saat orang tua membayar online. Wali santri dapat memilih transfer bank (Virtual Account BCA/BRI/Mandiri/BNI) atau memindai kode QRIS secara langsung dari e-wallet (Gopay/OVO/Dana/LinkAja).
*   **Cara Mengambil Screenshot:**
    1.  Dari halaman `/parent/invoices` wali santri, klik tombol **"Bayar Online"** pada salah satu tagihan belum dibayar.
    2.  Tunggu hingga pop-up / modal Midtrans berwarna putih muncul di layar.
    3.  Ambil screenshot pop-up pembayaran tersebut yang memuat logo perbankan dan pilihan QRIS.

### 4. Bukti Kuitansi PDF Digital
*   **Judul / Tagline:** **Kuitansi Pembayaran Digital — "Bukti Bayar Resmi Instan Tanpa Antre"**
*   **Penjelasan Singkat:** Dokumen bukti pembayaran (PDF) yang dibuat secara otomatis oleh sistem saat transaksi sukses. Format kuitansi telah disesuaikan dengan standar keuangan pondok pesantren lengkap dengan nomor kuitansi unik dan nominal terbilang otomatis.
*   **Cara Mengambil Screenshot:**
    1.  Masuk ke menu riwayat pembayaran orang tua, lalu klik "Unduh Kuitansi" pada transaksi yang berstatus "Lunas".
    2.  Buka file PDF hasil unduhan tersebut di browser atau PDF viewer.
    3.  Ambil screenshot seluruh halaman PDF kuitansi yang bersih dan rapi tersebut.

---

## 💻 BAGIAN B: HUB PENGELOLA & BENDAHARA (DESKTOP VIEW)

### 5. Pusat Kendali Keuangan (Admin Finance Hub)
*   **Judul / Tagline:** **Dashboard Utama Keuangan — "Pantau Aliran Kas dan Piutang Pesantren Real-Time"**
*   **Penjelasan Singkat:** Dashboard analisis bagi bendahara dan pimpinan pondok untuk memantau pendapatan bulanan, melacak total tunggakan iuran santri yang belum terbayar, serta memantau rasio keberhasilan penagihan tiap angkatan secara visual.
*   **Cara Mengambil Screenshot:**
    1.  Login menggunakan akun Super Admin (email `admin@test.com`, password `password`).
    2.  Masuk ke halaman `/finance`. Pastikan dalam tampilan layar PC/Laptop penuh (bukan mode mobile).
    3.  Ambil screenshot halaman dashboard utama tersebut yang memuat statistik ringkasan kartu data.

### 6. Kasir Pembayaran Tunai (Manual Payment)
*   **Judul / Tagline:** **Kasir Iuran Manual — "Akomodasi Pembayaran Tunai di Pondok yang Terintegrasi"**
*   **Penjelasan Singkat:** Halaman kasir bagi wali santri yang datang langsung ke pesantren membawa uang tunai. Bendahara cukup mengetik nama santri, memilih tagihan iuran yang dibayar di tempat, dan sistem otomatis mencatat pembayaran tunai tersebut agar laporan kasir dan online menyatu secara harmonis.
*   **Cara Mengambil Screenshot:**
    1.  Dalam posisi login Admin/Bendahara, masuk ke menu **Pembayaran Manual** di url `/finance/invoices/manual-payment`.
    2.  Pilih salah satu nama santri hasil seeder (contoh: cari nama santri Islami yang ada di sistem).
    3.  Biarkan daftar tagihan santri tersebut muncul, lalu ambil screenshot halaman kasir ini.

### 7. Manajemen Penjadwalan SPP Bulanan
*   **Judul / Tagline:** **Manajemen Iuran SPP — "Generasi Tagihan Bulanan Otomatis Sekali Klik"**
*   **Penjelasan Singkat:** Fitur bagi bendahara untuk menjadwalkan nominal iuran bulanan (SPP) secara massal per kelas dengan satu tombol di awal bulan. Fitur ini memangkas waktu kerja administrasi penulisan tagihan santri secara manual.
*   **Cara Mengambil Screenshot:**
    1.  Dalam posisi login Admin/Bendahara, buka menu **Manajemen SPP** di url `/finance/spp`.
    2.  Ambil screenshot formulir pembuatan SPP bulanan yang berisi isian bulan, tahun ajaran, dan nominal iuran default.

### 8. Pusat Pengiriman Broadcast Pengumuman WhatsApp
*   **Judul / Tagline:** **Broadcast WhatsApp Massal — "Direct Messaging Informasi Penting & Maklumat Umum"**
*   **Penjelasan Singkat:** Layanan penyiaran pesan WhatsApp massal secara direct ke HP wali santri. Memudahkan bendahara mengirimkan pemberitahuan (seperti pengumuman libur pondok, rapat wali santri, dsb.) secara masal kepada semua wali murid atau disaring per kelas.
*   **Cara Mengambil Screenshot:**
    1.  Dalam posisi login Admin/Bendahara, klik menu **Broadcast Pengumuman** di url `/finance/whatsapp-broadcast`.
    2.  Ketik pesan contoh pada textarea dan klik pilihan target "Filter Berdasarkan Kelas" agar memunculkan dropdown pilihan kelas.
    3.  Ambil screenshot halaman ini beserta tabel riwayat pengumuman di bawahnya.

---

## 🔒 BAGIAN C: PENGATURAN SISTEM & KEAMANAN

### 9. Log Audit Jejak Digital (Audit Trail)
*   **Judul / Tagline:** **Jejak Audit Keamanan Sistem — "Perlindungan & Transparansi Data Finansial dari Manipulasi"**
*   **Penjelasan Singkat:** Fitur audit trail yang mencatat setiap aktivitas sensitif staf (seperti merubah nominal iuran, menghapus data transaksi, atau menambah santri). Mencatat detail nama pelaku, aksi, waktu, perubahan data lama ke baru, serta IP address untuk mencegah kebocoran data.
*   **Cara Mengambil Screenshot:**
    1.  Dalam posisi login Admin/Bendahara, buka menu **Log Audit** di url `/management/audit_logs`.
    2.  Ambil screenshot tabel audit log yang menampilkan rincian aktivitas pengguna sistem secara real-time.

---

## 💡 Tips Mengambil Screenshot yang Profesional:
1.  **Gunakan Data yang Realistis:** Seluruh database telah disegarkan dengan nama wali & santri Islami. Cari nama yang representatif saat mendemonstrasikan halaman Kasir atau Detail Profil agar proposal Anda terlihat sangat natural dan premium.
2.  **Zoom Out Browser (Opsional):** Jika tampilan halaman terlalu panjang ke bawah, Anda bisa menekan tombol `Ctrl` + `-` di browser untuk mengecilkan tampilan (misalnya ke 90% atau 80%) sebelum menekan tombol screenshot (PrintScreen) agar seluruh informasi penting terekam dalam satu bingkai gambar.
3.  **Gunakan Bingkai Mockup (Opsional):** Untuk Portal Wali Santri (Tampilan HP), Anda dapat memasukkan gambar tangkapan layar tersebut ke dalam bingkai HP (*device frame mockup*) agar terlihat sangat modern di dalam proposal PDF.
