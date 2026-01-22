# Tutorial Menjalankan Trademetrics (Lokal XAMPP & Hosting Produksi)

Dokumen ini menjelaskan langkah-langkah menjalankan aplikasi Trademetrics di lingkungan lokal (XAMPP/Windows) dan di hosting produksi. Menggunakan bahasa Indonesia, ringkas, dan langsung dapat dieksekusi.

## Prasyarat
- PHP 8.1 atau lebih baru
- Composer terpasang
- Node.js (untuk build asset front-end) — atau bangun di lokal lalu unggah hasil build
- Database: PostgreSQL (default proyek) atau MySQL (opsional)

## Menjalankan di Lokal (XAMPP – Windows)
1. Salin proyek ke htdocs
   - Contoh lokasi: `C:\xampp\htdocs\trademetrics`
2. Pasang dependensi PHP
   ```bash
   cd C:\xampp\htdocs\trademetrics
   composer install
   php artisan key:generate
   ```
3. Konfigurasi `.env`
   - PostgreSQL (default):
     - Pastikan ekstensi `pgsql` dan `pdo_pgsql` aktif di `C:\xampp\php\php.ini` (hapus tanda `;`):
       - `extension=pdo_pgsql`
       - `extension=pgsql`
     - Pastikan folder `bin` PostgreSQL (yang berisi `libpq.dll`) ada di PATH Windows.
     - Sesuaikan kredensial di `.env`:
       ```env
       DB_CONNECTION=pgsql
       DB_HOST=127.0.0.1
       DB_PORT=5432
       DB_DATABASE=trademetrics
       DB_USERNAME=postgres
       DB_PASSWORD=your_password
       ```
   - MySQL (opsional – bawaan XAMPP):
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=trademetrics
     DB_USERNAME=root
     DB_PASSWORD=
     ```
     Buat database `trademetrics` di phpMyAdmin.
4. Migrasi database
   ```bash
   php artisan migrate
   ```
5. Build asset front-end (jika perlu)
   ```bash
   npm install
   npm run build
   ```
6. Jalankan aplikasi
   - Sederhana (built-in server):
     ```bash
     php artisan serve
     ```
     Akses: http://localhost:8000
   - Melalui Apache (XAMPP): atur DocumentRoot ke folder `public` proyek.
     - VirtualHost sederhana:
       ```
       <VirtualHost *:80>
         ServerName trademetrics.local
         DocumentRoot "C:/xampp/htdocs/trademetrics/public"
         <Directory "C:/xampp/htdocs/trademetrics/public">
           AllowOverride All
           Require all granted
         </Directory>
       </VirtualHost>
       ```
     - Tambah entri hosts: `127.0.0.1 trademetrics.local`
     - Restart Apache, akses http://trademetrics.local

## Deploy ke Hosting Produksi
1. Upload kode ke server
   - Pastikan web root diarahkan ke folder `public`.
2. Konfigurasi lingkungan produksi di `.env`
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domain-anda
   # DB sesuai server produksi (PostgreSQL atau MySQL)
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=trademetrics
   DB_USERNAME=postgres
   DB_PASSWORD=your_password
   ```
3. Pasang dependensi & generate key (jika belum)
   ```bash
   cd /path/to/trademetrics
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   ```
4. Build asset (pilih salah satu):
   - Bangun di server:
     ```bash
     npm ci
     npm run build
     ```
   - Atau bangun di lokal lalu unggah folder `public/build` ke server.
5. Izin folder & cache
   ```bash
   chmod -R 775 storage bootstrap/cache
   # Sesuaikan user/group proses web server
   chown -R www-data:www-data storage bootstrap/cache

   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
6. Migrasi database (pakai `--force` di produksi)
   ```bash
   php artisan migrate --force
   ```
7. Konfigurasi web server
   - Apache VirtualHost (contoh):
     ```
     <VirtualHost *:80>
       ServerName domain-anda
       DocumentRoot /var/www/trademetrics/public
       <Directory /var/www/trademetrics/public>
         AllowOverride All
         Require all granted
       </Directory>
     </VirtualHost>
     ```
   - Nginx (contoh):
     ```
     server {
       server_name domain-anda;
       root /var/www/trademetrics/public;
       index index.php index.html;
       location / {
         try_files $uri $uri/ /index.php?$query_string;
       }
       location ~ \.php$ {
         include fastcgi_params;
         fastcgi_pass unix:/run/php/php8.1-fpm.sock; # Sesuaikan versi PHP
         fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
         fastcgi_param DOCUMENT_ROOT $realpath_root;
       }
     }
     ```

## Backup & Restore (PostgreSQL)
- Backup (contoh perintah di server):
  ```bash
  PGPASSWORD=your_password pg_dump -h 127.0.0.1 -p 5432 -U postgres -d trademetrics -F p -f /path/to/backups/trademetrics.sql
  ```
- Restore:
  ```bash
  createdb -U postgres trademetrics
  psql -U postgres -d trademetrics -f /path/to/backups/trademetrics.sql
  ```

## Troubleshooting Umum
- "Driver pgsql/pdo_pgsql tidak ditemukan": aktifkan di `php.ini` dan pastikan `libpq.dll` tersedia di PATH (Windows) atau libpq terpasang (Linux).
- 500 error di produksi: cek `storage/logs/laravel.log` dan pastikan izin folder `storage` & `bootstrap/cache` benar.
- Asset tidak termuat: pastikan `npm run build` menghasilkan `public/build` dan `vite.config.js` sesuai.
- Ganti ke MySQL: update `.env` dan buat database, lalu `php artisan migrate`.

## Catatan
- Jangan unggah file `.env` ke repositori publik.
- Pastikan `APP_URL` akurat agar URL & email bekerja dengan benar.
- Jika menggunakan queue/scheduler: jalankan `php artisan queue:work` dan atur cron untuk `php artisan schedule:run`.
