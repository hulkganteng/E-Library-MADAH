<?php

namespace App\Imports;

use App\Models\{Book, Category, Publisher, Shelf, Author, BookCopy};
use Maatwebsite\Excel\Concerns\{ToModel, WithHeadingRow, SkipsOnError, WithValidation};

class BooksImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    public array $failed = [];

    public function model(array $row)
    {
        $category = $row['kategori'] ?? null ? Category::firstOrCreate(['name' => $row['kategori']], ['slug' => str()->slug($row['kategori'])]) : null;
        $publisher = $row['penerbit'] ?? null ? Publisher::firstOrCreate(['name' => $row['penerbit']]) : null;

        $book = Book::create([
            'title' => $row['judul'],
            'slug' => str()->slug($row['judul']) . '-' . uniqid(),
            'category_id' => $category?->id,
            'publisher_id' => $publisher?->id,
            'isbn' => $row['isbn'] ?? null,
            'publish_year' => $row['tahun'] ?? null,
            'type' => 'fisik',
        ]);

        if (!empty($row['penulis'])) {
            foreach (explode(';', $row['penulis']) as $authorName) {
                $author = Author::firstOrCreate(['name' => trim($authorName)], ['slug' => str()->slug($authorName)]);
                $book->authors()->attach($author->id);
            }
        }

        $copies = (int) ($row['eksemplar'] ?? 1);
        for ($c = 1; $c <= $copies; $c++) {
            BookCopy::create([
                'book_id' => $book->id,
                'inventory_code' => sprintf('ELIB-%04d-%02d', $book->id, $c),
                'qr_code' => (string) str()->uuid(),
                'condition' => 'baik',
                'status' => 'tersedia',
            ]);
        }

        return $book;
    }

    public function rules(): array
    {
        return ['judul' => ['required']];
    }

    public function onError(\Throwable $e)
    {
        $this->failed[] = $e->getMessage();
    }
}
