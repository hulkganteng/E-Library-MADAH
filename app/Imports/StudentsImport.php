<?php

namespace App\Imports;

use App\Models\{User, Student, ClassRoom};
use Maatwebsite\Excel\Concerns\{ToModel, WithHeadingRow, SkipsOnError, WithValidation};
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    public function __construct(public array $failed = [])
    {
    }

    public function model(array $row)
    {
        $user = User::create([
            'name' => $row['nama'],
            'email' => $row['email'],
            'password' => $row['password'] ?? 'password',
        ]);
        $user->assignRole('Siswa');

        $class = $row['kelas'] ?? null ? ClassRoom::firstOrCreate(['name' => $row['kelas']], ['grade' => $row['kelas']]) : null;

        return new Student([
            'user_id' => $user->id,
            'class_id' => $class?->id,
            'nis' => $row['nis'] ?? null,
            'nisn' => $row['nisn'] ?? null,
            'gender' => $row['jenis_kelamin'] ?? 'L',
            'birth_date' => $row['tanggal_lahir'] ?? null,
            'address' => $row['alamat'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
        ];
    }

    public function onError(\Throwable $e)
    {
        $this->failed[] = $e->getMessage();
    }
}
