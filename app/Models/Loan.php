<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'book_copy_id', 'borrower_name', 'borrower_class', 'borrowed_at', 'due_at', 'returned_at',
        'status', 'fine_amount', 'notes', 'handled_by',
    ];

    protected $casts = [
        'borrowed_at' => 'date',
        'due_at' => 'date',
        'returned_at' => 'date',
        'fine_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'dikembalikan' && $this->due_at->isPast();
    }

    public function daysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        $end = $this->returned_at ?? now();
        return max(0, $this->due_at->diffInDays($end));
    }
}
