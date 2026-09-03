<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'class_id', 'nis', 'nisn', 'gender', 'birth_date', 'address'];

    protected $casts = ['birth_date' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}
