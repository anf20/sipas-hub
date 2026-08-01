# Panduan Setup Cloudflare Tunnel (Aplikasi Lokal Online Gratis)

Panduan ini menjelaskan langkah demi langkah untuk membagikan aplikasi Laravel yang berjalan di laptop Anda (`localhost:8000`) ke internet menggunakan **domain Anda sendiri via Cloudflare**, 100% gratis, aman, dan tanpa perlu sewa VPS.

---

## 📋 Syarat Awal (Prerequisites)
1. Domain Anda sudah didaftarkan dan dikelola di **Cloudflare** (Name Server sudah diarahkan ke Cloudflare).
2. Laptop Anda menyala, dan aplikasi sedang berjalan di latar belakang:
   * **Web Server:** `php artisan serve` aktif (berjalan di port `8000`).
   * **Queue Worker:** `php artisan queue:work` aktif (opsional, untuk memproses broadcast WA).

---

## 🛠️ Langkah Demi Langkah Setup

### Langkah 1: Buka Dashboard Cloudflare Zero Trust
1. Buka browser dan masuk ke **[Cloudflare Zero Trust Dashboard](https://one.dash.cloudflare.com/)**.
2. Login menggunakan akun Cloudflare Anda.
3. Jika baru pertama kali masuk ke Zero Trust, Anda mungkin diminta memilih plan. Pilih **Free Plan** (tidak akan memungut biaya, Anda tidak perlu mengisi kartu).

### Langkah 2: Buat Tunnel Baru
1. Pada menu navigasi sebelah kiri, klik **Access** -> lalu pilih **Tunnels**.
2. Klik tombol **Create a Tunnel**.
3. Pilih tipe **Cloudflare Tunnel** (biasanya terpilih secara default), lalu klik **Next**.
4. Beri nama untuk tunnel Anda (misal: `sipashub-lokal`), lalu klik **Save tunnel**.

### Langkah 3: Install Konektor di Laptop Windows Anda
1. Pada halaman install connector, pilih tab **Windows** dan pilih sistem operasi Anda (biasanya **64-bit**).
2. Anda akan melihat bagian **"Choose an environment..."** yang menampilkan perintah instalasi otomatis untuk PowerShell.
3. Klik tombol **Copy** pada perintah PowerShell tersebut. Perintahnya akan terlihat mirip seperti ini:
   ```powershell
   powershell -Command "iex ((New-Object System.Net.WebClient).DownloadString('https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-windows-amd64.msi'))"
   ```
4. Buka **PowerShell** di laptop Anda dengan hak akses administrator (**Run as Administrator**).
5. Tempel (*paste*) perintah tersebut dan tekan Enter. PowerShell akan mengunduh dan menginstal agen Cloudflare di laptop Anda sebagai layanan sistem Windows (Windows Service).
6. Kembali ke layar browser Cloudflare Anda. Tunggu beberapa detik, di bagian bawah akan muncul status **"Connected"** berwarna hijau. Jika sudah muncul, klik **Next**.

### Langkah 4: Hubungkan Domain ke Localhost
Di halaman **Public Hostnames**, konfigurasikan alamat domain yang ingin Anda gunakan:

1. **Subdomain:** Isi dengan nama subdomain yang Anda inginkan (misalnya: `bayar` atau `portal`).
2. **Domain:** Pilih nama domain utama Anda dari menu dropdown yang tersedia (misalnya: `sipashub.my.id` atau domain Anda sendiri).
3. **Path:** Biarkan kosong.
4. **Service Type:** Pilih **HTTP** (bukan HTTPS, karena web server `artisan serve` berjalan secara lokal menggunakan HTTP).
5. **URL:** Isi dengan **`localhost:8000`** atau **`127.0.0.1:8000`**.
6. Klik tombol **Save hostname** (atau **Save tunnel**).

---

## 🎉 Selesai!
Sekarang, Cloudflare secara otomatis mengatur DNS Anda. Anda bisa langsung membuka domain tersebut di HP atau laptop mana pun di dunia (contoh: `https://bayar.domainanda.com`). 

Aplikasi Anda akan dimuat langsung dari laptop Anda dengan koneksi aman HTTPS!

### 💡 Tips Berguna:
* **Agar Web Selalu Aktif:** Pastikan laptop Anda tidak masuk ke mode *Sleep* atau mati secara otomatis saat didemonstrasikan.
* **Auto-Start:** Konektor Cloudflare telah diinstal sebagai Windows Service, artinya ia akan otomatis menyala setiap kali laptop Anda dihidupkan tanpa perlu menjalankan perintah manual lagi. Anda hanya perlu menyalakan `php artisan serve` saja di terminal.
