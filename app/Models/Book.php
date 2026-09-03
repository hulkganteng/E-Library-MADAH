<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'publisher_id', 'shelf_id', 'title', 'slug', 'isbn',
        'edition', 'publish_year', 'page_count', 'type', 'description',
        'cover', 'ebook_file', 'is_repository', 'borrow_count',
    ];

    protected $casts = ['is_repository' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function shelf()
    {
        return $this->belongsTo(Shelf::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_author');
    }

    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }

    public function availableCopies()
    {
        return $this->copies()->where('status', 'tersedia');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}
