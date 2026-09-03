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

    // Return
    public $returnCode = '';

    public $returnInfo = null;

    public $fine = 0;

    // Extend
    public $loanId = null;

    public $newDueAt;

    public function mount()
    {
        $this->dueDays = (int) Setting::get('loan_duration_days', 7);
    }

    public function setTab($tab)
    {
        abort_unless(in_array($tab, ['list', 'checkout', 'return'], true), 404);

        if ($tab === 'checkout') {
            Gate::authorize('peminjaman.create');
        }

        if ($tab === 'return') {
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
        Gate::authorize('peminjaman.create');
        $this->validate(['copyCode' => 'required']);
        $copy = BookCopy::with('book')->where('inventory_code', $this->copyCode)->orWhere('qr_code', $this->copyCode)->first();
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
        Gate::authorize('peminjaman.create');
        if (! $this->selectedMember || ! $this->selectedCopy) {
            $this->dispatch('notify', ['message' => 'Pilih anggota dan eksemplar.', 'type' => 'error']);

            return;
        }
        $member = User::find($this->selectedMember);
        $copy = $this->selectedCopy;

        $max = (int) Setting::get('max_borrow', 3);
        $active = $member->loans()->where('status', '!=', 'dikembalikan')->count();
        if ($active >= $max) {
            $this->dispatch('notify', ['message' => "Anggota sudah meminjam $max buku.", 'type' => 'error']);

            return;
        }

        Loan::create([
            'user_id' => $member->id,
            'book_copy_id' => $copy->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays($this->dueDays),
            'status' => 'dipinjam',
            'handled_by' => auth()->id(),
        ]);
        $copy->update(['status' => 'dipinjam']);
        $copy->book()->increment('borrow_count');

        $this->reset('memberQuery', 'copyCode', 'selectedMember', 'selectedCopy');
        $this->dispatch('notify', ['message' => 'Peminjaman berhasil dibuat.']);
    }

    public function updatedReturnCode()
    {
        $this->returnInfo = null;
        $this->fine = 0;
    }

    public function scanReturn()
    {
        Gate::authorize('peminjaman.edit');
        $this->validate(['returnCode' => 'required']);
        $copy = BookCopy::with('book')->where('inventory_code', $this->returnCode)->orWhere('qr_code', $this->returnCode)->first();
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
        Gate::authorize('peminjaman.edit');
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
        Gate::authorize('peminjaman.edit');
        if ($loan->status === 'dikembalikan' || $loan->isOverdue()) {
            $this->dispatch('notify', ['message' => 'Buku tidak dapat diperpanjang.', 'type' => 'error']);

            return;
        }
        $loan->update(['due_at' => $loan->due_at->addDays((int) Setting::get('loan_duration_days', 7))]);
        $this->dispatch('notify', ['message' => 'Masa pinjam diperpanjang 7 hari.']);
    }

    public function render()
    {
        $members = [];
        if ($this->memberQuery) {
            $members = User::role(['Siswa', 'Guru'])
                ->where('name', 'like', "%{$this->memberQuery}%")
                ->orWhere('email', 'like', "%{$this->memberQuery}%")
                ->limit(8)->get();
        }

        $loans = Loan::with('user', 'bookCopy.book')
            ->when($this->search, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%"))->orWhereHas('bookCopy', fn ($b) => $b->whereHas('book', fn ($bk) => $bk->where('title', 'like', "%{$this->search}%"))))
            ->latest()
            ->paginate(10);

        return view('livewire.loans.index', [
            'members' => $members,
            'loans' => $loans,
        ]);
    }
}
