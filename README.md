# E-Library

E-Library adalah aplikasi pengelolaan perpustakaan sekolah berbasis Laravel. Aplikasi ini membantu petugas mengelola katalog, anggota, eksemplar buku, transaksi peminjaman, pengembalian, denda, dan laporan dalam satu tempat.

## Mulai cepat

Pastikan PHP 8.2 atau lebih baru, Composer, dan Node.js sudah tersedia. Proyek ini menggunakan SQLite secara bawaan, jadi Anda tidak perlu menyiapkan server database untuk mencoba aplikasi.

1. Pasang dependensi PHP.

   ```bash
   composer install
   ```

2. Siapkan konfigurasi aplikasi dan kunci enkripsi.

   ```bash
   php -r "file_exists('.env') || copy('.env.example', '.env');"
   php artisan key:generate
   ```

3. Buat tabel dan isi data awal.

   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```

4. Pasang dependensi tampilan.

   ```bash
   npm install
   ```

5. Jalankan aplikasi.

   ```bash
   composer run dev
   ```

Buka `http://localhost:8000` di browser. Halaman pertama yang tampil adalah katalog publik. Pilih **Masuk** untuk membuka halaman pengelolaan sesuai hak akses akun.

## Akun awal

Perintah `php artisan migrate --seed` membuat akun berikut untuk keperluan pengembangan lokal.

| Peran | Email | Kata sandi |
| --- | --- | --- |
| Admin | `admin@assaadah.sch.id` | `password` |
| Pustakawan | `pustakawan@assaadah.sch.id` | `password` |
| Guru | `guru@assaadah.sch.id` | `password` |
| Siswa | `siswa@assaadah.sch.id` | `password` |

Jangan gunakan kata sandi bawaan tersebut pada lingkungan produksi.

## Hak akses pengguna

- **Admin** mengelola seluruh data, pengumuman, laporan, audit aktivitas, pengaturan, dan pencadangan database.
- **Pustakawan** mengelola buku dan eksemplar, mengimpor data, melayani transaksi peminjaman, serta melihat laporan.
- **Guru** dapat melihat katalog, daftar buku, dan informasi peminjaman.
- **Siswa** dapat membuka katalog dan melihat koleksi yang tersedia.

Menu yang terlihat dapat berbeda karena aplikasi menyesuaikannya dengan peran dan izin akun.

## Panduan penggunaan

### Mencari buku di katalog

Katalog dapat dibuka tanpa masuk ke aplikasi.

1. Buka halaman utama atau pilih menu **Katalog**.
2. Gunakan kolom pencarian untuk mencari judul buku.
3. Gunakan filter yang tersedia untuk mempersempit hasil.
4. Pilih sebuah buku untuk melihat deskripsi, lokasi rak, dan ketersediaan eksemplarnya.

### Menyiapkan data perpustakaan

Sebelum mencatat buku, lengkapi data master agar informasi koleksi tetap rapi.

1. Masuk sebagai Admin.
2. Lengkapi menu **Kategori**, **Penulis**, **Penerbit**, dan **Rak**.
3. Jika data siswa diperlukan, lengkapi **Kelas** terlebih dahulu.
4. Tambahkan data anggota melalui menu **Siswa** atau **Guru**.

Pustakawan dapat melihat data master dan mengimpor data siswa, tetapi perubahan data master dan anggota tetap menjadi kewenangan Admin.

### Menambahkan buku dan eksemplar

Data buku menyimpan informasi judul, sedangkan eksemplar mewakili setiap buku fisik yang dapat dipinjam.

1. Buka menu **Buku**, lalu pilih tombol untuk menambah buku.
2. Isi informasi buku, termasuk kategori, penulis, penerbit, dan rak.
3. Simpan data buku.
4. Buka menu **Eksemplar** untuk menambahkan jumlah salinan fisik.
5. Gunakan kode inventaris atau QR setiap eksemplar saat melakukan transaksi.

Status eksemplar dapat diubah menjadi tersedia, dipinjam, rusak, hilang, atau dipesan sesuai kondisi buku.

### Mencatat peminjaman

1. Buka menu **Peminjaman**.
2. Pilih tab **Peminjaman Baru**.
3. Cari anggota berdasarkan nama atau email, lalu pilih anggota tersebut.
4. Pindai QR atau masukkan kode inventaris eksemplar.
5. Periksa durasi pinjam, lalu konfirmasi transaksi.

Aplikasi hanya menerima eksemplar berstatus tersedia. Jumlah pinjaman aktif juga mengikuti batas maksimal yang ditetapkan Admin.

### Mencatat pengembalian

1. Buka menu **Peminjaman**.
2. Pilih tab **Pengembalian Buku**.
3. Pindai QR atau masukkan kode inventaris buku.
4. Periksa informasi peminjam dan denda yang muncul.
5. Pilih **Konfirmasi Pengembalian Buku**.

Setelah transaksi selesai, eksemplar kembali berstatus tersedia. Jika pengembalian melewati jatuh tempo, aplikasi menghitung denda berdasarkan tarif harian pada pengaturan.

### Memperpanjang masa pinjam

1. Buka tab **Daftar Peminjaman**.
2. Cari transaksi berdasarkan nama anggota atau judul buku.
3. Pilih **Perpanjang** pada transaksi yang masih aktif.
4. Setujui pesan konfirmasi.

Buku yang sudah dikembalikan atau telah melewati jatuh tempo tidak dapat diperpanjang.

### Melihat dan mengekspor laporan

1. Buka menu **Laporan**.
2. Tentukan rentang tanggal laporan.
3. Periksa ringkasan transaksi yang tampil.
4. Pilih ekspor PDF atau Excel jika Anda memiliki izin ekspor.

### Mengubah pengaturan sistem

Menu **Pengaturan** hanya tersedia untuk Admin. Di halaman ini, Admin dapat mengubah identitas sekolah dan perpustakaan, logo, favicon, durasi pinjam, batas jumlah buku, serta denda per hari.

Setelah mengubah nilai, pilih **Simpan Konfigurasi**. Pengaturan baru akan digunakan pada transaksi berikutnya.

### Membuat cadangan database

1. Masuk sebagai Admin, lalu buka **Pengaturan**.
2. Temukan bagian **Pencadangan Database**.
3. Pilih **Buat Cadangan Database Sekarang**.
4. Pastikan informasi cadangan terakhir sudah diperbarui.

Simpan salinan cadangan di lokasi lain secara berkala, terutama sebelum pembaruan aplikasi atau perubahan data dalam jumlah besar.

## Menghentikan aplikasi

Kembali ke terminal tempat aplikasi berjalan, lalu tekan `Ctrl+C`. Perintah tersebut menghentikan server Laravel, antrean, pembaca log, dan Vite yang dijalankan oleh `composer run dev`.

## Pemecahan masalah singkat

- Jika muncul pesan bahwa kunci aplikasi belum tersedia, jalankan `php artisan key:generate`.
- Jika tabel database belum ditemukan, jalankan `php artisan migrate --seed`.
- Jika tampilan tidak termuat dengan benar, pastikan `npm install` sudah selesai dan jalankan kembali `composer run dev`.
- Jika logo tidak tampil setelah diunggah, jalankan `php artisan storage:link`.
- Jika perubahan konfigurasi belum terbaca, jalankan `php artisan optimize:clear`.

## Teknologi

E-Library menggunakan Laravel 12, Livewire 4, Tailwind CSS 4, dan Vite 7. Konfigurasi bawaan memakai SQLite dan dapat disesuaikan melalui file `.env`.
