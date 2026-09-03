@props(['title' => 'Manajemen Guru'])
<div class="space-y-6">
    <x-page-header
        title="Data Anggota Guru & Staf"
        subtitle="Kelola profil guru, nomor NIP, mata pelajaran yang diampu, dan akun login perpustakaan."
    >
        <x-slot:actions>
            @can('guru.create')
            <button
                type="button"
                wire:click="create"
                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Tambah Guru</span>
            </button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    {{-- Search --}}
    <div class="max-w-md">
        <x-input
            wire:model.live.debounce.300ms="search"
            placeholder="Cari nama guru, NIP, atau mata pelajaran..."
        />
    </div>

    {{-- Data Table --}}
    <x-card>
        <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5 sm:px-6">Nama & Email</th>
                        <th class="hidden px-4 py-3.5 sm:table-cell">NIP</th>
                        <th class="hidden px-4 py-3.5 md:table-cell">Mata Pelajaran</th>
                        <th class="px-5 py-3.5 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($teachers as $t)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5 sm:px-6">
                                <p class="font-bold text-slate-900 text-xs sm:text-sm">{{ $t->user->name }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $t->user->email }}</p>
                            </td>
                            <td class="hidden px-4 py-3.5 sm:table-cell text-xs font-mono text-slate-600">
                                {{ $t->nip ?: '-' }}
                            </td>
                            <td class="hidden px-4 py-3.5 md:table-cell text-xs font-semibold text-emerald-800">
                                {{ $t->subject ?: '-' }}
                            </td>
                            <td class="px-5 py-3.5 sm:px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @can('guru.edit')
                                    <button
                                        type="button"
                                        wire:click="edit({{ $t->id }})"
                                        class="rounded-md px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                                    >
                                        Edit
                                    </button>
                                    @endcan
                                    @can('guru.delete')
                                    <button
                                        type="button"
                                        wire:click="delete({{ $t->id }})"
                                        wire:confirm="Yakin ingin menghapus data guru ini?"
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
                                Belum ada data guru yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($teachers->hasPages())
            <div class="mt-4 pt-4 border-t border-slate-100">
                {{ $teachers->links() }}
            </div>
        @endif
    </x-card>

    {{-- Modal Add / Edit Teacher --}}
    <x-modal :show="$showModal" :title="$editing ? 'Edit Data Guru' : 'Tambah Guru Baru'" maxWidth="max-w-2xl">
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input wire:model="name" label="Nama Lengkap & Gelar" required placeholder="Contoh: Drs. H. Ahmad Fauzi, M.Pd" :error="$errors->first('name')" />
            </div>

            <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                <x-input wire:model="email" label="Alamat Email" type="email" required placeholder="guru@assaadah.sch.id" :error="$errors->first('email')" />
                <x-input wire:model="password" label="Kata Sandi Akun" type="password" placeholder="••••••••" :error="$errors->first('password')" :hint="$editing ? 'Kosongkan jika tidak ingin diubah' : ''" />
            </div>

            <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                <x-input wire:model="nip" label="NIP / NUPTK" placeholder="Nomor Induk Pegawai" :error="$errors->first('nip')" />
                <x-input wire:model="subject" label="Mata Pelajaran yang Diampu" placeholder="Contoh: Bahasa Arab" />
            </div>

            <div>
                <x-input wire:model="phone" label="Nomor Telepon / WhatsApp" placeholder="08..." />
            </div>

            <div>
                <x-textarea wire:model="address" label="Alamat Tempat Tinggal" rows="2" placeholder="Alamat lengkap..." />
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
                    Simpan Data Guru
                </button>
            </div>
        </form>
    </x-modal>
</div>
