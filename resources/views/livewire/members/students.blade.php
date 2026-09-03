@props(['title' => 'Manajemen Siswa'])
<div class="space-y-6">
    <x-page-header
        title="Data Anggota Siswa"
        subtitle="Kelola profil siswa, data NIS/NISN, kelas, dan akun login perpustakaan."
    >
        <x-slot:actions>
            @can('siswa.import')
            <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                <svg class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                <span>Import Excel</span>
                <input type="file" wire:model="importFile" class="hidden">
            </label>
            @if($importFile)
                <button
                    type="button"
                    wire:click="import"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    <span>Proses Import</span>
                </button>
            @endif
            @endcan
            @can('siswa.create')
            <button
                type="button"
                wire:click="create"
                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Tambah Siswa</span>
            </button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    {{-- Search --}}
    <div class="max-w-md">
        <x-input
            wire:model.live.debounce.300ms="search"
            placeholder="Cari nama lengkap, NIS, atau email siswa..."
        />
    </div>

    {{-- Data Table --}}
    <x-card>
        <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5 sm:px-6">Nama & Email</th>
                        <th class="hidden px-4 py-3.5 sm:table-cell">NIS / NISN</th>
                        <th class="hidden px-4 py-3.5 md:table-cell">Kelas</th>
                        <th class="px-5 py-3.5 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($students as $s)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5 sm:px-6">
                                <p class="font-bold text-slate-900 text-xs sm:text-sm">{{ $s->user->name }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $s->user->email }}</p>
                            </td>
                            <td class="hidden px-4 py-3.5 sm:table-cell text-xs font-mono text-slate-600">
                                {{ $s->nis ?: '-' }} / {{ $s->nisn ?: '-' }}
                            </td>
                            <td class="hidden px-4 py-3.5 md:table-cell text-xs font-semibold text-emerald-800">
                                {{ $s->classRoom?->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 sm:px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @can('siswa.edit')
                                    <button
                                        type="button"
                                        wire:click="edit({{ $s->id }})"
                                        class="rounded-md px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                                    >
                                        Edit
                                    </button>
                                    @endcan
                                    @can('siswa.delete')
                                    <button
                                        type="button"
                                        wire:click="delete({{ $s->id }})"
                                        wire:confirm="Yakin ingin menghapus data siswa ini?"
                                        class="rounded-md px-2.5 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-xs text-slate-400">
                                Belum ada data siswa yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="mt-4 pt-4 border-t border-slate-100">
                {{ $students->links() }}
            </div>
        @endif
    </x-card>

    {{-- Modal Add / Edit Student --}}
    <x-modal :show="$showModal" :title="$editing ? 'Edit Profil Siswa' : 'Tambah Siswa Baru'" maxWidth="max-w-2xl">
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input wire:model="name" label="Nama Lengkap Siswa" required placeholder="Nama lengkap sesuai data madrasah" :error="$errors->first('name')" />
            </div>

            <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                <x-input wire:model="email" label="Alamat Email" type="email" required placeholder="siswa@assaadah.sch.id" :error="$errors->first('email')" />
                <x-input wire:model="password" label="Kata Sandi Akun" type="password" placeholder="••••••••" :error="$errors->first('password')" :hint="$editing ? 'Kosongkan jika kata sandi tidak ingin diubah' : ''" />
            </div>

            <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                <x-input wire:model="nis" label="NIS" placeholder="Nomor Induk Siswa" :error="$errors->first('nis')" />
                <x-input wire:model="nisn" label="NISN" placeholder="Nomor Induk Siswa Nasional" :error="$errors->first('nisn')" />
            </div>

            <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                <x-select wire:model="class_id" label="Kelas">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </x-select>
                <x-select wire:model="gender" label="Jenis Kelamin">
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </x-select>
            </div>

            <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                <x-input wire:model="birth_date" label="Tanggal Lahir" type="date" />
                <x-input wire:model="phone" label="Nomor Telepon / WhatsApp" placeholder="08..." />
            </div>

            <div>
                <x-textarea wire:model="address" label="Alamat Domisili" rows="2" placeholder="Alamat lengkap tempat tinggal..." />
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button
                    type="button"
                    wire:click="$set('showModal', false)"
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="rounded-lg bg-emerald-700 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
                >
                    Simpan Data Siswa
                </button>
            </div>
        </form>
    </x-modal>
</div>
