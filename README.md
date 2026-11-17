ZaxSecure Desk V2

Web-based Cyber Security Incident Management System.

1. Deskripsi Aplikasi

ZaxSecure Desk merupakan aplikasi berbasis web untuk mengelola pelaporan, pencatatan, pemantauan, dan penanganan insiden keamanan siber secara terstruktur.

2. Fitur Utama

Autentikasi dan login pengguna.

Hak akses berdasarkan peran Administrator dan User.

Pengelolaan data insiden.

Kategorisasi dan klasifikasi insiden.

Pengelolaan pengguna.

Dashboard dan laporan analitik.

Jejak audit aktivitas.

Pengelolaan bukti atau lampiran insiden.

Catatan: sesuaikan daftar ini dengan fitur yang benar-benar tersedia dan berjalan.

3. Teknologi

Backend: Laravel dan PHP

Database: MySQL/MariaDB

Frontend: Blade, Tailwind CSS, JavaScript

Build Tool: Vite

Hosting: InfinityFree

4. URL Aplikasi

https://zaxsecure.page.gd

5. Instalasi Lokal

Clone repository.

Jalankan composer install.

Salin .env.example menjadi .env.

Isi konfigurasi database lokal pada .env.

Jalankan php artisan key:generate.

Jalankan migration sesuai kebutuhan dan kondisi database.

Jalankan npm install.

Jalankan npm run build.

Jalankan php artisan serve.

6. Konfigurasi Environment

Konfigurasi aplikasi dilakukan melalui environment variables.

Contoh variabel:

APP_NAME

APP_ENV

APP_KEY

APP_URL

DB_CONNECTION

DB_HOST

DB_PORT

DB_DATABASE

DB_USERNAME

DB_PASSWORD

Nilai rahasia tidak disertakan dalam repository.

7. Penggunaan AI Agent

AI agent yang digunakan:

ChatGPT: membantu analisis, perencanaan, dan debugging.

Antigravity: membantu pengembangan dan pemeriksaan kode.

Claude: membantu dokumentasi dan penyusunan laporan.

Contoh prompt utama dan proses verifikasi dijelaskan dalam laporan tugas.

8. Self-Testing Keamanan

Pengujian mencakup:

SQL Injection dan XSS.

Access control dan IDOR.

Autentikasi dan manajemen sesi.

HTTPS dan Security Headers.

Pemeriksaan secret dalam repository.

Audit dependensi.

Hasil aktual pengujian dan perbaikan didokumentasikan dalam laporan tugas.

9. Repository

Source code: [ISI URL REPOSITORY GITHUB]

10. Pengembang

Muhammad Dzaky Putra Pamungkas
NIM: 20240810109
Program Studi Teknik Informatika
Universitas Kuningan
