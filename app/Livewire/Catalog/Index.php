<?php

namespace App\Livewire\Catalog;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Book, Category};

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $category = null;
    public string $type = '';

    protected $queryString = ['search', 'category', 'type'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clear()
    {
        $this->reset(['search', 'category', 'type']);
        $this->resetPage();
    }

    public function render()
    {
        $books = Book::query()
            ->with(['authors', 'category', 'copies'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('isbn', 'like', "%{$this->search}%")
                  ->orWhereHas('authors', fn ($a) => $a->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->category, fn ($q) => $q->where('category_id', $this->category))
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->orderBy('title')
            ->paginate(12);

        return view('livewire.catalog.index', [
            'books' => $books,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
