# Frontend Refinement and Kitab Reader

## Context

Frontend E-Library sudah konsisten, tetapi terlalu bergantung pada pola dashboard generik: kartu putih berulang, badge, kotak ikon berwarna, gradient, blur, dan radius besar. Tujuannya adalah mempertahankan identitas hijau MA Assadah sambil membuat antarmuka lebih tenang, editorial, dan terasa seperti perpustakaan. Pembaca Maktabah harus terasa seperti kitab fisik dengan arah baca RTL dan animasi membalik halaman.

## Approach

1. Bangun vertical slice pembaca Maktabah menggunakan CSS 3D dan Alpine yang sudah tersedia. Desktop menampilkan bentang dua halaman; mobile satu halaman. Navigasi tombol, keyboard, klik sisi halaman, dan swipe memicu animasi yang sama. `prefers-reduced-motion` meniadakan efek 3D tanpa menghilangkan navigasi.
2. Sederhanakan token visual global di `resources/css/app.css`: permukaan hangat, bayangan lebih datar, radius lebih terkendali, dan utilitas khusus lembar kitab. Tidak menambah dependency atau sistem tema baru.
3. Rapikan komponen bersama (`card`, `stat-card`, `page-header`, `book-card`, `button`) agar halaman turunan otomatis kehilangan pola SaaS generik.
4. Ubah layout utama menjadi kanvas institusional yang lebih bersih. Pertahankan struktur navigasi, izin, dan perilaku mobile yang ada.
5. Ubah dashboard menjadi hierarki operasional: ringkasan padat, transaksi sebagai fokus, statistik sekunder tanpa semua bagian menjadi kartu.
6. Ubah katalog menjadi komposisi rak/editorial dengan filter yang lebih ringan dan kartu buku yang menonjolkan sampul serta metadata.
7. Sederhanakan landing Maktabah: hapus orb/glow, pill dekoratif, dan gradient berlebihan; pertahankan pencarian, filter, daftar kitab, serta seluruh fungsi pembaca.

## Key decisions

- Refinement sistemik dipilih dibanding redesign total agar fungsi dan pola Livewire tetap stabil.
- Warna hijau institusional dipertahankan; krem kertas menjadi aksen khusus konten perpustakaan dan kitab.
- Efek page flip dibuat dengan platform CSS/Alpine yang sudah ada, bukan dependency baru.
- Dua halaman desktop memuat halaman aktif dan halaman berikutnya; mobile hanya menampilkan halaman aktif.
- Arah interaksi pembaca mengikuti RTL, tetapi kontrol tetap berlabel bahasa Indonesia agar mudah dipahami.
- Seluruh CRUD tidak ditulis ulang. Komponen bersama dan token global menjadi pemilik konsistensi visual.

## Files to modify

### Pembaca kitab

- `app/Livewire/Maktabah/Index.php`: sediakan teks halaman pasangan dan keadaan navigasi tanpa mengubah kontrak layanan eksternal.
- `resources/views/livewire/maktabah/index.blade.php`: struktur kitab, kontrol sederhana, gesture, keyboard, serta animasi flip.
- `resources/css/app.css`: perspektif, lembar, lipatan, tekstur kertas, animasi, dan reduced-motion.

### Fondasi visual

- `resources/views/components/layouts/app.blade.php`: kurangi dekorasi chrome dan rapikan kanvas utama.
- `resources/views/components/card.blade.php`
- `resources/views/components/stat-card.blade.php`
- `resources/views/components/page-header.blade.php`
- `resources/views/components/book-card.blade.php`
- `resources/views/components/button.blade.php`

### Halaman utama

- `resources/views/livewire/dashboard/staff.blade.php`: hierarki operasional.
- `resources/views/livewire/catalog/index.blade.php`: toolbar dan rak katalog.
- `resources/views/livewire/login.blade.php` dan `resources/views/components/layouts/guest.blade.php`: login institusional tanpa pola gradient/dot generik.

## Out of scope

- Tidak mengubah skema database, API Maktabah, autentikasi, izin, atau aturan bisnis.
- Tidak memasang package page-flip atau library animasi.
- Tidak membuat dark mode untuk seluruh aplikasi; tema gelap hanya dipertahankan pada pembaca.
- Tidak mengganti semua ikon inline atau membangun design-system baru.
- Tidak menulis ulang setiap halaman CRUD; perubahan mereka berasal dari komponen bersama.

## STOP conditions

- Jika API hanya dapat memuat satu halaman dan pemuatan halaman pasangan membutuhkan perubahan kontrak eksternal, pertahankan satu halaman pada semua viewport dan laporkan.
- Jika perubahan komponen bersama merusak slot/atribut yang digunakan halaman lain, pertahankan kontrak komponen dan sederhanakan hanya styling-nya.
- Jangan menimpa perubahan kerja yang tidak terkait pada file yang sudah dimodifikasi pengguna.

## Verification

- `npm run build`: Vite dan Tailwind selesai tanpa error.
- `php artisan test --filter=Maktabah`: alur Maktabah yang tersedia tetap lulus.
- `php artisan test --filter=SmokeTest`: halaman utama tetap dapat dirender.
- Pemeriksaan manual desktop dan mobile: flip RTL, tombol, keyboard, swipe, TOC, tema, ukuran huruf, dan reduced motion tetap berfungsi.

## Implementation notes

Catat penyimpangan implementasi di `docs/superpowers/plans/2026-11-16-frontend-refinement-notes.md` bila kode memaksa perubahan dari rencana ini.
