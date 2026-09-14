@props(['title' => 'Manajemen Pengguna'])
<div class="space-y-6">
    <x-page-header title="Manajemen Pengguna" subtitle="Kelola akun login, peran, dan status aktif pengguna sistem.">
        <x-slot:actions>
            <button type="button" wire:click="create" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Tambah Pengguna</span>
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="flex flex-col sm:flex-row gap-3 max-w-2xl">
        <div class="flex-1">
            <x-input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email..." />
        </div>
        <div class="w-full sm:w-48">
            <select wire:model.live="filterRole" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                <option value="">Semua Peran</option>
                @foreach($roles as $r)
                    <option value="{{ $r }}">{{ $r }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <x-card>
        <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5 sm:px-6">Nama & Email</th>
                        <th class="px-4 py-3.5">Peran</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-5 py-3.5 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5 sm:px-6">
                                <p class="font-bold text-slate-900 text-xs sm:text-sm">{{ $u->name }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $u->email }}</p>
                            </td>
                            <td class="px-4 py-3.5">
                                <x-badge color="brand" size="sm">{{ $u->roles->first()?->name ?? '-' }}</x-badge>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($u->is_active)
                                    <x-badge color="green" size="sm" dot>Aktif</x-badge>
                                @else
                                    <x-badge color="red" size="sm">Nonaktif</x-badge>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 sm:px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" wire:click="toggleActive({{ $u->id }})" class="rounded-md px-2.5 py-1 text-xs font-semibold {{ $u->is_active ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} transition cursor-pointer">
                                        {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                    <button type="button" wire:click="edit({{ $u->id }})" class="rounded-md px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer">Edit</button>
                                    <button type="button" wire:click="delete({{ $u->id }})" wire:confirm="Yakin hapus pengguna ini?" class="rounded-md px-2.5 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-xs text-slate-400">Belum ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="mt-4 pt-4 border-t border-slate-100">{{ $users->links() }}</div>
        @endif
    </x-card>

    <x-modal :show="$showModal" :title="$editing ? 'Edit Pengguna' : 'Tambah Pengguna Baru'" maxWidth="max-w-lg">
        <form wire:submit="save" class="space-y-4">
            <x-input wire:model="name" label="Nama Lengkap" required placeholder="Nama pengguna" :error="$errors->first('name')" />
            <x-input wire:model="email" label="Email" type="email" required placeholder="email@assaadah.sch.id" :error="$errors->first('email')" />
            <x-input wire:model="password" label="Kata Sandi" type="password" :error="$errors->first('password')" :hint="$editing ? 'Kosongkan jika tidak diubah' : 'Minimal 6 karakter'" />
            <x-select wire:model="role" label="Peran" required :error="$errors->first('role')">
                @foreach($roles as $r)
                    <option value="{{ $r }}">{{ $r }}</option>
                @endforeach
            </x-select>
            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                <input type="checkbox" wire:model="is_active" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4" />
                Akun Aktif
            </label>
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" wire:click="$set('showModal', false)" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">Batal</button>
                <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer">Simpan</button>
            </div>
        </form>
    </x-modal>
</div>
