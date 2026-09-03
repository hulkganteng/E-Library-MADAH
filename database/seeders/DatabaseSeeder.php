<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Category;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Shelf;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Setting;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $this->seedMasterData();
        $this->seedUsers();
        $this->seedBooks();
        $this->seedSettings();
    }

    private function seedUsers(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@assaadah.sch.id',
            'password' => 'password',
        ]);
        $admin->assignRole('Admin');

        $pustakawan = User::create([
            'name' => 'Pustakawan Perpustakaan',
            'email' => 'pustakawan@assaadah.sch.id',
            'password' => 'password',
        ]);
        $pustakawan->assignRole('Pustakawan');

        $guru = User::create([
            'name' => 'Ustadz Ahmad Fauzi',
            'email' => 'guru@assaadah.sch.id',
            'password' => 'password',
        ]);
        $guru->assignRole('Guru');
        Teacher::create(['user_id' => $guru->id, 'nip' => '1980010120000000', 'subject' => 'Bahasa Indonesia']);

        $siswa = User::create([
            'name' => 'Muhammad Rizky Ramadhan',
            'email' => 'siswa@assaadah.sch.id',
            'password' => 'password',
        ]);
        $siswa->assignRole('Siswa');
        Student::create([
            'user_id' => $siswa->id,
            'class_id' => 1,
            'nis' => '20241001',
            'nisn' => '0012345678',
            'gender' => 'L',
            'birth_date' => '2008-04-15',
        ]);
    }

    private function seedMasterData(): void
    {
        ClassRoom::create(['name' => 'X IPA 1', 'grade' => 'X', 'major' => 'IPA']);
        ClassRoom::create(['name' => 'XI IPA 2', 'grade' => 'XI', 'major' => 'IPA']);
        ClassRoom::create(['name' => 'XII IPS 1', 'grade' => 'XII', 'major' => 'IPS']);

        $categories = [
            'Agama & Al-Qur\'an', 'Bahasa', 'Sains', 'Matematika', 'Sosial',
            'Sejarah', 'Fiksi', 'Biografi', 'Teknologi', 'Referensi',
        ];
        foreach ($categories as $name) {
            Category::create(['name' => $name, 'slug' => str()->slug($name)]);
        }

        $authors = ['Tere Liye', 'Andrea Hirata', 'Pramoedya Ananta Toer', 'Buya Hamka', 'Nadiem Makarim'];
        foreach ($authors as $name) {
            Author::create(['name' => $name, 'slug' => str()->slug($name)]);
        }

        $publishers = ['PT Gramedia Pustaka Utama', 'Penerbit Republika', 'Penerbit Erlangga', 'Pustaka Alvabet'];
        foreach ($publishers as $name) {
            Publisher::create(['name' => $name]);
        }

        foreach (['A1', 'B2', 'C3', 'D4', 'E5'] as $code) {
            Shelf::create(['code' => $code, 'location' => "Rak $code - Lantai 1"]);
        }
    }

    private function seedBooks(): void
    {
        $titles = [
            ['Laskar Pelangi', 7, 1],
            ['Bumi', 7, 1],
            ['Tenggelamnya Kapal Van Der Wijck', 1, 2],
            ['Ayat-Ayat Cinta', 1, 2],
            ['Fisika Dasar Jilid 1', 3, 3],
        ];

        foreach ($titles as $i => [$title, $cat, $pub]) {
            $book = Book::create([
                'title' => $title,
                'slug' => str()->slug($title),
                'category_id' => $cat,
                'publisher_id' => $pub,
                'shelf_id' => 1,
                'isbn' => '978-' . str_pad((string) (1000000000 + $i), 10, '0', STR_PAD_LEFT),
                'publish_year' => 2005 + $i,
                'page_count' => 300 + ($i * 20),
                'type' => 'fisik',
                'description' => "Deskripsi singkat buku $title.",
            ]);
            $book->authors()->attach([($i % 5) + 1]);

            for ($c = 1; $c <= 3; $c++) {
                $inv = sprintf('ELIB-%04d-%02d', $book->id, $c);
                BookCopy::create([
                    'book_id' => $book->id,
                    'inventory_code' => $inv,
                    'qr_code' => (string) str()->uuid(),
                    'condition' => 'baik',
                    'status' => 'tersedia',
                ]);
            }
        }
    }

    private function seedSettings(): void
    {
        Setting::set('school_name', 'Madrasah Aliyah Assadah');
        Setting::set('library_name', 'Perpustakaan MA Assadah');
        Setting::set('address', 'Jl. Pendidikan No. 1');
        Setting::set('loan_duration_days', '7');
        Setting::set('fine_per_day', '1000');
        Setting::set('max_borrow', '3');
        Setting::set('phone', '(021) 1234567');
        Setting::set('email', 'perpustakaan@assaadah.sch.id');
    }
}
