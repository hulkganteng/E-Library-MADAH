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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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

    public ?string $apiCoverUrl = null;

    public $authors = [];

    public $initial_copies = 1;

    public $importFile;

    // Properti Uji Coba API
    public bool $showApiModal = false;

    public string $apiSource = 'openlibrary';

    public string $apiQuery = '';

    public ?string $googleApiKey = '';

    public array $apiResults = [];

    public ?string $apiError = null;

    public ?string $apiRawJson = null;

    public bool $showRawJson = false;

    public ?string $lastExecutedUrl = null;

    public ?float $lastResponseTime = null;

    public string $newAuthorName = '';

    public string $newPublisherName = '';

    public bool $showNewPublisher = false;

    public array $apiQuickResults = [];

    public bool $showAdvanced = false;

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\BooksTemplateExport, 'template-import-buku.xlsx');
    }

    public function import()
    {
        Gate::authorize('buku.import');
        $this->validate(['importFile' => 'required|file|mimes:xlsx,xls,csv|max:4096']);
        $import = new BooksImport;
        Excel::import($import, $this->importFile->getRealPath());

        $msg = "Import selesai. {$import->importedCount} buku berhasil diimpor.";
        if ($import->skippedCount > 0) {
            $msg .= " {$import->skippedCount} buku dilewati karena duplikat.";
        }
        $otherErrors = count($import->failed) - $import->skippedCount;
        if ($otherErrors > 0) {
            $msg .= " {$otherErrors} baris gagal.";
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
        $this->reset('editing', 'title', 'category_id', 'publisher_id', 'shelf_id', 'isbn', 'edition', 'publish_year', 'page_count', 'description', 'cover', 'apiCoverUrl', 'newAuthorName', 'newPublisherName', 'showNewPublisher', 'apiQuickResults', 'apiQuery');
        $this->authors = [];
        $this->type = 'fisik';
        $this->is_repository = false;
        $this->initial_copies = 1;
        $this->showAdvanced = false;
        $this->showModal = true;
    }

    public function edit(Book $book)
    {
        Gate::authorize('buku.edit');
        $this->editing = $book->id;
        $this->apiCoverUrl = null;
        $this->showAdvanced = true;
        $this->apiQuickResults = [];
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
        } elseif ($this->apiCoverUrl && ! $this->editing) {
            try {
                $imgRes = Http::timeout(8)->get($this->apiCoverUrl);
                if ($imgRes->successful()) {
                    $filename = 'covers/'.uniqid().'.jpg';
                    Storage::disk('public')->put($filename, $imgRes->body());
                    $data['cover'] = $filename;
                }
            } catch (\Throwable $e) {
                // ignore
            }
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

    public function selectAuthor($id)
    {
        if ($id && ! in_array((string) $id, $this->authors, true)) {
            $this->authors[] = (string) $id;
        }
    }

    public function removeAuthor($id)
    {
        $this->authors = array_values(array_filter($this->authors, fn ($a) => (string) $a !== (string) $id));
    }

    public function addAuthor()
    {
        $name = trim($this->newAuthorName);
        if ($name !== '') {
            $slug = str()->slug($name).'-'.uniqid();
            $author = Author::firstOrCreate(['name' => $name], ['slug' => $slug]);
            if (! in_array((string) $author->id, $this->authors, true)) {
                $this->authors[] = (string) $author->id;
            }
            $this->newAuthorName = '';
        }
    }

    public function createPublisher()
    {
        $name = trim($this->newPublisherName);
        if ($name !== '') {
            $pub = Publisher::firstOrCreate(['name' => $name]);
            $this->publisher_id = $pub->id;
            $this->newPublisherName = '';
            $this->showNewPublisher = false;
        }
    }

    public function searchApiInModal()
    {
        $this->searchApi();
        $this->apiQuickResults = array_slice($this->apiResults, 0, 3);
    }

    public function applyQuickApiBook(int $index)
    {
        $item = $this->apiQuickResults[$index] ?? null;
        if (! $item) {
            return;
        }

        $this->title = $item['title'];
        $this->isbn = $item['isbn'];
        $this->publish_year = ! empty($item['publish_year']) ? (int) $item['publish_year'] : null;
        $this->page_count = ! empty($item['page_count']) ? (int) $item['page_count'] : null;
        $this->description = $item['description'];
        $this->apiCoverUrl = $item['cover_url'];

        if (! empty($item['publisher'])) {
            $pub = Publisher::firstOrCreate(['name' => trim($item['publisher'])]);
            $this->publisher_id = $pub->id;
        }

        if (! empty($item['authors']) && is_array($item['authors'])) {
            $authorIds = [];
            foreach ($item['authors'] as $authorName) {
                $name = trim($authorName);
                if ($name !== '') {
                    $slug = str()->slug($name).'-'.uniqid();
                    $author = Author::firstOrCreate(['name' => $name], ['slug' => $slug]);
                    $authorIds[] = (string) $author->id;
                }
            }
            $this->authors = $authorIds;
        }

        if (! empty($item['categories']) && is_array($item['categories'])) {
            foreach ($item['categories'] as $catName) {
                $cleanCat = trim(explode('/', $catName)[0]);
                $matched = Category::where('name', 'like', "%{$cleanCat}%")->first();
                if ($matched) {
                    $this->category_id = $matched->id;
                    break;
                }
            }
        }

        $this->apiQuickResults = [];
        $this->showAdvanced = true;
        $this->dispatch('notify', ['message' => 'Data buku otomatis diterapkan ke form!']);
    }

    public function openApiModal()
    {
        Gate::authorize('buku.create');
        $this->apiQuery = '';
        $this->apiResults = [];
        $this->apiError = null;
        $this->apiRawJson = null;
        $this->showRawJson = false;
        $this->lastExecutedUrl = null;
        $this->lastResponseTime = null;
        $this->showApiModal = true;
    }

    public function setQueryAndSearch(string $query, ?string $source = null)
    {
        if ($source) {
            $this->apiSource = $source;
        }
        $this->apiQuery = $query;
        $this->searchApi();
    }

    public function searchApi()
    {
        Gate::authorize('buku.create');

        $query = trim($this->apiQuery);
        if (strlen($query) < 2) {
            $this->apiError = 'Masukkan minimal 2 karakter untuk mencari buku.';
            $this->apiResults = [];
            return;
        }

        $this->apiError = null;
        $this->apiResults = [];
        $this->apiRawJson = null;

        $startTime = microtime(true);

        try {
            if ($this->apiSource === 'google') {
                $params = [
                    'q' => $query,
                    'maxResults' => 10,
                ];
                $key = trim($this->googleApiKey ?: (config('services.google_books.key') ?? env('GOOGLE_BOOKS_API_KEY', '')));
                if ($key !== '') {
                    $params['key'] = $key;
                }

                $endpoint = 'https://www.googleapis.com/books/v1/volumes';
                $this->lastExecutedUrl = $endpoint.'?'.http_build_query($params);

                $response = Http::timeout(15)->get($endpoint, $params);
                $this->lastResponseTime = round((microtime(true) - $startTime) * 1000);

                if ($response->status() === 429) {
                    $this->apiError = 'Google Books API: Kuota request terlampaui (429 Quota Exceeded). Silakan coba sumber Open Library atau masukkan Google Books API Key.';
                    $this->apiRawJson = json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    return;
                }

                if (! $response->successful()) {
                    $this->apiError = 'Google Books API error: status '.$response->status().' - '.($response->json('error.message') ?? $response->body());
                    $this->apiRawJson = json_encode($response->json() ?: $response->body(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    return;
                }

                $data = $response->json();
                $this->apiRawJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

                $items = $data['items'] ?? [];
                if (empty($items)) {
                    $this->apiError = 'Tidak ada buku ditemukan untuk pencarian "'.$query.'".';
                    return;
                }

                $results = [];
                foreach ($items as $item) {
                    $info = $item['volumeInfo'] ?? [];
                    $isbn = null;
                    foreach ($info['industryIdentifiers'] ?? [] as $idObj) {
                        if (($idObj['type'] ?? '') === 'ISBN_13') {
                            $isbn = $idObj['identifier'];
                            break;
                        } elseif (($idObj['type'] ?? '') === 'ISBN_10' && ! $isbn) {
                            $isbn = $idObj['identifier'];
                        }
                    }

                    $year = null;
                    if (! empty($info['publishedDate'])) {
                        $y = (int) substr($info['publishedDate'], 0, 4);
                        if ($y >= 1800 && $y <= ((int) date('Y') + 1)) {
                            $year = $y;
                        }
                    }

                    $cover = $info['imageLinks']['thumbnail'] ?? $info['imageLinks']['smallThumbnail'] ?? null;
                    if ($cover) {
                        $cover = str_replace('http://', 'https://', $cover);
                    }

                    $results[] = [
                        'id' => $item['id'] ?? uniqid(),
                        'source' => 'Google Books API',
                        'title' => $info['title'] ?? 'Tanpa Judul',
                        'authors' => $info['authors'] ?? [],
                        'publisher' => $info['publisher'] ?? null,
                        'publish_year' => $year,
                        'page_count' => $info['pageCount'] ?? null,
                        'isbn' => $isbn,
                        'edition' => null,
                        'description' => isset($info['description']) ? strip_tags($info['description']) : null,
                        'cover_url' => $cover,
                        'categories' => $info['categories'] ?? [],
                    ];
                }

                $this->apiResults = $results;
            } else {
                // Open Library
                $endpoint = 'https://openlibrary.org/search.json';
                $params = [
                    'title' => $query,
                    'fields' => 'key,title,author_name,publisher,first_publish_year,publish_year,isbn,number_of_pages_median,cover_i,subject',
                    'limit' => 10,
                ];
                $this->lastExecutedUrl = $endpoint.'?'.http_build_query($params);

                $response = Http::timeout(20)
                    ->withHeaders(['User-Agent' => 'ELibrary/1.0 (library@maassadah.sch.id)'])
                    ->get($endpoint, $params);

                $this->lastResponseTime = round((microtime(true) - $startTime) * 1000);

                if (! $response->successful()) {
                    $this->apiError = 'Open Library API error: status '.$response->status();
                    $this->apiRawJson = json_encode($response->json() ?: $response->body(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    return;
                }

                $data = $response->json();
                $this->apiRawJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

                $docs = $data['docs'] ?? [];
                if (empty($docs)) {
                    $this->apiError = 'Tidak ada buku ditemukan untuk pencarian "'.$query.'".';
                    return;
                }

                $results = [];
                foreach ($docs as $doc) {
                    $year = $doc['first_publish_year'] ?? ($doc['publish_year'][0] ?? null);
                    $isbn = isset($doc['isbn']) && is_array($doc['isbn']) ? $doc['isbn'][0] : null;
                    $publisher = isset($doc['publisher']) && is_array($doc['publisher']) ? $doc['publisher'][0] : null;
                    $coverUrl = ! empty($doc['cover_i']) ? "https://covers.openlibrary.org/b/id/{$doc['cover_i']}-M.jpg" : null;

                    $results[] = [
                        'id' => $doc['key'] ?? uniqid(),
                        'source' => 'Open Library API',
                        'title' => $doc['title'] ?? 'Tanpa Judul',
                        'authors' => $doc['author_name'] ?? [],
                        'publisher' => $publisher,
                        'publish_year' => $year ? (int) $year : null,
                        'page_count' => $doc['number_of_pages_median'] ?? null,
                        'isbn' => $isbn,
                        'edition' => null,
                        'description' => null,
                        'cover_url' => $coverUrl,
                        'categories' => array_slice($doc['subject'] ?? [], 0, 3),
                    ];
                }

                $this->apiResults = $results;
            }
        } catch (\Throwable $e) {
            $this->lastResponseTime = round((microtime(true) - $startTime) * 1000);
            $this->apiError = 'Koneksi ke API gagal: '.$e->getMessage();
        }
    }

    public function useApiBook(int $index)
    {
        Gate::authorize('buku.create');

        $item = $this->apiResults[$index] ?? null;
        if (! $item) {
            return;
        }

        $this->create();

        $this->title = $item['title'];
        $this->isbn = $item['isbn'];
        $this->publish_year = ! empty($item['publish_year']) ? (int) $item['publish_year'] : null;
        $this->page_count = ! empty($item['page_count']) ? (int) $item['page_count'] : null;
        $this->description = $item['description'];
        $this->apiCoverUrl = $item['cover_url'];

        if (! empty($item['publisher'])) {
            $pub = Publisher::firstOrCreate(['name' => trim($item['publisher'])]);
            $this->publisher_id = $pub->id;
        }

        if (! empty($item['authors']) && is_array($item['authors'])) {
            $authorIds = [];
            foreach ($item['authors'] as $authorName) {
                $name = trim($authorName);
                if ($name !== '') {
                    $slug = str()->slug($name).'-'.uniqid();
                    $author = Author::firstOrCreate(
                        ['name' => $name],
                        ['slug' => $slug]
                    );
                    $authorIds[] = (string) $author->id;
                }
            }
            $this->authors = $authorIds;
        }

        if (! empty($item['categories']) && is_array($item['categories'])) {
            foreach ($item['categories'] as $catName) {
                $cleanCat = trim(explode('/', $catName)[0]);
                $matched = Category::where('name', 'like', "%{$cleanCat}%")->first();
                if ($matched) {
                    $this->category_id = $matched->id;
                    break;
                }
            }
        }

        $this->showApiModal = false;
        $this->showModal = true;
        $this->dispatch('notify', ['message' => 'Data dari API berhasil dipindahkan ke form tambah buku.']);
    }

    public function directSaveApiBook(int $index)
    {
        Gate::authorize('buku.create');

        $item = $this->apiResults[$index] ?? null;
        if (! $item) {
            return;
        }

        if (! empty($item['isbn'])) {
            $existing = Book::where('isbn', $item['isbn'])->first();
            if ($existing) {
                $this->dispatch('notify', ['message' => 'Buku dengan ISBN ini sudah terdaftar: '.$existing->title]);
                return;
            }
        }

        $publisherId = null;
        if (! empty($item['publisher'])) {
            $pub = Publisher::firstOrCreate(['name' => trim($item['publisher'])]);
            $publisherId = $pub->id;
        }

        $categoryId = null;
        if (! empty($item['categories']) && is_array($item['categories'])) {
            foreach ($item['categories'] as $catName) {
                $cleanCat = trim(explode('/', $catName)[0]);
                $matched = Category::where('name', 'like', "%{$cleanCat}%")->first();
                if ($matched) {
                    $categoryId = $matched->id;
                    break;
                }
            }
        }

        $coverPath = null;
        if (! empty($item['cover_url'])) {
            try {
                $imgRes = Http::timeout(8)->get($item['cover_url']);
                if ($imgRes->successful()) {
                    $filename = 'covers/'.uniqid().'.jpg';
                    Storage::disk('public')->put($filename, $imgRes->body());
                    $coverPath = $filename;
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $slug = str()->slug($item['title']).'-'.uniqid();

        $book = Book::create([
            'title' => $item['title'],
            'slug' => $slug,
            'isbn' => $item['isbn'] ?? null,
            'publish_year' => ! empty($item['publish_year']) ? (int) $item['publish_year'] : null,
            'page_count' => ! empty($item['page_count']) ? (int) $item['page_count'] : null,
            'description' => $item['description'] ?? null,
            'publisher_id' => $publisherId,
            'category_id' => $categoryId,
            'type' => 'fisik',
            'cover' => $coverPath,
            'is_repository' => false,
        ]);

        if (! empty($item['authors']) && is_array($item['authors'])) {
            $authorIds = [];
            foreach ($item['authors'] as $authorName) {
                $name = trim($authorName);
                if ($name !== '') {
                    $slug = str()->slug($name).'-'.uniqid();
                    $author = Author::firstOrCreate(
                        ['name' => $name],
                        ['slug' => $slug]
                    );
                    $authorIds[] = $author->id;
                }
            }
            $book->authors()->sync($authorIds);
        }

        BookCopy::create([
            'book_id' => $book->id,
            'inventory_code' => sprintf('ELIB-%04d-01', $book->id),
            'qr_code' => (string) str()->uuid(),
            'condition' => 'baik',
            'status' => 'tersedia',
        ]);

        $this->showApiModal = false;
        $this->dispatch('notify', ['message' => "Buku \"{$book->title}\" berhasil disimpan dari API beserta 1 eksemplar."]);
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
