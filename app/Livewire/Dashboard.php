<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Book, BookCopy, Loan, User, Student, Announcement};

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();

        if ($user->isStudent() || $user->isTeacher()) {
            $data = [
                'activeLoans' => Loan::where('user_id', $user->id)->where('status', '!=', 'dikembalikan')->with('bookCopy.book')->latest()->get(),
                'history' => Loan::where('user_id', $user->id)->where('status', 'dikembalikan')->with('bookCopy.book')->latest()->limit(5)->get(),
                'favorites' => $user->favorites()->with('book')->latest()->get(),
                'announcements' => Announcement::published()->whereIn('audience', ['semua', $user->roles->first()->name])->latest()->limit(4)->get(),
                'popular' => Book::withCount('favorites')->orderBy('borrow_count', 'desc')->limit(6)->get(),
                'totalBorrowed' => $user->loans()->count(),
                'totalFavorite' => $user->favorites()->count(),
            ];
            return view('livewire.dashboard.member', $data);
        }

        $overdue = Loan::where('status', '!=', 'dikembalikan')->where('due_at', '<', now())->count();
        $dueSoon = Loan::where('status', '!=', 'dikembalikan')->whereBetween('due_at', [now(), now()->addDays(2)])->count();

        $data = [
            'totalBooks' => Book::count(),
            'totalCopies' => BookCopy::count(),
            'availableCopies' => BookCopy::where('status', 'tersedia')->count(),
            'totalMembers' => User::role(['Siswa', 'Guru'])->count(),
            'activeLoans' => Loan::where('status', '!=', 'dikembalikan')->count(),
            'overdue' => $overdue,
            'dueSoon' => $dueSoon,
            'recentLoans' => Loan::with('user', 'bookCopy.book')->latest()->limit(8)->get(),
            'popular' => Book::orderBy('borrow_count', 'desc')->limit(6)->get(),
            'monthlyLoans' => Loan::whereMonth('borrowed_at', now()->month)->whereYear('borrowed_at', now()->year)->count(),
            'monthlyReturns' => Loan::whereMonth('returned_at', now()->month)->whereYear('returned_at', now()->year)->count(),
            'announcements' => Announcement::published()->latest()->limit(4)->get(),
        ];
        return view('livewire.dashboard.staff', $data);
    }
}
