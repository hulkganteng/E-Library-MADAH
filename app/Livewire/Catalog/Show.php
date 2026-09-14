<?php

namespace App\Livewire\Catalog;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\Setting;
use App\Models\User;
use Livewire\Component;

class Show extends Component
{
    public Book $book;

    public $showBorrowModal = false;

    public $borrowerName = '';

    public $borrowerClass = '';

    public $selectedCopyId = null;

    public $dueDays = 7;

    public function mount(Book $book)
    {
        $this->book = $book;
        $this->book->load(['authors', 'category', 'publisher', 'shelf', 'copies']);
        $this->dueDays = (int) Setting::get('loan_duration_days', 7);
    }

    private function getStaticUser()
    {
        return User::firstOrCreate(
            ['email' => 'peminjam@assaadah.sch.id'],
            [
                'name' => 'Peminjam',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );
    }

    public function openBorrowModal()
    {
        if ($this->book->availableCopies->isEmpty()) {
            session()->flash('error', 'Maaf, stok eksemplar buku ini sedang habis.');

            return;
        }
        $this->selectedCopyId = $this->book->availableCopies->first()?->id;
        $this->showBorrowModal = true;
    }

    public function submitBorrowRequest()
    {
        $this->validate([
            'borrowerName' => 'required|string|max:100',
            'borrowerClass' => 'required|string|max:50',
        ]);

        $copy = BookCopy::find($this->selectedCopyId);
        if (! $copy || $copy->status !== 'tersedia') {
            session()->flash('error', 'Eksemplar buku tidak tersedia.');

            return;
        }

        $user = auth()->user() ?? $this->getStaticUser();

        $existing = Loan::where('user_id', $user->id)
            ->where('book_copy_id', $copy->id)
            ->whereIn('status', ['menunggu', 'dipinjam'])
            ->exists();

        if ($existing) {
            session()->flash('error', 'Buku ini sedang diajukan atau dipinjam.');
            $this->showBorrowModal = false;

            return;
        }

        Loan::create([
            'user_id' => $user->id,
            'book_copy_id' => $copy->id,
            'borrower_name' => trim($this->borrowerName),
            'borrower_class' => trim($this->borrowerClass),
            'borrowed_at' => now(),
            'due_at' => now()->addDays((int) $this->dueDays),
            'status' => 'menunggu',
            'notes' => 'Peminjam: '.trim($this->borrowerName).' ('.trim($this->borrowerClass).')',
        ]);

        $this->showBorrowModal = false;
        $this->reset('borrowerName', 'borrowerClass');
        session()->flash('success', 'Pengajuan peminjaman berhasil dikirim! Menunggu persetujuan petugas perpustakaan.');
    }

    public function toggleFavorite()
    {
        $user = auth()->user() ?? $this->getStaticUser();
        $exists = $user->favorites()->where('book_id', $this->book->id)->exists();
        if ($exists) {
            $user->favorites()->where('book_id', $this->book->id)->delete();
        } else {
            $user->favorites()->create(['book_id' => $this->book->id]);
        }
    }

    public function reserve()
    {
        $user = auth()->user() ?? $this->getStaticUser();
        if ($this->book->type === 'ebook') {
            return redirect()->route('catalog.show', $this->book);
        }
        if ($user->reservations()->where('book_id', $this->book->id)->where('status', 'menunggu')->exists()) {
            session()->flash('error', 'Buku ini sudah direservasi untuk peminjam.');

            return;
        }
        $user->reservations()->create([
            'book_id' => $this->book->id,
            'reserved_at' => now(),
            'status' => 'menunggu',
        ]);
        session()->flash('success', 'Reservasi berhasil diproses.');
    }

    public function render()
    {
        return view('livewire.catalog.show');
    }
}
