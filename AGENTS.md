# AGENTS.md — Hemat Token

## Tujuan
Kerjakan tugas dengan perubahan sekecil dan setepat mungkin. Prioritaskan hemat token, sedikit tool call, dan context minimal.

## Aturan Context
- Mulai dari file/path yang disebut user.
- Jangan scan seluruh repository kecuali benar-benar diperlukan.
- Jangan baca file yang tidak relevan hanya untuk "memahami project".
- Jika informasi sudah cukup, langsung kerjakan.
- Hindari membaca ulang file yang sama tanpa alasan.
- Jangan membuka folder besar/generated kecuali diminta: `node_modules/`, `vendor/`, `.git/`, `dist/`, `build/`, `storage/logs/`, cache, binary, dan lockfile besar.
- Gunakan pencarian nama/simbol spesifik sebelum membuka banyak file.
- Jika harus memperluas pencarian, lakukan bertahap dari scope terkecil.

## Aturan Pengerjaan
- Ubah hanya file yang diperlukan.
- Jangan refactor bagian lain jika tidak diminta.
- Jangan membuat file baru jika perubahan bisa dilakukan pada file yang ada.
- Pertahankan style, struktur, framework, dan dependency project yang sudah ada.
- Jangan memasang package/dependency baru kecuali benar-benar diperlukan atau diminta.
- Untuk bug, cari penyebab paling mungkin terlebih dahulu dan lakukan fix minimal.
- Untuk UI, fokus pada komponen/halaman yang disebut user.

## Aturan Output
- Jawaban singkat dan langsung.
- Jangan menjelaskan kode baris demi baris kecuali diminta.
- Jangan mengulang isi prompt user.
- Jangan menampilkan seluruh isi file setelah edit; tampilkan ringkasan perubahan atau diff penting saja.
- Jangan membuat dokumentasi, README, komentar panjang, atau contoh tambahan kecuali diminta.
- Jika tugas berhasil, cukup jelaskan file yang berubah, perubahan utama, dan cara verifikasi singkat.

## Verifikasi
- Jalankan hanya test/lint/build yang relevan dengan perubahan.
- Pilih command verifikasi paling kecil terlebih dahulu.
- Jangan menjalankan seluruh test suite jika pemeriksaan terfokus sudah cukup.
- Jika command berpotensi lama/mahal dan tidak wajib, jangan jalankan tanpa kebutuhan.

## Laravel / Web
- Prioritaskan file terkait di `routes/`, `app/`, `resources/views/`, `resources/js/`, dan `resources/css/`.
- Jangan membaca `vendor/` untuk memahami package; gunakan kode project/config terlebih dahulu.
- Jangan membaca seluruh `storage/logs/`; ambil hanya bagian log yang relevan jika diperlukan.
- Untuk Blade/CSS/JS, perbaiki komponen yang bermasalah tanpa mengubah desain lain.
- Untuk database, jangan membuat migration baru jika perubahan schema tidak diperlukan.

## Prinsip Akhir
Jika ada dua cara yang sama-sama benar, pilih yang membutuhkan:
1. context paling sedikit,
2. file paling sedikit,
3. perubahan paling kecil,
4. output paling pendek.
