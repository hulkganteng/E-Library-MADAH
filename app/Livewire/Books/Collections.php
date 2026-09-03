<?php

namespace App\Livewire\Books;

use App\Models\Book;
use App\Models\BookCopy;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Collections extends Component
{
    public $book_id = null;

    public $addCount = 1;

    public $showQR = null;

    public function addCopies()
    {
        Gate::authorize('eksemplar.create');
        $this->validate([
            'book_id' => 'required|exists:books,id',
            'addCount' => 'required|integer|min:1|max:50',
        ]);

        $book = Book::findOrFail($this->book_id);
        $last = $book->copies()->max('id') ?: 0;

        for ($c = 1; $c <= $this->addCount; $c++) {
            $last++;
            BookCopy::create([
                'book_id' => $book->id,
                'inventory_code' => sprintf('ELIB-%04d-%02d', $book->id, $last),
                'qr_code' => (string) str()->uuid(),
                'condition' => 'baik',
                'status' => 'tersedia',
            ]);
        }

        $this->reset('addCount');
        $this->dispatch('notify', ['message' => "{$this->addCount} eksemplar ditambahkan."]);
    }

    public function updateStatus(BookCopy $copy, string $status)
    {
        Gate::authorize('eksemplar.edit');
        if (in_array($status, ['tersedia', 'rusak', 'hilang'])) {
            $copy->update(['status' => $status]);
        }
    }

    public function delete(BookCopy $copy)
    {
        Gate::authorize('eksemplar.delete');
        $copy->delete();
        $this->dispatch('notify', ['message' => 'Eksemplar dihapus.']);
    }

    public function render()
    {
        $books = Book::where('type', 'fisik')->with('copies')->orderBy('title')->get();

        $copies = BookCopy::with('book')
            ->when($this->book_id, fn ($q) => $q->where('book_id', $this->book_id))
            ->orderBy('inventory_code')
            ->paginate(12);

        return view('livewire.books.collections', [
            'books' => $books,
            'copies' => $copies,
        ]);
    }
}
