# Riwayat & Panduan Deployment SIPAS-Hub (VPS Ubuntu 24.04 + CloudPanel)

Dokumen ini mencatat seluruh proses, kendala (troubleshooting), dan solusi yang diterapkan selama proses *deployment* aplikasi **SIPAS-Hub** ke VPS dengan domain **sipashub.my.id**.

---

## 📋 Bagian 1: Alur Deployment yang Benar (Best Practice)

Ikuti langkah-langkah di bawah ini untuk melakukan setup deployment dari awal pada server baru:

### 1. Konfigurasi DNS di Registrar (IDCloudHost)
Arahkan domain ke IP VPS Anda dengan menambahkan DNS record berikut pada DNS Zones:
*   **A Record**: Name = `sipashub.my.id`, RDATA = `103.191.92.132` (IP VPS), TTL = `3600`.
*   **CNAME Record**: Name = `www.sipashub.my.id`, RDATA = `sipashub.my.id`, TTL = `3600`.

### 2. Membuat Site Baru di CloudPanel
*   Masuk ke Dashboard CloudPanel (`https://IP_VPS:8443`).
*   Pilih **Add Site** -> **Create a PHP Site**.
*   Masukkan domain: `sipashub.my.id`.
*   Pilih versi PHP: **PHP 8.4**.
*   Tentukan SSH/Site User: **`sipashub-user`**.
*   Aktifkan SSL: Buka tab **SSL** situs -> klik **Actions** -> **New Let's Encrypt Certificate** -> **Create and Install**.

### 3. Masuk ke SSH dan Bersihkan Folder Default
*   Masuk ke VPS via SSH sebagai **root**:
    ```bash
    ssh root@103.191.92.132
    ```
*   Masuk ke folder website utama:
    ```bash
    cd /home/sipashub-user/htdocs
    ```
*   Hapus file `.gitignore` atau file bawaan CloudPanel lainnya agar folder benar-benar kosong:
    ```bash
    rm -rf *
    ```

### 4. Mengunduh Source Code dari GitHub
*   Clone repositori ke folder saat ini (pastikan menyertakan titik `.` di akhir perintah):
    ```bash
    git clone https://github.com/anf20/sipas-hub.git .
    ```
*   Perbaiki hak kepemilikan folder ke user website agar server Nginx bisa membaca file:
    ```bash
    chown -R sipashub-user:sipashub-user /home/sipashub-user/htdocs
    ```

### 5. Upgrade Node.js ke Versi Terbaru (v22)
Vite pada Laravel 13 membutuhkan Node.js v20/v22. Jalankan perintah berikut untuk meng-upgrade:
```bash
apt remove nodejs npm -y && apt autoremove -y
curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
apt install nodejs -y
```

### 6. Instalasi Dependensi & Kompilasi Aset
Jalankan perintah ini menggunakan user `sipashub-user` untuk keamanan:
*   Install package PHP:
    ```bash
    sudo -u sipashub-user composer install --no-dev --optimize-autoloader
    ```
*   Setup Environment File:
    ```bash
    sudo -u sipashub-user cp .env.example .env
    sudo -u sipashub-user php artisan key:generate
    ```
*   Install package JS dan Compile Aset:
    ```bash
    sudo -u sipashub-user npm install
    sudo -u sipashub-user npm run build
    ```

### 7. Setup Database MySQL
*   Buat database baru di CloudPanel (tab **Databases**). Catat *Database Name*, *Database User*, dan *Password*.
*   Edit `.env` di VPS (`sudo -u sipashub-user nano .env`) dan sesuaikan:
    ```env
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://sipashub.my.id

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_db_anda
    DB_USERNAME=user_db_anda
    DB_PASSWORD=password_db_anda

    QUEUE_CONNECTION=database
    ```
*   Jalankan migrasi database di VPS (gunakan pustaka dev sementara untuk seeder jika perlu data simulasi, lalu bersihkan kembali):
    ```bash
    # 1. Install dev dependencies sementara untuk Faker
    sudo -u sipashub-user composer install
    
    # 2. Migrasi & Seeding
    sudo -u sipashub-user php artisan migrate:fresh --force --seed
    
    # 3. Bersihkan kembali untuk mode production
    sudo -u sipashub-user composer install --no-dev --optimize-autoloader
    ```

### 8. Konfigurasi Nginx VHost (Kompatibel Tanpa IPv6)
Salin konfigurasi Nginx dari proyek lokal (`nginx_config.conf`) langsung ke folder Nginx VPS Anda:
```bash
cp /home/sipashub-user/htdocs/nginx_config.conf /etc/nginx/sites-enabled/www.sipashub.my.id.conf
nginx -t
systemctl reload nginx
```

### 9. Konfigurasi Background Worker & Scheduler
*   **Cron Job Scheduler**: Tambahkan di CloudPanel -> tab **Cron Jobs** -> pilih **Every Minute** (`* * * * *`):
    ```text
    php /home/sipashub-user/htdocs/artisan schedule:run >> /dev/null 2>&1
    ```
*   **Supervisor (Queue Worker)**: Install Supervisor di VPS:
    ```bash
    apt install supervisor -y
    ```
    Buat file konfigurasi di `/etc/supervisor/conf.d/sipas-worker.conf` berisi detail `queue:work` seperti yang didefinisikan di panduan. Jalankan dengan:
    ```bash
    supervisorctl reread && supervisorctl update && supervisorctl start sipas-worker:*
    ```

---

## ❓ Bagian 2: Q&A / Troubleshooting Kendala yang Ditemukan

Berikut adalah daftar masalah yang dialami selama proses deployment ini beserta cara mengatasinya:

### Q1: Mengapa muncul peringatan `WARNING: REMOTE HOST IDENTIFICATION HAS CHANGED!` saat login SSH?
*   **Penyebab**: VPS Anda baru saja di-rebuild/reinstall, sehingga sidik jari (fingerprint) SSH server berubah. Komputer lokal Anda menolak koneksi karena mendeteksi perbedaan tersebut.
*   **Solusi**: Jalankan perintah pembersihan sidik jari lama di CMD/PowerShell Windows Anda sebelum mencoba masuk kembali:
    ```cmd
    ssh-keygen -R 103.191.92.132
    ```

### Q2: Mengapa muncul error `Permission Denied` saat mencoba masuk ke direktori `/home/cloudpanel/htdocs/...`?
*   **Penyebab**: Anda masuk menggunakan user biasa (`sipashub`) yang memiliki hak akses terbatas (chroot/restricted).
*   **Solusi**: Beralihlah ke user administrator utama (**root**) terlebih dahulu dengan perintah:
    ```bash
    sudo su
    ```

### Q3: Di mana folder website saya sebenarnya berada di VPS CloudPanel?
*   **Penyebab**: Setiap situs di CloudPanel diisolasi di bawah folder user situs tersebut.
*   **Solusi**: Folder website Anda terletak di `/home/[site-user]/htdocs`. Jika user situs Anda adalah `sipashub-user`, maka letak foldernya adalah:
    ```bash
    /home/sipashub-user/htdocs
    ```

### Q4: Mengapa composer/git memprotes folder tidak kosong saat melakukan `git clone ... .`?
*   **Penyebab**: CloudPanel otomatis membuat file `.gitignore` tersembunyi di dalam direktori `htdocs` baru Anda. Git menolak mengunduh repositori ke dalam folder yang sudah berisi file.
*   **Solusi**: Hapus file bawaan tersebut dengan perintah `rm -f .gitignore` sebelum menjalankan perintah `git clone`.

### Q5: Saya sudah clone, tapi file proyek malah terduplikasi ke dalam subfolder `sipas-hub`. Mengapa?
*   **Penyebab**: Saat melakukan cloning, Anda lupa menuliskan tanda titik `.` di ujung perintah `git clone https://github.com/.../sipas-hub.git`. Tanpa titik, Git otomatis membuat subfolder baru dengan nama repositori tersebut.
*   **Solusi**: Pindahkan seluruh file ke folder induk (`htdocs`) dan hapus folder kosongnya:
    ```bash
    mv sipas-hub/* sipas-hub/.* .
    rm -rf sipas-hub
    ```

### Q6: Mengapa perintah `npm run build` menampilkan error `ReferenceError: CustomEvent is not defined`?
*   **Penyebab**: Sistem operasi Ubuntu 24.04 secara default memasang Node.js versi 18. Sedangkan Vite (pembawa aset Laravel/Tailwind 4) mewajibkan penggunaan Node.js versi minimal 20 atau 22.
*   **Solusi**: Hapus Node.js versi lama dan instal Node.js v22 menggunakan package manager NodeSource (ikuti langkah pada Bagian 1, poin 5).

### Q7: Mengapa muncul error `Access denied for user 'root'@'localhost'` saat migrasi database?
*   **Penyebab**: Laravel secara bawaan mencoba masuk menggunakan user `root` MySQL. Di CloudPanel, website dilarang keras menggunakan user root demi alasan keamanan.
*   **Solusi**: Buat database khusus di tab Databases CloudPanel, lalu masukkan detail *Name*, *User*, dan *Password* tersebut ke dalam file `.env` di bagian `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`. Jalankan `php artisan config:clear` sebelum melakukan migrasi ulang.

### Q8: Mengapa muncul error `Call to undefined function Database\Factories\fake()` saat seeding database?
*   **Penyebab**: Pustaka developer (Faker) tidak terinstal karena kita menjalankan composer dengan parameter `--no-dev`.
*   **Solusi**: Instal sementara pustaka dev (`composer install`), jalankan migrasi dan seed (`php artisan migrate:fresh --seed`), lalu bersihkan kembali (`composer install --no-dev --optimize-autoloader`).

### Q9: Mengapa web panel/website tidak bisa diakses dan menampilkan error `socket() [::]:80 failed`?
*   **Penyebab**: VPS Anda tidak mendukung/mengaktifkan protokol IPv6 di tingkat kernel (hanya IPv4), sehingga Nginx error saat mencoba mendengarkan port IPv6 (`[::]:80` atau `[::]:8080`).
*   **Solusi**: Matikan konfigurasi IPv6 dengan memberikan tanda komentar `#` di depan baris `listen [::]...` di semua file konfigurasi Nginx (khususnya `sites-enabled/default.conf` dan `www.sipashub.my.id.conf`).

### Q10: Terjadi Nginx Deadlock: CloudPanel gagal menyimpan Vhost baru karena konfigurasi lama di disk error, dan kita tidak bisa menyalin script panjang via terminal karena kesulitan memformatnya. Bagaimana mengatasinya?
*   **Penyebab**: CloudPanel selalu menjalankan validasi syntax Nginx (`nginx -t`) sebelum menyimpan. Jika konfigurasi di server sedang rusak, CloudPanel menolak menyimpan konfigurasi baru.
*   **Solusi**:
    1.  Tulis konfigurasi Nginx bersih di komputer lokal Anda (`nginx_config.conf`), ganti semua variabel template `{{...}}` dengan path asli (misal: jalur sertifikat SSL dan port PHP-FPM 19001).
    2.  Kirim file tersebut ke repositori GitHub via `git push`.
    3.  Tarik file tersebut di VPS menggunakan `git pull`.
    4.  Timpa file konfigurasi lama di VPS secara langsung melalui SSH dengan perintah:
        ```bash
        cp nginx_config.conf /etc/nginx/sites-enabled/www.sipashub.my.id.conf
        ```
    5.  Jalankan `nginx -t` dan `systemctl reload nginx`.

### Q11: Semua konfigurasi sudah sukses di VPS, tetapi browser masih menampilkan `This site can't be reached`?
*   **Penyebab**: Port 80 (HTTP) dan 443 (HTTPS) diblokir oleh sistem firewall external penyedia VPS (IDCloudHost), atau DNS masih dalam proses propagasi.
*   **Solusi**:
    1.  Masuk ke dashboard panel IDCloudHost VPS Anda, buka tab **Firewall**, tambahkan **Inbound Rules** baru untuk mengizinkan port **80** (HTTP), **443** (HTTPS), **22** (SSH), dan **8443** (CloudPanel) untuk semua IP (`0.0.0.0/0`).
    2.  Uji status DNS menggunakan public resolver Google (jalankan `nslookup sipashub.my.id 8.8.8.8` di CMD lokal). Jika Google DNS mendeteksi IP VPS Anda, berarti setup sudah benar, Anda hanya perlu menunggu proses propagasi DNS lokal ISP Anda (5-15 menit) atau mencobanya menggunakan paket data seluler HP.

### Q12: Mengapa muncul error `502 Bad Gateway` setelah mengunggah atau merubah konfigurasi Nginx?
*   **Penyebab**: Nginx utama (port 80/443) mencoba mem-proxy lalu lintas ke Varnish Cache (`http://127.0.0.1:8081`), namun service Varnish sedang mati atau dinonaktifkan di CloudPanel. Selain itu, ada risiko variabel Nginx seperti `$uri` terhapus/berkurang argumennya jika disalin langsung via GUI editor CloudPanel yang menginterpretasikan tanda `$` sebagai variabel template internal.
*   **Solusi**:
    1.  Jika Varnish tidak digunakan, ubah port proxy utama di file virtual host Nginx dari port `8081` (Varnish) ke port `8080` (Nginx Backend PHP-FPM):
        ```nginx
        location / {
          proxy_pass http://127.0.0.1:8080;
          ...
        }
        ```
    2.  Untuk melakukan perubahan dengan aman tanpa merusak struktur sintaksis (mencegah error `invalid number of arguments` karena karakter spasi aneh atau tanda `$` yang hilang), jalankan perintah `sed` otomatis berikut di VPS:
        ```bash
        sed -i 's/proxy_pass http:\/\/127.0.0.1:8081;/proxy_pass http:\/\/127.0.0.1:8080;/g' /etc/nginx/sites-enabled/www.sipashub.my.id.conf
        ```
    3.  Lakukan pengetesan sintaksis dan muat ulang Nginx:
        ```bash
        nginx -t
        systemctl reload nginx
        ```

### Q13: Mengapa tombol UI tidak merespons (tidak bisa diklik), dan muncul error `flux.min.js 404 (Not Found)` serta `fluxModal is not defined` di console browser?
*   **Penyebab**: Livewire Flux UI menyajikan file aset JS/CSS secara dinamis via route internal Laravel (`/flux/flux.min.js`). Namun, aturan penanganan static files umum pada Nginx (`location ~* ^.+\.(css|js|...)`) menangkap request tersebut dan memaksanya mencari file fisik di disk public. Karena file tidak ada secara fisik, Nginx mengembalikan error 404, sehingga fungsi JS / modal Alpine.js gagal dimuat.
*   **Solusi**: Tambahkan konfigurasi `location` khusus untuk `/flux/` **sebelum** blok static files umum pada file virtual host Nginx (`/etc/nginx/sites-enabled/www.sipashub.my.id.conf`):

    1.  Pada server block utama **port 80/443** (di atas block static files):
        ```nginx
        location ~* ^/flux/(flux|editor)(\.min)?\.(js|css)$ {
          expires off;
          proxy_pass http://127.0.0.1:8080;
          proxy_set_header Host $host;
          proxy_set_header X-Forwarded-Host $host;
          proxy_set_header X-Real-IP $remote_addr;
          proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
          proxy_set_header X-Forwarded-Proto $scheme;
          proxy_set_header X-Forwarded-Port $server_port;
          proxy_hide_header X-Varnish;
          proxy_redirect off;
        }
        ```

    2.  Pada server block backend **port 8080** (di atas block static files):
        ```nginx
        location ~* ^/flux/(flux|editor)(\.min)?\.(js|css)$ {
          expires off;
          try_files $uri $uri/ /index.php?$query_string;
        }
        ```

    3.  Jalankan pengetesan sintaksis dan muat ulang Nginx:
        ```bash
        nginx -t
        systemctl reload nginx
        ```
