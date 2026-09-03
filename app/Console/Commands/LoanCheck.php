<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loan;
use App\Models\Notification;

class LoanCheck extends Command
{
    protected $signature = 'loan:check';

    protected $description = 'Tandai keterlambatan dan kirim notifikasi jatuh tempo.';

    public function handle(): int
    {
        $overdue = Loan::where('status', 'dipinjam')->where('due_at', '<', now())->get();
        foreach ($overdue as $loan) {
            if ($loan->status === 'dipinjam') {
                $loan->update(['status' => 'terlambat']);
            }
            Notification::create([
                'user_id' => $loan->user_id,
                'title' => 'Buku terlambat dikembalikan',
                'body' => $loan->bookCopy->book->title . ' terlambat ' . $loan->daysOverdue() . ' hari.',
                'type' => 'warning',
            ]);
        }

        $dueSoon = Loan::where('status', '!=', 'dikembalikan')->whereBetween('due_at', [now(), now()->addDays(2)])->get();
        foreach ($dueSoon as $loan) {
            Notification::updateOrCreate(
                ['user_id' => $loan->user_id, 'type' => 'warning', 'title' => 'Jatuh tempo besok / lusa'],
                ['body' => $loan->bookCopy->book->title . ' harus dikembalikan pada ' . $loan->due_at->format('d M Y')]
            );
        }

        $this->info('Pemeriksaan peminjaman selesai.');
        return self::SUCCESS;
    }
}
