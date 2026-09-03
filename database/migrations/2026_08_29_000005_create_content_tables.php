<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('content');
            $table->enum('audience', ['semua', 'siswa', 'guru', 'pustakawan', 'admin'])->default('semua');
            $table->boolean('is_published')->default(true);
            $table->date('published_at')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('type', 50)->default('info');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('announcements');
    }
};
