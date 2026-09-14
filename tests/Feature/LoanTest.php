<?php

namespace Tests\Feature;

use App\Livewire\Loans\Index;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_checkout_and_return_flow(): void
    {
        $admin = User::where('email', 'admin@assaadah.sch.id')->firstOrFail();
        $copy = BookCopy::where('status', 'tersedia')->firstOrFail();

        Livewire::actingAs($admin)->test(Index::class)
            ->set('selectedCopy', $copy)
            ->call('checkout');

        $this->assertEquals('dipinjam', $copy->fresh()->status);
        $this->assertEquals(1, Loan::where('book_copy_id', $copy->id)->where('status', '!=', 'dikembalikan')->count());

        Livewire::actingAs($admin)->test(Index::class)
            ->set('returnCode', $copy->inventory_code)
            ->call('scanReturn')
            ->assertNotSet('returnInfo', null);

        Livewire::actingAs($admin)->test(Index::class)
            ->set('returnInfo', $copy->activeLoan)
            ->call('processReturn');

        $this->assertEquals('tersedia', $copy->fresh()->status);
        $this->assertNotNull(Loan::where('book_copy_id', $copy->id)->latest()->first()->returned_at);
    }

    public function test_read_only_user_cannot_create_a_loan(): void
    {
        $teacher = User::where('email', 'guru@assaadah.sch.id')->firstOrFail();
        $copy = BookCopy::where('status', 'tersedia')->firstOrFail();

        Livewire::actingAs($teacher)->test(Index::class)
            ->set('selectedCopy', $copy)
            ->call('checkout')
            ->assertForbidden();

        $this->assertDatabaseMissing('loans', ['book_copy_id' => $copy->id]);
    }

    public function test_catalog_borrow_request_and_approval_flow(): void
    {
        $book = \App\Models\Book::has('availableCopies')->firstOrFail();
        $admin = User::where('email', 'admin@assaadah.sch.id')->firstOrFail();

        Livewire::test(\App\Livewire\Catalog\Show::class, ['book' => $book])
            ->call('openBorrowModal')
            ->set('borrowerName', 'Siswa Test')
            ->call('submitBorrowRequest');

        $loan = Loan::where('status', 'menunggu')->firstOrFail();
        $this->assertEquals('menunggu', $loan->status);

        Livewire::actingAs($admin)->test(Index::class)
            ->call('approveLoan', $loan->id);

        $this->assertEquals('dipinjam', $loan->fresh()->status);
        $this->assertEquals('dipinjam', $loan->bookCopy->fresh()->status);
    }
}
