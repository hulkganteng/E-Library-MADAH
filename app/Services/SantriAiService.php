<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SantriAiService
{
    public const API_URL = 'https://santriai.com/perpus_digital/api_kitab.php';
    public const HEADER_APP = 'SecureKitabV1';

    /**
     * Kirim request terenkripsi ke API SantriAI Kitabuna
     */
    public function callApi(array $params, int $timeout = 15): array
    {
        $queryString = http_build_query($params);
        $encoded = strrev(base64_encode($queryString));

        try {
            $response = Http::timeout($timeout)
                ->retry(2, 300, throw: false)
                ->withHeaders([
                    'X-Santri-App' => self::HEADER_APP,
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)',
                    'Accept' => 'application/json',
                ])
                ->asForm()
                ->post(self::API_URL, [
                    'q' => $encoded,
                ]);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            return ['success' => false, 'error' => 'Gagal terhubung ke API SantriAI (HTTP ' . $response->status() . ')'];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => 'Koneksi error: ' . $e->getMessage()];
        }
    }

    /**
     * Ambil data global index (kategori, penulis, dan daftar kitab)
     */
    public function getGlobalIndex(): array
    {
        return Cache::store('file')->remember('santriai_global_index', 86400, function () {
            $res = $this->callApi(['action' => 'global_index'], 20);
            return $res['data'] ?? [
                'categories' => [],
                'authors' => [],
                'books' => [],
            ];
        });
    }

    /**
     * Ambil daftar kategori
     */
    public function getCategories(): array
    {
        $index = $this->getGlobalIndex();
        return $index['categories'] ?? [];
    }

    /**
     * Ambil daftar penulis
     */
    public function getAuthors(): array
    {
        $index = $this->getGlobalIndex();
        $authors = $index['authors'] ?? [];
        return array_column($authors, 'name', 'id');
    }

    /**
     * Ambil daftar kitab dengan filter kategori atau judul/penulis
     */
    public function getBooks(?int $categoryId = null, ?string $query = null, int $limit = 24, int $offset = 0): array
    {
        $index = $this->getGlobalIndex();
        $books = $index['books'] ?? [];
        $categories = $index['categories'] ?? [];
        $rawAuthors = $index['authors'] ?? [];
        $authors = array_column($rawAuthors, 'name', 'id');

        if (empty($books)) {
            return [
                'items' => [],
                'total' => 0,
                'error' => 'Katalog SantriAI sedang tidak dapat diakses.',
            ];
        }

        // Filter kategori
        if (! empty($categoryId)) {
            $books = array_values(array_filter($books, fn ($b) => (int) ($b['cat_id'] ?? 0) === (int) $categoryId));
        }

        // Filter pencarian
        if (! empty(trim((string) $query))) {
            $q = mb_strtolower(trim($query));
            $books = array_values(array_filter($books, function ($b) use ($q, $authors) {
                $name = mb_strtolower($b['name'] ?? '');
                $authorName = mb_strtolower($authors[$b['author_id'] ?? 0] ?? '');
                return str_contains($name, $q) || str_contains($authorName, $q);
            }));
        }

        $total = count($books);
        $sliced = array_slice($books, $offset, $limit);

        $items = [];
        foreach ($sliced as $b) {
            $catId = $b['cat_id'] ?? 0;
            $authorId = $b['author_id'] ?? 0;

            $items[] = [
                'id' => $b['id'],
                'title' => $b['name'] ?? 'Tanpa Judul',
                'author_id' => $authorId,
                'author' => $authors[$authorId] ?? 'Ulama / Penulis Tidak Diketahui',
                'cat_id' => $catId,
                'category' => $categories[$catId] ?? 'Kitab Kuning / Turots',
                'source' => 'SantriAI Kitabuna',
            ];
        }

        return [
            'items' => $items,
            'total' => $total,
            'error' => null,
        ];
    }

    /**
     * Ambil detail metadata kitab & daftar isi / headings
     */
    public function getBookMeta(int $bookId): array
    {
        $cacheKey = "santriai_book_meta_{$bookId}";
        return Cache::remember($cacheKey, 3600, function () use ($bookId) {
            $res = $this->callApi([
                'action' => 'book_meta',
                'id' => $bookId,
            ]);

            $meta = $res['meta'] ?? [];
            if (! empty($meta['headings']) && is_array($meta['headings'])) {
                $maxPage = max(array_column($meta['headings'], 'page'));
                if ($maxPage > ($meta['total_pages'] ?? 0)) {
                    $meta['total_pages'] = $maxPage;
                }
            }

            return $meta;
        });
    }

    /**
     * Ambil teks halaman kitab tertentu
     */
    public function getBookPage(int $bookId, int $page): array
    {
        $cacheKey = "santriai_book_{$bookId}_page_{$page}";
        return Cache::remember($cacheKey, 86400, function () use ($bookId, $page) {
            $res = $this->callApi([
                'action' => 'page',
                'id' => $bookId,
                'page' => $page,
            ]);

            return $res;
        });
    }

    /**
     * Pencarian teks ibarot global atau dalam satu kitab
     */
    public function searchIbarot(string $query, ?int $bookId = null, ?int $categoryId = null): array
    {
        $params = [
            'action' => 'search',
            'query' => $query,
        ];

        if (! empty($bookId)) {
            $params['book_id'] = $bookId;
        }
        if (! empty($categoryId)) {
            $params['category'] = $categoryId;
        }

        $res = $this->callApi($params, 20);
        return $res['results'] ?? [];
    }

    /**
     * Impor kitab SantriAI ke database lokal E-Library
     */
    public function importToLocal(array $item): Book
    {
        $pubName = 'Maktabah Digital SantriAI (Kitabuna)';
        $publisher = Publisher::firstOrCreate(['name' => $pubName]);

        $catName = trim($item['category'] ?? 'Kitab Kuning');
        $category = Category::firstOrCreate(
            ['name' => $catName],
            ['slug' => str()->slug($catName) . '-' . uniqid()]
        );

        $bookId = $item['id'] ?? uniqid();
        $baseSlug = str()->slug($item['title'] ?? 'kitab');
        $slug = ($baseSlug ?: 'kitab-' . $bookId) . '-' . uniqid();

        // Ambil detail meta untuk info lebih lengkap bila ada
        $meta = $this->getBookMeta((int) $bookId);
        $totalPages = ! empty($meta['total_pages']) ? (int) $meta['total_pages'] : null;

        $desc = "Kitab Digital dari Maktabah SantriAI (Kitabuna).\nKategori: {$catName}\nPenulis: " . ($item['author'] ?? 'Ulama');

        $book = Book::create([
            'title' => $item['title'] ?? 'Tanpa Judul',
            'slug' => $slug,
            'isbn' => null,
            'publish_year' => null,
            'page_count' => $totalPages,
            'description' => $desc,
            'publisher_id' => $publisher->id,
            'category_id' => $category->id,
            'type' => 'ebook',
            'cover' => null,
            'ebook_file' => "https://santriai.com/perpus_digital/?book_id={$bookId}",
            'is_repository' => false,
            'is_textbook' => false,
            'borrow_count' => 0,
        ]);

        if (! empty($item['author'])) {
            $wClean = trim($item['author']);
            if ($wClean !== '') {
                $authorSlug = str()->slug($wClean) ?: 'author-' . uniqid();
                $author = Author::firstOrCreate(
                    ['name' => $wClean],
                    ['slug' => $authorSlug . '-' . uniqid()]
                );
                $book->authors()->sync([$author->id]);
            }
        }

        BookCopy::create([
            'book_id' => $book->id,
            'inventory_code' => sprintf('KTB-%04d-01', $book->id),
            'qr_code' => (string) str()->uuid(),
            'condition' => 'baik',
            'status' => 'tersedia',
        ]);

        return $book;
    }
}
