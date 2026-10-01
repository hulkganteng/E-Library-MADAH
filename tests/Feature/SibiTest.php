<?php

namespace Tests\Feature;

use App\Livewire\Sibi\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SibiTest extends TestCase
{
    use RefreshDatabase;

    public function test_sibi_page_is_publicly_accessible()
    {
        $response = $this->get('/sibi');
        $response->assertStatus(200);
        $response->assertSee('Pusat Buku Digital SIBI');
    }

    public function test_sibi_livewire_component_renders()
    {
        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSet('curriculum', 'merdeka')
            ->assertSet('level', 'SMA');
    }

    public function test_guest_cannot_import_sibi_book()
    {
        $dummyBook = [
            'id' => 'test-123',
            'title' => 'Test Buku Merdeka',
            'writer' => 'Penulis Uji',
            'publisher' => 'Pusbuk',
            'curriculum' => 'Kurikulum Merdeka',
            'isbn' => '999-999-999',
            'attachment' => null,
            'cover' => null,
            'description' => 'Buku uji',
        ];

        Livewire::test(Index::class)
            ->call('import', $dummyBook)
            ->assertDispatched('notify');

        $this->assertDatabaseMissing('books', ['title' => 'Test Buku Merdeka']);
    }

    public function test_librarian_can_import_sibi_book()
    {
        $admin = User::factory()->create();
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'buku.create', 'guard_name' => 'web']);
        $admin->givePermissionTo($permission);

        $dummyBook = [
            'id' => 'sibi-999',
            'title' => 'Buku Siswa Matematika Kelas VII',
            'writer' => 'Pusat Perbukuan Kemendikdasmen',
            'publisher' => 'Pusat Perbukuan Kemendikdasmen',
            'curriculum' => 'Kurikulum Merdeka',
            'isbn' => '978-602-244-000-0',
            'attachment' => 'https://example.com/matematika.pdf',
            'cover' => null,
            'description' => 'Buku ajar resmi',
        ];

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('import', $dummyBook)
            ->assertDispatched('notify');

        $this->assertDatabaseHas('books', [
            'title' => 'Buku Siswa Matematika Kelas VII',
            'isbn' => '978-602-244-000-0',
            'type' => 'ebook',
        ]);
    }
}
