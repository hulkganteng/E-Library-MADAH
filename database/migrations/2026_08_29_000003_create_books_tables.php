<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('publisher_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shelf_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 255);
            $table->string('slug', 260)->unique();
            $table->string('isbn', 20)->nullable()->unique();
            $table->string('edition')->nullable();
            $table->year('publish_year')->nullable();
            $table->integer('page_count')->nullable();
            $table->enum('type', ['fisik', 'ebook'])->default('fisik');
            $table->text('description')->nullable();
            $table->string('cover')->nullable();
            $table->string('ebook_file')->nullable();
            $table->boolean('is_repository')->default(false);
            $table->unsignedInteger('borrow_count')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('book_author', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained()->cascadeOnDelete();
            $table->unique(['book_id', 'author_id']);
        });

        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('inventory_code', 50)->unique();
            $table->string('qr_code', 100)->unique();
            $table->enum('condition', ['baik', 'rusak', 'hilang'])->default('baik');
            $table->enum('status', ['tersedia', 'dipinjam', 'rusak', 'hilang', 'dipesan'])->default('tersedia')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_copies');
        Schema::dropIfExists('book_author');
        Schema::dropIfExists('books');
    }
};
