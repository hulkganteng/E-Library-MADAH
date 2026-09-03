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
        $student = User::where('email', 'siswa@assaadah.sch.id')->firstOrFail();
        $copy = BookCopy::where('status', 'tersedia')->firstOrFail();

        Livewire::actingAs($admin)->test(Index::class)
            ->set('selectedMember', $student->id)
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
        $student = User::where('email', 'siswa@assaadah.sch.id')->firstOrFail();
        $copy = BookCopy::where('status', 'tersedia')->firstOrFail();

        Livewire::actingAs($teacher)->test(Index::class)
            ->set('selectedMember', $student->id)
            ->set('selectedCopy', $copy)
            ->call('checkout')
            ->assertForbidden();

        $this->assertDatabaseMissing('loans', ['book_copy_id' => $copy->id]);
    }
}
