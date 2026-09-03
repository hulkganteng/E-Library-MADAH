<?php

namespace App\Exports;

use App\Models\Loan;
use Maatwebsite\Excel\Concerns\{FromQuery, WithHeadings, WithMapping};

class LoansExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private string $from = '',
        private string $to = '',
        private string $status = '',
    ) {
    }

    public function query()
    {
        return Loan::query()
            ->with('user', 'bookCopy.book')
            ->when($this->from, fn ($q) => $q->whereDate('borrowed_at', '>=', $this->from))
            ->when($this->to, fn ($q) => $q->whereDate('borrowed_at', '<=', $this->to))
            ->when($this->status, fn ($q) => $q->where('status', $this->status));
    }

    public function headings(): array
    {
        return ['No', 'Anggota', 'Judul Buku', 'Kode Inventaris', 'Tanggal Pinjam', 'Jatuh Tempo', 'Tanggal Kembali', 'Status', 'Denda'];
    }

    public function map($loan): array
    {
        return [
            $loan->id,
            $loan->user->name,
            $loan->bookCopy->book->title,
            $loan->bookCopy->inventory_code,
            $loan->borrowed_at?->format('d-m-Y'),
            $loan->due_at?->format('d-m-Y'),
            $loan->returned_at?->format('d-m-Y'),
            $loan->status,
            $loan->fine_amount,
        ];
    }
}
