<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = [
            'buku' => ['view', 'create', 'edit', 'delete', 'import', 'export'],
            'kategori' => ['view', 'create', 'edit', 'delete'],
            'penulis' => ['view', 'create', 'edit', 'delete'],
            'penerbit' => ['view', 'create', 'edit', 'delete'],
            'rak' => ['view', 'create', 'edit', 'delete'],
            'eksemplar' => ['view', 'create', 'edit', 'delete'],
            'peminjaman' => ['view', 'create', 'edit', 'delete'],
            'siswa' => ['view', 'create', 'edit', 'delete', 'import'],
            'guru' => ['view', 'create', 'edit', 'delete'],
            'kelas' => ['view', 'create', 'edit', 'delete'],
            'pustakawan' => ['view', 'create', 'edit', 'delete'],
            'katalog' => ['view'],
            'pengumuman' => ['view', 'create', 'edit', 'delete'],
            'laporan' => ['view', 'export'],
            'pengaturan' => ['view', 'edit'],
            'audit' => ['view'],
            'backup' => ['create', 'restore'],
        ];

        $permissions = [];
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissions[] = Permission::firstOrCreate(['name' => "{$module}.{$action}"]);
            }
        }

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions($permissions);

        $pustakawan = Role::firstOrCreate(['name' => 'Pustakawan']);
        $pustakawan->syncPermissions([
            'buku.view', 'buku.create', 'buku.edit', 'buku.import',
            'kategori.view', 'penulis.view', 'penerbit.view', 'rak.view',
            'eksemplar.view', 'eksemplar.create', 'eksemplar.edit',
            'peminjaman.view', 'peminjaman.create', 'peminjaman.edit',
            'siswa.view', 'siswa.import', 'katalog.view',
            'pengumuman.view', 'laporan.view',
        ]);

        $guru = Role::firstOrCreate(['name' => 'Guru']);
        $guru->syncPermissions(['katalog.view', 'peminjaman.view', 'buku.view']);

        $siswa = Role::firstOrCreate(['name' => 'Siswa']);
        $siswa->syncPermissions(['katalog.view']);
    }
}
