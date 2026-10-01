<?php

namespace App\Livewire\Sibi;

use App\Models\Book;
use App\Services\SibiService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';
    public string $curriculum = 'merdeka';
    public string $level = 'SMA';
    public string $selectedClass = '';
    public int $page = 1;
    public int $perPage = 24;

    public ?string $activePdfUrl = null;
    public ?string $activeBookTitle = null;
    public bool $showReaderModal = false;

    public array $importedIsbns = [];
    public array $importedTitles = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'curriculum' => ['except' => 'merdeka'],
        'level' => ['except' => 'SMA'],
        'selectedClass' => ['except' => ''],
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

        $this->importedIsbns = Book::whereNotNull('isbn')
            ->pluck('isbn')
            ->filter()
            ->values()
            ->all();

        $this->importedTitles = Book::pluck('title')
            ->map(fn ($t) => mb_strtolower(trim($t)))
            ->values()
            ->all();
    }

    public function updatedSearch()
    {
        $this->page = 1;
    }

    public function updatedCurriculum()
    {
        $this->page = 1;
    }

    public function updatedLevel()
    {
        $this->page = 1;
    }

    public function updatedSelectedClass()
    {
        $this->page = 1;
    }

    public function refreshCatalog()
    {
        \Illuminate\Support\Facades\Cache::forget("sibi_catalog_raw_{$this->curriculum}");
        \Illuminate\Support\Facades\Cache::forget("kemenag_madrasah_books_raw");
        $this->page = 1;
        $this->dispatch('notify', ['message' => 'Memuat ulang data dari server pusat...']);
    }

    public function nextPage(int $totalPages)
    {
        if ($this->page < $totalPages) {
            $this->page++;
        }
    }

    public function previousPage()
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }

    public function goToPage(int $page)
    {
        $this->page = max(1, $page);
    }

    public function openReader(string $pdfUrl, string $title)
    {
        $this->activePdfUrl = $pdfUrl;
        $this->activeBookTitle = $title;
        $this->showReaderModal = true;
    }

    public function closeReader()
    {
        $this->showReaderModal = false;
        $this->activePdfUrl = null;
        $this->activeBookTitle = null;
    }

    public function import(array $item, SibiService $service)
    {
        if (! auth()->check() || ! auth()->user()->can('buku.create')) {
            $this->dispatch('notify', [
                'message' => 'Anda tidak memiliki hak akses untuk mengimpor buku.',
                'type' => 'error',
            ]);
            return;
        }

        $title = $item['title'] ?? '';
        $isbn = $item['isbn'] ?? null;

        if ($this->isAlreadyImported($isbn, $title)) {
            $this->dispatch('notify', [
                'message' => "Buku \"{$title}\" sudah ada di database perpustakaan.",
                'type' => 'warning',
            ]);
            return;
        }

        try {
            $book = $service->importToLocal($item);

            if ($book->isbn) {
                $this->importedIsbns[] = $book->isbn;
            }
            $this->importedTitles[] = mb_strtolower(trim($book->title));

            $this->dispatch('notify', [
                'message' => "Buku \"{$book->title}\" berhasil diimpor ke koleksi perpustakaan!",
                'type' => 'success',
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('notify', [
                'message' => 'Gagal mengimpor buku: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function isAlreadyImported(?string $isbn, string $title): bool
    {
        if ($isbn && in_array($isbn, $this->importedIsbns, true)) {
            return true;
        }

        return in_array(mb_strtolower(trim($title)), $this->importedTitles, true);
    }

    public function render(SibiService $service)
    {
        $offset = ($this->page - 1) * $this->perPage;
        $response = $service->getBooks(
            category: $this->curriculum,
            query: $this->search,
            level: $this->level,
            class: $this->selectedClass,
            limit: $this->perPage,
            offset: $offset
        );

        $items = $response['items'] ?? [];
        $total = $response['total'] ?? count($items);
        $error = $response['error'] ?? null;
        $totalPages = $total > 0 ? (int) ceil($total / $this->perPage) : 1;

        return view('livewire.sibi.index', [
            'books' => $items,
            'total' => $total,
            'totalPages' => $totalPages,
            'error' => $error,
        ]);
    }
}
