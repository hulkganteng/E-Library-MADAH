<?php

namespace App\Livewire\Reports;

use App\Exports\LoansExport;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    public string $from = '';

    public string $to = '';

    public string $status = '';

    public function mount()
    {
        $this->from = now()->startOfMonth()->format('Y-m-d');
        $this->to = now()->format('Y-m-d');
    }

    public function exportPdf()
    {
        Gate::authorize('laporan.export');
        $data = $this->loansData();
        $pdf = Pdf::loadView('reports.loans', [
            'loans' => $data,
            'from' => $this->from,
            'to' => $this->to,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(fn () => print ($pdf->output()), 'laporan-peminjaman.pdf');
    }

    public function exportExcel()
    {
        Gate::authorize('laporan.export');

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
            'students' => Student::count(),
            'teachers' => Teacher::count(),
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
