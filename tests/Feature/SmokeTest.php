<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin()
    {
        return User::where('email', 'admin@assaadah.sch.id')->firstOrFail();
    }

    public function test_public_pages(): void
    {
        $this->get('/')->assertRedirect();
        $this->get('/katalog')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/buku-tamu')->assertOk();
    }

    public function test_admin_pages(): void
    {
        $pages = ['/dashboard', '/buku', '/eksemplar', '/kategori', '/penulis', '/penerbit', '/rak', '/peminjaman', '/kunjungan', '/guru', '/laporan', '/pengumuman', '/audit', '/pengaturan', '/notifikasi'];
        foreach ($pages as $p) {
            $this->actingAs($this->admin())->get($p)->assertOk();
        }
    }

    public function test_student_dashboard(): void
    {
        $this->get('/katalog')->assertOk();
        $this->get('/peminjaman')->assertOk();
    }

    public function test_download_books_template_excel(): void
    {
        \Livewire\Livewire::actingAs($this->admin())
            ->test(\App\Livewire\Books\Index::class)
            ->call('downloadTemplate')
            ->assertFileDownloaded('template-import-buku.xlsx');
    }

    public function test_routes_follow_each_module_permission(): void
    {
        $librarian = User::where('email', 'pustakawan@assaadah.sch.id')->firstOrFail();
        $teacher = User::where('email', 'guru@assaadah.sch.id')->firstOrFail();

        $this->actingAs($librarian)->get('/pengumuman')->assertOk();
        $this->actingAs($librarian)->get('/guru')->assertForbidden();
        $this->actingAs($librarian)->get('/pengaturan')->assertForbidden();

        $this->actingAs($teacher)->get('/buku')->assertOk();
        $this->actingAs($teacher)->get('/eksemplar')->assertForbidden();
        $this->actingAs($teacher)->get('/kategori')->assertForbidden();
    }
}
