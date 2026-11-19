<?php

namespace App\Livewire\Maktabah;

use App\Models\Book;
use App\Services\SantriAiService;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class Index extends Component
{
    public string $mode = 'catalog'; // 'catalog' atau 'ibarot'
    public string $search = '';
    public ?int $selectedCategory = null;
    public int $page = 1;
    public int $perPage = 24;

    // Pencarian Ibarot (Full-text)
    public string $ibarotQuery = '';
    public array $ibarotResults = [];
    public bool $hasSearchedIbarot = false;
    public ?string $ibarotError = null;

    // Reader State
    public bool $showReaderModal = false;
    public ?int $readerBookId = null;
    public array $readerBookMeta = [];
    public int $readerCurrentPage = 1;
    public string $readerPageText = '';
    public string $readerNextPageText = '';
    public bool $readerLoading = false;
    public int $readerFontSize = 20;

    // Tracking buku yang sudah diimpor ke perpustakaan lokal
    public array $importedTitles = [];

    protected $queryString = [
        'mode' => ['except' => 'catalog'],
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => null],
        'page' => ['except' => 1],
    ];

    public function mount()
    {
        $this->loadExistingImported();
    }

    public function loadExistingImported(): void
    {
        if (! Schema::hasTable('books')) {
            return;
        }

        $this->importedTitles = Book::pluck('title')
            ->map(fn ($t) => mb_strtolower(trim($t)))
            ->values()
            ->all();
    }

    public function updatedSearch()
    {
        $this->page = 1;
    }

    public function updatedSelectedCategory()
    {
        $this->page = 1;
    }

    public function setCategory(?int $catId)
    {
        $this->selectedCategory = $catId;
        $this->page = 1;
    }

    public function setMode(string $newMode)
    {
        $this->mode = in_array($newMode, ['catalog', 'ibarot'], true) ? $newMode : 'catalog';
    }

    public function searchIbarot(SantriAiService $service)
    {
        $q = trim($this->ibarotQuery);
        if ($q === '') {
            $this->ibarotResults = [];
            $this->hasSearchedIbarot = false;
            return;
        }

        $this->hasSearchedIbarot = true;
        $this->ibarotError = null;

        $res = $service->searchContent($q, categoryId: $this->selectedCategory);
        if (! empty($res['error'])) {
            $this->ibarotError = $res['error'];
            $this->ibarotResults = [];
        } else {
            $this->ibarotResults = $res['results'] ?? [];
        }
    }

    public function openReader(int $bookId, int $page = 1, SantriAiService $service = null)
    {
        $service = $service ?? app(SantriAiService::class);
        $this->readerBookId = $bookId;
        $this->readerLoading = true;
        $this->showReaderModal = true;

        $meta = $service->getBookMeta($bookId);
        $this->readerBookMeta = $meta;

        // Tentukan halaman awal: jika page 1 dan ada headings, pilih page pertama di headings jika page 1 kosong
        $targetPage = $page;
        if ($targetPage === 1 && ! empty($meta['headings'][0]['page'])) {
            $targetPage = (int) $meta['headings'][0]['page'];
        }

        $this->readerCurrentPage = $targetPage;
        $this->loadPageText($targetPage, $service);
    }

    public function loadPage(int $page, SantriAiService $service = null)
    {
        if (! $this->readerBookId) {
            return;
        }

        $service = $service ?? app(SantriAiService::class);
        $lastPage = (int) ($this->readerBookMeta['total_pages'] ?? PHP_INT_MAX);
        $this->readerCurrentPage = min(max(1, $page), max(1, $lastPage));
        $this->loadPageText($this->readerCurrentPage, $service);
    }

    public function nextPage(SantriAiService $service = null)
    {
        $this->loadPage($this->readerCurrentPage + 1, $service);
    }

    public function prevPage(SantriAiService $service = null)
    {
        if ($this->readerCurrentPage > 1) {
            $this->loadPage($this->readerCurrentPage - 1, $service);
        }
    }

    protected function loadPageText(int $page, SantriAiService $service)
    {
        $this->readerLoading = true;
        $this->readerPageText = $this->getPageText($page, $service);

        $lastPage = (int) ($this->readerBookMeta['total_pages'] ?? $page);
        $this->readerNextPageText = $page < $lastPage
            ? $this->getPageText($page + 1, $service)
            : '';

        $this->readerLoading = false;
    }

    private function getPageText(int $page, SantriAiService $service): string
    {
        $pageData = $service->getBookPage($this->readerBookId, $page);

        if (! empty($pageData['text'])) {
            return $pageData['text'];
        }

        if (! empty($pageData['error'])) {
            return "({$pageData['error']})";
        }

        return '(Halaman tidak berisi teks atau kosong)';
    }

    public function increaseFontSize()
    {
        if ($this->readerFontSize < 34) {
            $this->readerFontSize += 2;
        }
    }

    public function decreaseFontSize()
    {
        if ($this->readerFontSize > 14) {
            $this->readerFontSize -= 2;
        }
    }

    public function closeReader()
    {
        $this->showReaderModal = false;
        $this->readerBookId = null;
        $this->readerBookMeta = [];
        $this->readerPageText = '';
        $this->readerNextPageText = '';
    }

    public function isAlreadyImported(string $title): bool
    {
        return in_array(mb_strtolower(trim($title)), $this->importedTitles, true);
    }

    public function importBook(array $item, SantriAiService $service)
    {
        if (! auth()->check() || ! auth()->user()->can('buku.create')) {
            $this->dispatch('notify', [
                'message' => 'Anda tidak memiliki hak akses untuk mengimpor kitab.',
                'type' => 'error',
            ]);
            return;
        }

        $title = $item['title'] ?? $item['name'] ?? '';
        if ($this->isAlreadyImported($title)) {
            $this->dispatch('notify', [
                'message' => "Kitab \"{$title}\" sudah ada di katalog perpustakaan.",
                'type' => 'warning',
            ]);
            return;
        }

        try {
            $book = $service->importToLocal($item);
            $this->importedTitles[] = mb_strtolower(trim($book->title));

            $this->dispatch('notify', [
                'message' => "Kitab \"{$book->title}\" berhasil diimpor ke koleksi perpustakaan!",
                'type' => 'success',
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('notify', [
                'message' => 'Gagal mengimpor kitab: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function render(SantriAiService $service)
    {
        $categories = $service->getCategories();
        $offset = ($this->page - 1) * $this->perPage;

        $response = $service->getBooks(
            categoryId: $this->selectedCategory,
            query: $this->search,
            limit: $this->perPage,
            offset: $offset
        );

        $books = $response['items'] ?? [];
        $total = $response['total'] ?? 0;
        $totalPages = $total > 0 ? (int) ceil($total / $this->perPage) : 1;

        return view('livewire.maktabah.index', [
            'mode' => $this->mode,
            'categories' => $categories,
            'books' => $books,
            'total' => $total,
            'totalPages' => $totalPages,
        ])->layout('components.layouts.app', [
            'title' => 'Maktabah Digital SantriAI — E-Library',
        ]);
    }
}
