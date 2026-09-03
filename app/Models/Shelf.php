<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelf extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'location', 'description'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
