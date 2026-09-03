<?php

namespace App\Livewire\Books;

use App\Imports\BooksImport;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\Shelf;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public $showModal = false;

    public $editing = null;

    public $title;

    public $category_id;

    public $publisher_id;

    public $shelf_id;

    public $isbn;

    public $edition;

    public $publish_year;

    public $page_count;

    public $type = 'fisik';

    public $description;

    public $is_repository = false;

    public $cover;

    public $authors = [];

    public $initial_copies = 1;

    public $importFile;

    public function import()
    {
        Gate::authorize('buku.import');
        $this->validate(['importFile' => 'required|file|mimes:xlsx,xls,csv|max:4096']);
        $import = new BooksImport;
        Excel::import($import, $this->importFile->getRealPath());
        $msg = 'Import selesai.';
        if ($import->failed) {
            $msg .= ' '.count($import->failed).' baris gagal.';
        }
        $this->reset('importFile');
        $this->dispatch('notify', ['message' => $msg]);
    }

    protected $listeners = ['refresh' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        Gate::authorize('buku.create');
        $this->reset('editing', 'title', 'category_id', 'publisher_id', 'shelf_id', 'isbn', 'edition', 'publish_year', 'page_count', 'description', 'cover');
        $this->authors = [];
        $this->type = 'fisik';
        $this->is_repository = false;
        $this->initial_copies = 1;
        $this->showModal = true;
    }

    public function edit(Book $book)
    {
        Gate::authorize('buku.edit');
        $this->editing = $book->id;
        $this->fill($book->only('title', 'category_id', 'publisher_id', 'shelf_id', 'isbn', 'edition', 'publish_year', 'page_count', 'type', 'description', 'is_repository'));
        $this->authors = $book->authors->pluck('id')->map(fn ($v) => (string) $v)->toArray();
        $this->showModal = true;
    }

    public function save()
    {
        Gate::authorize($this->editing ? 'buku.edit' : 'buku.create');
        $data = $this->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'publisher_id' => 'nullable|exists:publishers,id',
            'shelf_id' => 'nullable|exists:shelves,id',
            'isbn' => 'nullable|string|max:20|unique:books,isbn,'.$this->editing,
            'edition' => 'nullable|string|max:50',
            'publish_year' => 'nullable|integer|min:1900|max:'.now()->year,
            'page_count' => 'nullable|integer|min:1',
            'type' => 'required|in:fisik,ebook',
            'description' => 'nullable|string',
            'cover' => 'nullable|image|max:2048',
            'initial_copies' => 'required|integer|min:0|max:100',
        ]);

        if ($this->cover) {
            $data['cover'] = $this->cover->store('covers', 'public');
        }

        $slug = str()->slug($this->title).'-'.uniqid();
        $book = $this->editing ? Book::findOrFail($this->editing) : new Book;
        $book->fill($data);
        $book->slug = $this->editing ? $book->slug : $slug;
        $book->is_repository = (bool) $this->is_repository;
        $book->save();

        $book->authors()->sync($this->authors);

        if (! $this->editing && $this->type === 'fisik' && $this->initial_copies > 0) {
            for ($c = 1; $c <= $this->initial_copies; $c++) {
                BookCopy::create([
                    'book_id' => $book->id,
                    'inventory_code' => sprintf('ELIB-%04d-%02d', $book->id, $c),
                    'qr_code' => (string) str()->uuid(),
                    'condition' => 'baik',
                    'status' => 'tersedia',
                ]);
            }
        }

        $this->showModal = false;
        $this->dispatch('notify', ['message' => 'Buku berhasil disimpan.']);
    }

    public function delete(Book $book)
    {
        Gate::authorize('buku.delete');
        $book->delete();
        $this->dispatch('notify', ['message' => 'Buku dihapus.']);
    }

    public function render()
    {
        $books = Book::with(['category', 'authors', 'copies'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.books.index', [
            'books' => $books,
            'categories' => Category::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
            'shelves' => Shelf::orderBy('code')->get(),
            'authorList' => Author::orderBy('name')->get(),
        ]);
    }
}
