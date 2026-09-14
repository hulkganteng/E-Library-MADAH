<?php

namespace App\Imports;

use App\Models\{Book, Category, Publisher, Shelf, Author, BookCopy};
use Maatwebsite\Excel\Concerns\{ToModel, WithHeadingRow, SkipsOnError, SkipsOnFailure, WithValidation};
use Maatwebsite\Excel\Validators\Failure;

class BooksImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    public array $failed = [];

    public int $importedCount = 0;

    public int $skippedCount = 0;

    protected array $processedIsbns = [];

    protected array $processedTitles = [];

    public function model(array $row)
    {
        $title = trim((string) ($row['judul'] ?? ''));
        if ($title === '') {
            return null;
        }

        $isbn = !empty($row['isbn']) ? trim((string) $row['isbn']) : null;

        // Cek duplikasi ISBN jika ada
        if ($isbn) {
            $isbnExists = Book::where('isbn', $isbn)->exists();
            if ($isbnExists || in_array($isbn, $this->processedIsbns, true)) {
                $this->skippedCount++;
                $this->failed[] = "Buku '{$title}' dilewati: ISBN '{$isbn}' sudah terdaftar.";
                return null;
            }
            $this->processedIsbns[] = $isbn;
        }

        // Cek duplikasi Judul (case-insensitive)
        $titleLower = strtolower($title);
        $titleExists = Book::whereRaw('LOWER(title) = ?', [$titleLower])->exists();
        if ($titleExists || in_array($titleLower, $this->processedTitles, true)) {
            $this->skippedCount++;
            $this->failed[] = "Buku '{$title}' dilewati: Judul buku sudah terdaftar.";
            return null;
        }
        $this->processedTitles[] = $titleLower;

        $category = !empty($row['kategori']) ? Category::firstOrCreate(['name' => trim($row['kategori'])], ['slug' => str()->slug($row['kategori'])]) : null;
        $publisher = !empty($row['penerbit']) ? Publisher::firstOrCreate(['name' => trim($row['penerbit'])]) : null;

        $book = Book::create([
            'title' => $title,
            'slug' => str()->slug($title) . '-' . uniqid(),
            'category_id' => $category?->id,
            'publisher_id' => $publisher?->id,
            'isbn' => $isbn,
            'publish_year' => !empty($row['tahun']) ? (int) $row['tahun'] : null,
            'type' => 'fisik',
        ]);

        if (!empty($row['penulis'])) {
            foreach (explode(';', $row['penulis']) as $authorName) {
                $name = trim($authorName);
                if ($name !== '') {
                    $author = Author::firstOrCreate(['name' => $name], ['slug' => str()->slug($name)]);
                    $book->authors()->attach($author->id);
                }
            }
        }

        $copies = max(1, (int) ($row['eksemplar'] ?? 1));
        for ($c = 1; $c <= $copies; $c++) {
            BookCopy::create([
                'book_id' => $book->id,
                'inventory_code' => sprintf('ELIB-%04d-%02d', $book->id, $c),
                'qr_code' => (string) str()->uuid(),
                'condition' => 'baik',
                'status' => 'tersedia',
            ]);
        }

        $this->importedCount++;

        return $book;
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string'],
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failed[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
        }
    }

    public function onError(\Throwable $e)
    {
        $this->failed[] = $e->getMessage();
    }
}
