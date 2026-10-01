<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SibiService
{
    private const CLOUDAPP_BASE = 'https://api.buku.cloudapp.web.id/api/catalogue';
    private const KEMDIK_BASE = 'https://api.buku.kemendikdasmen.go.id';
    private const SIKURMA_BASE = 'https://sikurma.kemenag.go.id/portal/Buku';

    /**
     * Ambil koleksi buku SIBI / Kemenag dengan caching & filter jenjang/kata kunci
     */
    public function getBooks(string $category = 'merdeka', string $query = '', string $level = 'SMA', ?string $class = '', int $limit = 24, int $offset = 0): array
    {
        $category = in_array($category, ['merdeka', 'k13', 'nonteks', 'btp', 'kemenag']) ? $category : 'merdeka';

        if ($category === 'kemenag') {
            return $this->getKemenagBooks($query, $level, $class, $limit, $offset);
        }

        if ($category === 'btp') {
            return $this->getFromKemdikdasmen($query, $level, $class, $limit, $offset);
        }

        $allBooks = $this->getCachedCloudappBooks($category);
        if (isset($allBooks['error'])) {
            return ['items' => [], 'total' => 0, 'error' => $allBooks['error']];
        }

        $items = $allBooks['items'] ?? [];

        // 1. Filter Level / Jenjang (Default: SMA / MA / SMK)
        if ($level !== '' && $level !== 'ALL') {
            $items = array_values(array_filter($items, function ($item) use ($level) {
                $lvl = strtoupper($item['level'] ?? '');
                $cls = strtoupper($item['class'] ?? '');

                if ($level === 'SMA') {
                    // Filter SMA / MA / SMK
                    return str_contains($lvl, 'SMA') ||
                           str_contains($lvl, 'MA') ||
                           str_contains($lvl, 'SMK') ||
                           str_contains($cls, '10') ||
                           str_contains($cls, '11') ||
                           str_contains($cls, '12') ||
                           str_contains($cls, 'X') ||
                           str_contains($cls, 'XI') ||
                           str_contains($cls, 'XII');
                }

                return str_contains($lvl, strtoupper($level));
            }));
        }

        // 2. Filter Kelas (X, XI, XII) jika ada
        if (! empty($class)) {
            $items = array_values(array_filter($items, function ($item) use ($class) {
                $cls = strtoupper((string) ($item['class'] ?? ''));
                $targetClass = strtoupper((string) $class);
                return str_contains($cls, $targetClass);
            }));
        }

        // 3. Filter Kata Kunci (Query)
        if (trim($query) !== '') {
            $q = mb_strtolower(trim($query));
            $items = array_values(array_filter($items, function ($item) use ($q) {
                return str_contains(mb_strtolower($item['title'] ?? ''), $q) ||
                       str_contains(mb_strtolower($item['writer'] ?? ''), $q) ||
                       str_contains(mb_strtolower($item['subject'] ?? ''), $q) ||
                       str_contains(mb_strtolower($item['isbn'] ?? ''), $q);
            }));
        }

        $total = count($items);
        $sliced = array_slice($items, $offset, $limit);

        return [
            'items' => $sliced,
            'total' => $total,
            'error' => null,
        ];
    }

    /**
     * Ambil dan cache buku dari SIBI Cloudapp CDN dengan proteksi anti-502 & stale fallback
     */
    private function getCachedCloudappBooks(string $category): array
    {
        $cacheKey = "sibi_catalog_raw_{$category}";
        $staleKey = "sibi_catalog_stale_{$category}";

        // 1. Cek cache valid
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && ! empty($cached['items'])) {
            return $cached;
        }

        $endpoint = match ($category) {
            'k13' => self::CLOUDAPP_BASE . '/getTextBooks',
            'nonteks' => self::CLOUDAPP_BASE . '/getNonTextBooks',
            default => self::CLOUDAPP_BASE . '/getPenggerakTextBooks',
        };

        // 2. Fetch dengan auto-retry
        try {
            $res = Http::timeout(12)
                ->retry(3, 250, throw: false)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)',
                    'Accept' => 'application/json',
                ])
                ->get($endpoint, ['limit' => 500]);

            // Jika limit 500 overload (502/504), coba porsi lebih ringan
            if (! $res->successful()) {
                $res = Http::timeout(10)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)',
                        'Accept' => 'application/json',
                    ])
                    ->get($endpoint, ['limit' => 200]);
            }

            if (! $res->successful()) {
                // Gunakan stale cache jika server pusat sedang down/502
                $stale = Cache::get($staleKey);
                if (is_array($stale) && ! empty($stale['items'])) {
                    return $stale;
                }
                return ['error' => 'Server SIBI Kemendikdasmen sedang sibuk (HTTP ' . $res->status() . '). Tekan tombol Coba Lagi untuk memuat ulang.'];
            }

            $json = $res->json();
            $rawItems = $json['results'] ?? [];

            $curriculumName = match ($category) {
                'merdeka' => 'Kurikulum Merdeka',
                'k13' => 'Kurikulum 2013',
                'nonteks' => 'Buku Pengayaan',
                default => 'Umum',
            };

            $items = [];
            foreach ($rawItems as $item) {
                $items[] = [
                    'id' => (string) ($item['id'] ?? uniqid()),
                    'slug' => $item['slug'] ?? null,
                    'title' => $item['title'] ?? 'Tanpa Judul',
                    'writer' => $item['writer'] ?? 'Pusat Perbukuan Kemendikdasmen',
                    'publisher' => $item['publisher'] ?? 'Pusat Perbukuan, Kemendikdasmen',
                    'category' => $item['category'] ?? $curriculumName,
                    'curriculum' => $curriculumName,
                    'level' => $item['level'] ?? null,
                    'class' => ! empty($item['class']) ? 'Kelas ' . $item['class'] : null,
                    'subject' => $item['subject'] ?? null,
                    'isbn' => $item['isbn'] ?? null,
                    'cover' => $item['image'] ?? null,
                    'attachment' => $item['attachment'] ?? null,
                    'description' => $item['description'] ?? null,
                ];
            }

            $result = ['items' => $items];

            // Cache hanya jika sukses (hindari caching pesan error)
            Cache::put($cacheKey, $result, 7200);
            Cache::put($staleKey, $result, 86400 * 3);

            return $result;
        } catch (\Throwable $e) {
            $stale = Cache::get($staleKey);
            if (is_array($stale) && ! empty($stale['items'])) {
                return $stale;
            }
            return ['error' => 'Koneksi ke SIBI Kemendikdasmen terputus: ' . $e->getMessage()];
        }
    }

    /**
     * Ambil dari endpoint resmi BTP Kemendikdasmen
     */
    private function getFromKemdikdasmen(string $query = '', string $level = 'SMA', ?string $class = '', int $limit = 24, int $offset = 0): array
    {
        $page = (int) floor($offset / $limit) + 1;
        $url = self::KEMDIK_BASE . '/btp/book';
        $params = [
            'code' => 'GuruSejahtera2025',
            'per_page' => 100,
            'page' => $page,
        ];

        if (trim($query) !== '') {
            $params['search'] = trim($query);
        }

        try {
            $res = Http::timeout(15)->get($url, $params);
            if (! $res->successful()) {
                return ['items' => [], 'total' => 0, 'error' => 'HTTP ' . $res->status()];
            }

            $json = $res->json();
            $data = $json['data'] ?? [];

            $items = [];
            foreach ($data as $item) {
                $lvl = $item['school_level']['name'] ?? '';
                if ($level === 'SMA' && ! str_contains(strtoupper($lvl), 'SMA') && ! str_contains(strtoupper($lvl), 'SMK') && ! str_contains(strtoupper($lvl), 'MA')) {
                    continue;
                }

                $items[] = [
                    'id' => (string) ($item['id'] ?? uniqid()),
                    'slug' => null,
                    'title' => $item['title'] ?? 'Tanpa Judul',
                    'writer' => $item['author'] ?? 'Pusat Perbukuan Kemendikdasmen',
                    'publisher' => $item['publisher']['name'] ?? 'Pusat Perbukuan Kemendikdasmen',
                    'category' => $item['category']['name'] ?? 'Buku Teks Utama',
                    'curriculum' => 'Kurikulum Nasional',
                    'level' => $lvl ?: null,
                    'class' => ! empty($item['school_class']['name']) ? 'Kelas ' . $item['school_class']['name'] : null,
                    'subject' => $item['subjects']['name'] ?? null,
                    'isbn' => $item['isbn'] ?? null,
                    'cover' => $item['cover_image'] ?? null,
                    'attachment' => null,
                    'description' => $item['descriptions'] ?? null,
                ];
            }

            return ['items' => array_slice($items, 0, $limit), 'total' => count($items), 'error' => null];
        } catch (\Throwable $e) {
            return ['items' => [], 'total' => 0, 'error' => $e->getMessage()];
        }
    }

    /**
     * Ambil buku digital madrasah dari Sikurma Kementerian Agama RI
     */
    public function getKemenagBooks(string $query = '', string $level = 'SMA', ?string $class = '', int $limit = 24, int $offset = 0): array
    {
        $cacheKey = 'kemenag_madrasah_books_raw';
        $items = Cache::get($cacheKey);

        if (! is_array($items) || empty($items)) {
            $items = $this->fetchSikurmaBooks();
            if (! empty($items)) {
                Cache::put($cacheKey, $items, 86400); // Cache 24 jam
            }
        }

        if (empty($items)) {
            return [
                'items' => [],
                'total' => 0,
                'error' => 'Gagal mengambil data buku Madrasah Kemenag dari server Sikurma. Coba muat ulang.',
            ];
        }

        // 1. Filter Level
        if ($level !== '' && $level !== 'ALL') {
            $items = array_values(array_filter($items, function ($item) use ($level) {
                $lvl = strtoupper($item['level'] ?? '');
                $cls = strtoupper($item['class'] ?? '');

                if ($level === 'SMA') {
                    return str_contains($lvl, 'SMA') ||
                           str_contains($lvl, 'MA') ||
                           str_contains($cls, '10') ||
                           str_contains($cls, '11') ||
                           str_contains($cls, '12') ||
                           $lvl === 'UMUM';
                }

                if ($level === 'SMK') {
                    return str_contains($lvl, 'SMK') || str_contains($lvl, 'MAK');
                }

                return str_contains($lvl, strtoupper($level));
            }));
        }

        // 2. Filter Kelas
        if (! empty($class)) {
            $items = array_values(array_filter($items, function ($item) use ($class) {
                $cls = strtoupper((string) ($item['class'] ?? ''));
                $targetClass = strtoupper((string) $class);
                return str_contains($cls, $targetClass);
            }));
        }

        // 3. Filter Query
        if (trim($query) !== '') {
            $q = mb_strtolower(trim($query));
            $items = array_values(array_filter($items, function ($item) use ($q) {
                return str_contains(mb_strtolower($item['title'] ?? ''), $q) ||
                       str_contains(mb_strtolower($item['writer'] ?? ''), $q) ||
                       str_contains(mb_strtolower($item['subject'] ?? ''), $q);
            }));
        }

        $total = count($items);
        $sliced = array_slice($items, $offset, $limit);

        return [
            'items' => $sliced,
            'total' => $total,
            'error' => null,
        ];
    }

    /**
     * Scrape katalog buku resmi Sikurma Kemenag RI
     */
    private function fetchSikurmaBooks(): array
    {
        try {
            $res = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get(self::SIKURMA_BASE . '/data_buku');

            if (! $res->successful()) {
                return [];
            }

            $html = $res->body();
            $pattern = "/<div class=\"blog-wrapper.*?<img src=\"([^\"]+)\".*?href=\"[^\"]*detail\/([^\"]+)\".*?<div class=\"h5 text-center\">\s*(.*?)\s*<\/div>.*?<small class=\"block m-top-xs text-muted\"><i class=\"fa fa-user\"><\/i>\s*(.*?)\s*<\/small>.*?<p class=\"\">([^<]*)<\/p>\s*<p>([^<]*)<\/p>/s";

            if (! preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) {
                return [];
            }

            $items = [];
            foreach ($matches as $m) {
                $rawImg = trim($m[1]);
                $img = str_replace('/./', '/', $rawImg);
                if (str_ends_with(strtolower($img), '.pdf')) {
                    $img = null;
                }

                $bookId = trim($m[2]);
                $title = html_entity_decode(trim($m[3]), ENT_QUOTES, 'UTF-8');
                $writer = html_entity_decode(trim($m[4]), ENT_QUOTES, 'UTF-8');
                $classRaw = trim($m[5]);
                $majorRaw = trim($m[6]);

                $classNum = preg_replace('/[^0-9]/', '', $classRaw);
                $level = 'UMUM';
                if ($classNum !== '') {
                    $c = (int) $classNum;
                    if ($c >= 10) {
                        $level = 'MA / SMA';
                    } elseif ($c >= 7) {
                        $level = 'MTS / SMP';
                    } else {
                        $level = 'MI / SD';
                    }
                }

                $downloadUrl = self::SIKURMA_BASE . '/download/' . $bookId;

                $items[] = [
                    'id' => 'kemenag-' . substr(md5($bookId), 0, 10),
                    'slug' => str()->slug($title) . '-' . substr(md5($bookId), 0, 6),
                    'title' => $title,
                    'writer' => ! empty($writer) ? $writer : 'Kementerian Agama RI',
                    'publisher' => 'Direktorat KSKK Madrasah, Kemenag RI',
                    'category' => 'Buku Madrasah Kemenag',
                    'curriculum' => 'Madrasah Kemenag (KMA 183)',
                    'level' => $level,
                    'class' => $classRaw !== 'umum' ? $classRaw : 'Semua Tingkat',
                    'subject' => $majorRaw,
                    'isbn' => null,
                    'cover' => $img,
                    'attachment' => $downloadUrl,
                    'description' => "Buku teks resmi Pendidikan Agama Islam & Bahasa Arab Madrasah terbitan Kementerian Agama RI ({$majorRaw}, {$classRaw}).",
                    'source' => 'Sikurma Kemenag RI',
                ];
            }

            return $items;
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Impor buku SIBI / Kemenag ke database lokal E-Library
     */
    public function importToLocal(array $item): Book
    {
        $pubName = trim($item['publisher'] ?? 'Pusat Perbukuan Kemendikdasmen');
        $publisher = Publisher::firstOrCreate(['name' => $pubName]);

        $catName = trim($item['curriculum'] ?? $item['category'] ?? 'Buku Kemendikdasmen');
        $category = Category::firstOrCreate(
            ['name' => $catName],
            ['slug' => str()->slug($catName) . '-' . uniqid()]
        );

        $coverPath = null;
        if (! empty($item['cover'])) {
            try {
                $imgRes = Http::timeout(10)->get($item['cover']);
                if ($imgRes->successful()) {
                    $coverFilename = 'covers/ext_' . uniqid() . '.jpg';
                    Storage::disk('public')->put($coverFilename, $imgRes->body());
                    $coverPath = $coverFilename;
                }
            } catch (\Throwable) {
                // Ignore download failure
            }
        }

        $baseSlug = str()->slug($item['title'] ?? 'buku');
        $slug = $baseSlug . '-' . uniqid();

        $book = Book::create([
            'title' => $item['title'] ?? 'Tanpa Judul',
            'slug' => $slug,
            'isbn' => $item['isbn'] ?? null,
            'publish_year' => (int) date('Y'),
            'page_count' => null,
            'description' => $item['description'] ?? 'Buku resmi Kementerian untuk jenjang madrasah/sekolah.',
            'publisher_id' => $publisher->id,
            'category_id' => $category->id,
            'type' => ! empty($item['attachment']) ? 'ebook' : 'fisik',
            'cover' => $coverPath,
            'ebook_file' => $item['attachment'] ?? null,
            'is_repository' => false,
            'borrow_count' => 0,
        ]);

        if (! empty($item['writer'])) {
            $writers = preg_split('/[,;&]|\bdan\b/i', $item['writer']);
            $authorIds = [];
            foreach ($writers as $w) {
                $wClean = trim($w);
                if ($wClean !== '' && strlen($wClean) > 2) {
                    $authorSlug = str()->slug($wClean) . '-' . uniqid();
                    $author = Author::firstOrCreate(
                        ['name' => $wClean],
                        ['slug' => $authorSlug]
                    );
                    $authorIds[] = $author->id;
                }
            }
            if (! empty($authorIds)) {
                $book->authors()->sync($authorIds);
            }
        }

        $prefix = str_contains(strtolower($catName), 'kemenag') ? 'KMN' : 'SIBI';
        BookCopy::create([
            'book_id' => $book->id,
            'inventory_code' => sprintf('%s-%04d-01', $prefix, $book->id),
            'qr_code' => (string) str()->uuid(),
            'condition' => 'baik',
            'status' => 'tersedia',
        ]);

        return $book;
    }
}
