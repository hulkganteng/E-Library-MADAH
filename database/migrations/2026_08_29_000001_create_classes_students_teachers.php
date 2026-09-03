<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('grade', 20)->nullable();
            $table->string('major', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('nis', 30)->nullable()->unique();
            $table->string('nisn', 20)->nullable()->unique();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nip', 30)->nullable()->unique();
            $table->string('subject', 100)->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('students');
        Schema::dropIfExists('classes');
    }
};
