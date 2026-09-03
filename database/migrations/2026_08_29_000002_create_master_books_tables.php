<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 110)->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 160)->unique();
            $table->string('biography')->nullable();
            $table->timestamps();
        });

        Schema::create('publishers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->timestamps();
        });

        Schema::create('shelves', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('location', 150)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shelves');
        Schema::dropIfExists('publishers');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('categories');
    }
};
