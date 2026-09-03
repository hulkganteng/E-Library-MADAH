<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    use HasFactory;

    protected $fillable = ['book_id', 'inventory_code', 'qr_code', 'condition', 'status'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function activeLoan()
    {
        return $this->hasOne(Loan::class)->where('status', '!=', 'dikembalikan')->latest();
    }
}
