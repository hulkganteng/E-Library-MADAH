<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LoansExport;
use App\Models\{Loan, Book, User, BookCopy};

class Index extends Component
{
    public string $from = '', $to = '', $status = '';

    public function mount()
    {
        $this->from = now()->startOfMonth()->format('Y-m-d');
        $this->to = now()->format('Y-m-d');
    }

    public function exportPdf()
    {
        $data = $this->loansData();
        $pdf = Pdf::loadView('reports.loans', [
            'loans' => $data,
            'from' => $this->from,
            'to' => $this->to,
        ])->setPaper('a4', 'landscape');
        return response()->streamDownload(fn () => print($pdf->output()), 'laporan-peminjaman.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new LoansExport($this->from, $this->to, $this->status), 'laporan-peminjaman.xlsx');
    }

    private function loansData()
    {
        return Loan::with('user', 'bookCopy.book')
            ->when($this->from, fn ($q) => $q->whereDate('borrowed_at', '>=', $this->from))
            ->when($this->to, fn ($q) => $q->whereDate('borrowed_at', '<=', $this->to))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->latest()->get();
    }

    public function render()
    {
        $stats = [
            'totalBooks' => Book::count(),
            'totalCopies' => BookCopy::count(),
            'available' => BookCopy::where('status', 'tersedia')->count(),
            'members' => User::role(['Siswa', 'Guru'])->count(),
            'students' => \App\Models\Student::count(),
            'teachers' => \App\Models\Teacher::count(),
            'activeLoans' => Loan::where('status', '!=', 'dikembalikan')->count(),
            'totalLoans' => Loan::count(),
            'returned' => Loan::where('status', 'dikembalikan')->count(),
            'overdue' => Loan::where('status', '!=', 'dikembalikan')->where('due_at', '<', now())->count(),
            'fines' => Loan::sum('fine_amount'),
            'popular' => Book::orderBy('borrow_count', 'desc')->limit(5)->get(),
        ];

        return view('livewire.reports.index', ['stats' => $stats]);
    }
}
