<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BooksTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['judul', 'kategori', 'penerbit', 'penulis', 'isbn', 'tahun', 'eksemplar'];
    }

    public function array(): array
    {
        return [
            [
                'judul' => 'Laskar Pelangi',
                'kategori' => 'Fiksi',
                'penerbit' => 'Bentang Pustaka',
                'penulis' => 'Andrea Hirata',
                'isbn' => '978-602-291-663-5',
                'tahun' => '2005',
                'eksemplar' => '3',
            ],
            [
                'judul' => 'Fisika Dasar Jilid 1',
                'kategori' => 'Sains',
                'penerbit' => 'Erlangga',
                'penulis' => 'Halliday; Resnick',
                'isbn' => '978-979-015-001-1',
                'tahun' => '2010',
                'eksemplar' => '2',
            ],
        ];
    }
}
