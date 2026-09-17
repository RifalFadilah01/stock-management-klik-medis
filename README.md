# Sistem Manajemen Stok Obat

Aplikasi berbasis web untuk mengelola persediaan obat pada fasilitas kesehatan. Dibangun menggunakan Laravel 12 (Backend) dan jQuery/Blade (Frontend) dengan fitur pemrosesan data asinkron untuk performa tinggi.

## Persyaratan Sistem

- PHP >= 8.2
- Composer
- MySQL / MariaDB
- Git

## Langkah Instalasi dan Konfigurasi

1. Clone repositori ini ke dalam direktori lokal Anda:
   ```bash
   git clone <URL_REPOSITORY_ANDA>
   cd stock-management
   ```

2. Instal semua dependensi PHP menggunakan Composer:
   ```bash
   composer install
   ```

3. Salin file konfigurasi environment:
   ```bash
   cp .env.example .env
   ```

4. Hasilkan application key:
   ```bash
   php artisan key:generate
   ```

5. Konfigurasi koneksi database dan sistem queue pada file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=stock_management
   DB_USERNAME=root
   DB_PASSWORD=

   // Pastikan connection nya mengacu ke database
   QUEUE_CONNECTION=database
   CACHE_STORE=database
   ```

6. Jalankan migrasi database beserta seeder untuk menghasilkan 10.000 data dummy:
   ```bash
   php artisan migrate --seed
   ```

## Cara Menjalankan Aplikasi

Aplikasi ini membutuhkan dua proses terminal yang berjalan secara bersamaan agar fitur background job (Export Excel) dapat berfungsi dengan baik.

1. Buka Terminal 1 dan jalankan server web lokal:
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui [http://127.0.0.1:8000](http://127.0.0.1:8000)

2. Buka Terminal 2 dan jalankan queue worker:
   ```bash
   php artisan queue:work
   ```
   *Catatan: Terminal ini harus tetap terbuka agar antrean proses export Excel dapat berjalan di latar belakang.*

## Fitur Utama

- **CRUD Tanpa Reload:** Seluruh operasi Create, Read, Update, dan Delete diimplementasikan menggunakan jQuery AJAX tanpa memuat ulang halaman utama.
- **Realtime Search & Filter:** Pencarian data responsif yang berjalan otomatis saat pengguna mengetik, dilengkapi dengan filter kategori dan status stok.
- **Auto-save:** Perubahan pada form Edit (seperti Minimum Stok, Expired Date, dan Catatan Internal) akan tersimpan secara asinkron dan otomatis tanpa memerlukan interaksi pada tombol simpan.
- **Validasi Server-side:** Menangkap dan menampilkan pesan error validasi langsung dari Form Request Laravel ke antarmuka pengguna secara mulus.
- **Export Excel via Queue:** Pembuatan laporan Excel untuk data dalam jumlah besar diproses menggunakan Laravel Job di background, lengkap dengan indikator progress bar realtime (polling).
- **Export PDF:** Pembuatan laporan PDF secara dinamis yang mematuhi filter dan kata kunci pencarian yang sedang aktif di antarmuka pengguna.

## Keputusan Teknis dan Keterbatasan Implementasi

- **Manajemen Memori Export Excel:** Mengingat database dikonfigurasi untuk memuat lebih dari 10.000 baris data, proses export Excel diimplementasikan menggunakan package `spatie/simple-excel`. Package ini memproses data baris demi baris menggunakan metode generator (chunk 500 data) sehingga penggunaan memori (RAM) server tetap sangat stabil dan efisien dibandingkan memuat seluruh koleksi data sekaligus.
- **Debounce pada Pencarian dan Auto-save:** Logika JavaScript dilengkapi dengan fungsi debounce (300ms untuk pencarian, 800ms untuk auto-save) guna menunda pengiriman request AJAX. Ini merupakan langkah preventif untuk mencegah server kelebihan beban akibat spam request saat pengguna mengetik dengan cepat.
- **Polling Progress Bar:** Progress bar Excel mengandalkan mekanisme polling AJAX setiap 1,5 detik yang membaca status dari Laravel Cache. Pendekatan ini dipilih untuk menjaga kesederhanaan infrastruktur dan memenuhi ruang lingkup tes tanpa harus mengonfigurasi layanan WebSockets (seperti Pusher atau Laravel Reverb).
- **Sinkronisasi Filter Export:** Logika export (baik Excel maupun PDF) dirancang untuk menerima parameter URL secara dinamis dari frontend. Hal ini menjamin bahwa dokumen yang diekspor akan selalu memuat data yang sama persis dengan tabel yang sedang dilihat oleh pengguna pada saat mengaktifkan filter ganda.
- **Pembatalan Request AJAX (Abort):** Pada fitur pencarian realtime, objek request AJAX disimpan ke dalam variabel. Jika pengguna mengetik kata kunci baru sebelum request lama selesai merender respons, request lama tersebut akan langsung dibatalkan (`.abort()`). Ini mencegah terjadinya *race condition* yang dapat membuat tampilan data di tabel menjadi tidak akurat atau saling menimpa.
