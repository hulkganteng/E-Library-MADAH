<?php

namespace App\Livewire\Catalog;

use Livewire\Component;
use App\Models\Book;

class Show extends Component
{
    public Book $book;

    public function mount(Book $book)
    {
        $this->book = $book;
        $this->book->load(['authors', 'category', 'publisher', 'shelf', 'copies']);
    }

    public function toggleFavorite()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $exists = auth()->user()->favorites()->where('book_id', $this->book->id)->exists();
        if ($exists) {
            auth()->user()->favorites()->where('book_id', $this->book->id)->delete();
        } else {
            auth()->user()->favorites()->create(['book_id' => $this->book->id]);
        }
    }

    public function reserve()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        if ($this->book->type === 'ebook') {
            return redirect()->route('catalog.show', $this->book);
        }
        if (auth()->user()->reservations()->where('book_id', $this->book->id)->where('status', 'menunggu')->exists()) {
            session()->flash('error', 'Anda sudah mereservasi buku ini.');
            return;
        }
        auth()->user()->reservations()->create([
            'book_id' => $this->book->id,
            'reserved_at' => now(),
            'status' => 'menunggu',
        ]);
        session()->flash('success', 'Reservasi berhasil. Perpustakaan akan memproses saat buku tersedia.');
    }

    public function render()
    {
        return view('livewire.catalog.show');
    }
}
