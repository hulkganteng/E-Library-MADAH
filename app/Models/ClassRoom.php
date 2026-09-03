<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = ['name', 'grade', 'major'];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}
