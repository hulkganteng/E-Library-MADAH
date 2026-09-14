<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'visitor_type',
        'identifier',
        'institution',
        'phone',
        'purpose',
        'notes',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public static array $types = [
        'umum' => 'Tamu Umum / Luar',
        'siswa' => 'Siswa',
        'guru' => 'Guru / Pendidik',
        'alumni' => 'Alumni',
        'wali_murid' => 'Wali Murid',
        'instansi_luar' => 'Instansi / Dinas Luar',
        'staf' => 'Tenaga Kependidikan / Staf',
    ];

    public static array $purposes = [
        'membaca' => 'Membaca / Literasi',
        'meminjam' => 'Peminjaman / Pengembalian Buku',
        'tugas' => 'Mengerjakan Tugas / Belajar Mandiri',
        'diskusi' => 'Diskusi Kelompok / Riset',
        'kunjungan_dinas' => 'Kunjungan Dinas / Studi Banding',
        'administrasi' => 'Keperluan Administrasi / Lainnya',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::$types[$this->visitor_type] ?? ucfirst($this->visitor_type);
    }

    public function getPurposeLabelAttribute(): string
    {
        return self::$purposes[$this->purpose] ?? $this->purpose;
    }
}
