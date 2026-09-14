<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guest_visits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('visitor_type')->default('umum'); // umum, siswa, guru, alumni, wali_murid, instansi_luar
            $table->string('identifier')->nullable(); // NISN / NIP / NIK / No. Anggota
            $table->string('institution')->nullable(); // Sekolah / Kelas / Lembaga asal
            $table->string('phone')->nullable();
            $table->string('purpose'); // Membaca, Meminjam/Mengembalikan, Diskusi, Kunjungan Kerja/Dinas, dll
            $table->text('notes')->nullable();
            $table->dateTime('visited_at');
            $table->timestamps();

            $table->index('visited_at');
            $table->index('visitor_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_visits');
    }
};
