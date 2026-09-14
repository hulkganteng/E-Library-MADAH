<?php

namespace App\Livewire\Loans;

use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $tab = 'list';

    public $search = '';

    // Checkout
    public $memberQuery = '';

    public $copyCode = '';

    public $dueDays;

    public $selectedMember = null;

    public $selectedCopy = null;

    public $borrowerName = '';

    public $borrowerClass = '';

    // Return
    public $returnCode = '';

    public $returnInfo = null;

    public $fine = 0;

    // Extend
    public $loanId = null;

    public $newDueAt;

    private function getStaticUser()
    {
        return User::firstOrCreate(
            ['email' => 'peminjam@assaadah.sch.id'],
            [
                'name' => 'Peminjam',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );
    }

    public function mount()
    {
        $this->dueDays = (int) Setting::get('loan_duration_days', 7);
    }

    public function setTab($tab)
    {
        abort_unless(in_array($tab, ['list', 'checkout', 'return'], true), 404);

        if ($tab === 'checkout' && auth()->check()) {
            Gate::authorize('peminjaman.create');
        }

        if ($tab === 'return' && auth()->check()) {
            Gate::authorize('peminjaman.edit');
        }

        $this->tab = $tab;
        $this->resetPage();
    }

    public function updatedMemberQuery()
    {
        $this->selectedMember = null;
    }

    public function pickMember($id)
    {
        $this->selectedMember = $id;
    }

    public function updatedCopyCode()
    {
        $this->selectedCopy = null;
    }

    public function scanCopy()
    {
        if (auth()->check()) {
            Gate::authorize('peminjaman.create');
        }
        $this->validate(['copyCode' => 'required']);
        $code = trim($this->copyCode);
        if (filter_var($code, FILTER_VALIDATE_URL)) {
            $code = basename(parse_url($code, PHP_URL_PATH));
        }
        $copy = BookCopy::with('book')->where('inventory_code', $code)->orWhere('qr_code', $code)->first();
        if (! $copy) {
            $this->dispatch('notify', ['message' => 'Kode eksemplar tidak ditemukan.', 'type' => 'error']);

            return;
        }
        if ($copy->status !== 'tersedia') {
            $this->dispatch('notify', ['message' => 'Eksemplar sedang tidak tersedia.', 'type' => 'error']);

            return;
        }
        $this->selectedCopy = $copy;
    }

    public function checkout()
    {
        if (auth()->check()) {
            Gate::authorize('peminjaman.create');
        }

        if (! $this->selectedCopy) {
            $this->dispatch('notify', ['message' => 'Scan atau cari eksemplar buku terlebih dahulu.', 'type' => 'error']);

            return;
        }

        $member = $this->getStaticUser();
        $copy = $this->selectedCopy;

        $max = (int) Setting::get('max_borrow', 3);
        $active = $member->loans()->where('status', '!=', 'dikembalikan')->count();
        if ($active >= $max) {
            $this->dispatch('notify', ['message' => "Batas peminjaman ($max buku) tercapai.", 'type' => 'error']);

            return;
        }

        $this->validate([
            'borrowerName' => 'required|string|max:100',
            'borrowerClass' => 'required|string|max:50',
        ]);

        Loan::create([
            'user_id' => $member->id,
            'book_copy_id' => $copy->id,
            'borrower_name' => trim($this->borrowerName),
            'borrower_class' => trim($this->borrowerClass),
            'borrowed_at' => now(),
            'due_at' => now()->addDays($this->dueDays),
            'status' => 'dipinjam',
            'handled_by' => auth()->id() ?? $member->id,
        ]);
        $copy->update(['status' => 'dipinjam']);
        $copy->book()->increment('borrow_count');

        $this->reset('copyCode', 'selectedCopy', 'borrowerName', 'borrowerClass');
        $this->dispatch('notify', ['message' => 'Peminjaman berhasil dibuat.']);
    }

    public function updatedReturnCode()
    {
        $this->returnInfo = null;
        $this->fine = 0;
    }

    public function scanReturn()
    {
        if (auth()->check()) {
            Gate::authorize('peminjaman.edit');
        }
        $this->validate(['returnCode' => 'required']);
        $code = trim($this->returnCode);
        if (filter_var($code, FILTER_VALIDATE_URL)) {
            $code = basename(parse_url($code, PHP_URL_PATH));
        }
        $copy = BookCopy::with('book')->where('inventory_code', $code)->orWhere('qr_code', $code)->first();
        if (! $copy) {
            $this->dispatch('notify', ['message' => 'Kode eksemplar tidak ditemukan.', 'type' => 'error']);

            return;
        }
        $loan = $copy->activeLoan;
        if (! $loan) {
            $this->dispatch('notify', ['message' => 'Eksemplar ini tidak sedang dipinjam.', 'type' => 'error']);

            return;
        }
        $this->returnInfo = $loan->load('user');
        $perDay = (float) Setting::get('fine_per_day', 0);
        $this->fine = $loan->daysOverdue() * $perDay;
    }

    public function processReturn()
    {
        if (auth()->check()) {
            Gate::authorize('peminjaman.edit');
        }
        $loan = $this->returnInfo;
        $loan->update([
            'returned_at' => now(),
            'status' => $this->fine > 0 ? 'terlambat' : 'dikembalikan',
            'fine_amount' => $this->fine,
        ]);
        $loan->bookCopy->update(['status' => 'tersedia']);
        $this->reset('returnCode', 'returnInfo', 'fine');
        $this->dispatch('notify', ['message' => 'Buku berhasil dikembalikan.'.($this->fine > 0 ? ' Denda: Rp'.number_format($this->fine, 0, ',', '.') : '')]);
    }

    public function extendLoan(Loan $loan)
    {
        if (auth()->check()) {
            Gate::authorize('peminjaman.edit');
        }
        if ($loan->status === 'dikembalikan' || $loan->isOverdue()) {
            $this->dispatch('notify', ['message' => 'Buku tidak dapat diperpanjang.', 'type' => 'error']);

            return;
        }
        $loan->update(['due_at' => $loan->due_at->addDays((int) Setting::get('loan_duration_days', 7))]);
        $this->dispatch('notify', ['message' => 'Masa pinjam diperpanjang 7 hari.']);
    }

    public function approveLoan(Loan $loan)
    {
        if (auth()->check()) {
            Gate::authorize('peminjaman.edit');
        }
        if ($loan->status !== 'menunggu') {
            $this->dispatch('notify', ['message' => 'Status pengajuan sudah tidak berlaku.', 'type' => 'error']);

            return;
        }

        $copy = $loan->bookCopy;
        if ($copy->status !== 'tersedia') {
            $this->dispatch('notify', ['message' => 'Eksemplar buku sedang tidak tersedia.', 'type' => 'error']);

            return;
        }

        $loan->update([
            'status' => 'dipinjam',
            'borrowed_at' => now(),
            'due_at' => now()->addDays((int) Setting::get('loan_duration_days', 7)),
            'handled_by' => auth()->id(),
        ]);
        $copy->update(['status' => 'dipinjam']);
        $copy->book()->increment('borrow_count');

        $this->dispatch('notify', ['message' => 'Pengajuan peminjaman berhasil disetujui.']);
    }

    public function rejectLoan(Loan $loan)
    {
        if (auth()->check()) {
            Gate::authorize('peminjaman.edit');
        }
        if ($loan->status !== 'menunggu') {
            $this->dispatch('notify', ['message' => 'Status pengajuan sudah tidak berlaku.', 'type' => 'error']);

            return;
        }

        $loan->update([
            'status' => 'ditolak',
            'handled_by' => auth()->id(),
        ]);

        $this->dispatch('notify', ['message' => 'Pengajuan peminjaman ditolak.']);
    }

    public function render()
    {
        $pendingCount = Loan::where('status', 'menunggu')->count();

        $loans = Loan::with('user', 'bookCopy.book')
            ->when($this->search, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%"))->orWhereHas('bookCopy', fn ($b) => $b->whereHas('book', fn ($bk) => $bk->where('title', 'like', "%{$this->search}%"))))
            ->latest()
            ->paginate(10);

        return view('livewire.loans.index', [
            'loans' => $loans,
            'pendingCount' => $pendingCount,
        ]);
    }
}
