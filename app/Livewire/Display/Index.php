<?php

namespace App\Livewire\Display;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Book, Category};

#[Layout('components.layouts.kiosk')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';
    public ?int $category = null;
    public string $type = '';
    public ?int $selectedId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clear()
    {
        $this->reset(['search', 'category', 'type']);
        $this->resetPage();
    }

    public function openDetail($id)
    {
        $this->selectedId = (int) $id;
    }

    public function closeDetail()
    {
        $this->selectedId = null;
    }

    public function render()
    {
        $books = Book::query()
            ->with(['authors', 'category', 'shelf', 'copies'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('isbn', 'like', "%{$this->search}%")
                  ->orWhereHas('authors', fn ($a) => $a->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->category, fn ($q) => $q->where('category_id', $this->category))
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->orderBy('title')
            ->paginate(24);

        $selected = $this->selectedId
            ? Book::with(['authors', 'category', 'publisher', 'shelf', 'copies'])->find($this->selectedId)
            : null;

        return view('livewire.display.index', [
            'books' => $books,
            'categories' => Category::orderBy('name')->get(),
            'selected' => $selected,
        ]);
    }
}
