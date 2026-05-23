# Skenario Testing — Sistem Pembayaran Sekolah

**Versi:** 1.0  
**Tanggal:** 2026-05-22  
**Role:** Admin (web desktop) · Orang tua (mobile)  
**Payment gateway:** Midtrans (Virtual Account + QRIS)  
**Pembayaran manual:** Konfirmasi langsung oleh admin  

---

## Ringkasan

| Kategori | Jumlah Skenario |
|---|---|
| Autentikasi & akses | 6 |
| Manajemen tagihan (admin) | 8 |
| Pembayaran Midtrans | 11 |
| Pembayaran manual (admin) | 5 |
| Laporan & rekap (admin) | 5 |
| **Total** | **35** |

**Prioritas tinggi:** 21 · **Sedang:** 11 · **Rendah:** 3

---

## Keterangan Kolom

| Kolom | Keterangan |
|---|---|
| ID | Kode unik skenario |
| Prioritas | Tinggi / Sedang / Rendah |
| Tipe | Functional, Negative, Security, Integration, Edge case, UI/UX |
| Role | Admin, Orang tua |
| Status | Diisi saat eksekusi: ✅ Pass / ❌ Fail / ⏭️ Skip |
| Catatan | Temuan bug atau komentar |

---

## 1. Autentikasi & Akses

| ID | Judul | Skenario / Expected Result | Prioritas | Tipe | Role | Status | Catatan |
|---|---|---|---|---|---|---|---|
| TC-A01 | Login admin — kredensial valid | Email & password benar → berhasil masuk ke dashboard admin | Tinggi | Functional | Admin | | |
| TC-A02 | Login admin — password salah | Sistem menampilkan pesan error, akun tidak terkunci setelah 3x percobaan | Tinggi | Negative | Admin | | |
| TC-A03 | Login orang tua — kredensial valid | Masuk ke halaman tagihan anak pada tampilan mobile | Tinggi | Functional | Orang tua | | |
| TC-A04 | Login orang tua — akun tidak terdaftar | Sistem menampilkan pesan error yang jelas, tidak ada info sensitif yang bocor | Sedang | Negative | Orang tua | | |
| TC-A05 | Akses halaman admin oleh orang tua | URL admin dibuka paksa oleh session orang tua → redirect / forbidden (403) | Tinggi | Security | Orang tua | | |
| TC-A06 | Session expired saat sedang checkout | Token habis di tengah proses bayar → diarahkan login ulang tanpa kehilangan data tagihan | Sedang | Edge case | Admin, Orang tua | | |

---

## 2. Manajemen Tagihan (Admin)

| ID | Judul | Skenario / Expected Result | Prioritas | Tipe | Role | Status | Catatan |
|---|---|---|---|---|---|---|---|
| TC-T01 | Buat tagihan SPP bulanan | Isi nominal, bulan, pilih kelas/siswa → tagihan tersimpan dan muncul di sisi orang tua | Tinggi | Functional | Admin | | |
| TC-T02 | Buat tagihan custom (non-SPP) | Mis. study tour, seragam — nama bebas, nominal bebas → tersimpan benar | Tinggi | Functional | Admin | | |
| TC-T03 | Buat tagihan dengan nominal nol | Sistem menolak atau meminta konfirmasi sebelum menyimpan | Sedang | Negative | Admin | | |
| TC-T04 | Buat tagihan bulk (satu kelas) | Semua siswa kelas X mendapat tagihan sekaligus — tidak ada yang terlewat | Tinggi | Functional | Admin | | |
| TC-T05 | Edit tagihan yang belum dibayar | Nominal berhasil diubah, perubahan tercermin di halaman orang tua | Sedang | Functional | Admin | | |
| TC-T06 | Edit tagihan yang sudah dibayar | Sistem mencegah atau meminta konfirmasi dengan peringatan jelas | Tinggi | Edge case | Admin | | |
| TC-T07 | Hapus tagihan yang belum dibayar | Tagihan hilang dari list, tidak muncul lagi di sisi orang tua | Sedang | Functional | Admin | | |
| TC-T08 | Tampilan daftar tagihan di mobile (orang tua) | Semua tagihan pending & lunas terbaca dengan baik di layar kecil | Tinggi | UI/UX | Orang tua | | |

---

## 3. Pembayaran Midtrans

> **Catatan:** Gunakan **Midtrans Sandbox** untuk semua test case di bagian ini. Simulasi semua status (success, pending, deny, expire) tersedia tanpa uang sungguhan. Untuk test callback/webhook di local, gunakan ngrok atau deploy ke staging.

| ID | Judul | Skenario / Expected Result | Prioritas | Tipe | Role | Status | Catatan |
|---|---|---|---|---|---|---|---|
| TC-M01 | Pilih Virtual Account BCA | Nomor VA muncul, nominal benar, instruksi pembayaran lengkap | Tinggi | Functional | Orang tua | | |
| TC-M02 | Pilih Virtual Account Mandiri | Kode bayar unik muncul, waktu kadaluarsa ditampilkan | Tinggi | Functional | Orang tua | | |
| TC-M03 | Pilih QRIS | QR code tampil benar di mobile, dapat di-scan dengan aplikasi dompet digital | Tinggi | Functional | Orang tua | | |
| TC-M04 | Callback Midtrans sukses diterima | Setelah bayar, status tagihan berubah dari "belum bayar" ke "lunas" secara otomatis | Tinggi | Integration | — | | |
| TC-M05 | Callback Midtrans pending / tertunda | Status tagihan tetap "pending", orang tua tidak bisa bayar dua kali | Tinggi | Integration | — | | |
| TC-M06 | Callback Midtrans gagal / deny | Status kembali ke "belum bayar", orang tua bisa coba bayar lagi | Tinggi | Integration | — | | |
| TC-M07 | VA kadaluarsa sebelum dibayar | Sistem handle expiry — orang tua bisa generate ulang order tanpa duplikasi tagihan | Tinggi | Edge case | Orang tua | | |
| TC-M08 | Callback Midtrans diterima dua kali (duplicate) | Idempotency check — status tidak berubah dua kali, tidak ada double credit | Tinggi | Security | — | | |
| TC-M09 | Callback dengan signature key yang salah | Sistem menolak callback palsu, status tagihan tidak berubah | Tinggi | Security | — | | |
| TC-M10 | Orang tua kembali ke app setelah bayar VA | Halaman menampilkan status terkini (lunas/pending), tidak stuck di loading | Sedang | UI/UX | Orang tua | | |
| TC-M11 | QRIS ditampilkan di desktop admin | Jika admin preview tagihan, QR tetap terbaca meski bukan use case utama | Rendah | UI/UX | Admin | | |

---

## 4. Pembayaran Manual (Admin)

| ID | Judul | Skenario / Expected Result | Prioritas | Tipe | Role | Status | Catatan |
|---|---|---|---|---|---|---|---|
| TC-P01 | Admin konfirmasi pembayaran manual | Admin pilih tagihan → klik konfirmasi lunas → status berubah ke "lunas" langsung | Tinggi | Functional | Admin | | |
| TC-P02 | Konfirmasi manual pada tagihan yang sudah lunas via Midtrans | Tombol konfirmasi manual tidak tersedia atau menampilkan peringatan duplikat | Tinggi | Negative | Admin | | |
| TC-P03 | Konfirmasi manual — verifikasi data siswa | Data tagihan jelas menampilkan nama + kelas sebelum konfirmasi akhir | Sedang | UI/UX | Admin | | |
| TC-P04 | Riwayat konfirmasi manual tersimpan | Log mencatat siapa admin yang mengkonfirmasi dan kapan (audit trail) | Sedang | Functional | Admin | | |
| TC-P05 | Pembatalan konfirmasi yang sudah dilakukan | Apakah ada fitur reversal? Jika tidak, pastikan ada warning "tidak dapat dibatalkan" sebelum konfirmasi final | Sedang | Edge case | Admin | | |

---

## 5. Laporan & Rekap (Admin)

| ID | Judul | Skenario / Expected Result | Prioritas | Tipe | Role | Status | Catatan |
|---|---|---|---|---|---|---|---|
| TC-L01 | Rekap pembayaran per bulan | Total lunas, total belum bayar, breakdown per kelas ditampilkan benar | Tinggi | Functional | Admin | | |
| TC-L02 | Filter tagihan by status (lunas / belum) | Filter bekerja benar, tidak ada data yang salah masuk kategori | Sedang | Functional | Admin | | |
| TC-L03 | Filter tagihan by kelas atau siswa | Pencarian menampilkan data yang tepat tanpa false positive | Sedang | Functional | Admin | | |
| TC-L04 | Kalkulasi total di laporan | Total di laporan sama dengan sum individual — tidak ada rounding error | Tinggi | Functional | Admin | | |
| TC-L05 | Laporan dengan data kosong | Jika belum ada pembayaran di bulan tertentu, tampil pesan kosong bukan error | Rendah | Edge case | Admin | | |

---

## Catatan Eksekusi

### Lingkungan Testing

| Item | Nilai |
|---|---|
| Environment | Staging / UAT |
| Midtrans mode | Sandbox |
| Browser admin | Chrome / Firefox (desktop) |
| Browser orang tua | Chrome / Safari (mobile) |
| Resolusi mobile | 375px – 430px |

### Alur Eksekusi yang Disarankan

1. Jalankan TC-A01 s/d TC-A06 terlebih dahulu — autentikasi harus jalan sebelum modul lain
2. Lanjut TC-T01 s/d TC-T08 — buat data tagihan yang akan digunakan di modul berikutnya
3. Jalankan TC-M01 s/d TC-M11 — gunakan Midtrans Sandbox
4. Jalankan TC-P01 s/d TC-P05 — konfirmasi manual menggunakan tagihan yang belum dibayar
5. Jalankan TC-L01 s/d TC-L05 — laporan diperiksa setelah ada data pembayaran

### Kriteria Pass / Fail

- **Pass:** Hasil sesuai expected result, tidak ada error pada UI atau console
- **Fail:** Hasil tidak sesuai, error muncul, atau data tidak konsisten antar modul
- **Skip:** Test case tidak dapat dieksekusi (dependensi belum siap, fitur belum ada)

---

*Dokumen ini dibuat untuk keperluan QA internal. Update kolom Status dan Catatan saat eksekusi testing.*