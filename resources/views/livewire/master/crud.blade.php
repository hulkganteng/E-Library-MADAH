@props(['title' => ''])
<div class="space-y-6">
    <x-page-header
        :title="'Data Master ' . $name"
        :subtitle="'Kelola referensi data ' . strtolower($name) . ' perpustakaan.'"
    >
        <x-slot:actions>
            <button
                type="button"
                wire:click="create"
                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Tambah {{ $name }}</span>
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-md">
        <x-input
            wire:model.live.debounce.300ms="search"
            placeholder="Cari data..."
        />
    </div>

    <x-card>
        <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5 sm:px-6">{{ $nameKey === 'code' ? 'Kode / Simbol' : 'Nama ' . $name }}</th>
                        <th class="hidden px-4 py-3.5 sm:table-cell">{{ $secondaryLabel ?: 'Keterangan' }}</th>
                        <th class="px-5 py-3.5 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5 sm:px-6 font-bold text-slate-900 text-xs sm:text-sm">
                                {{ $item->{$nameKey} }}
                            </td>
                            <td class="hidden px-4 py-3.5 sm:table-cell text-xs text-slate-500">
                                {{ $item->{$secondaryProp} ?: '-' }}
                            </td>
                            <td class="px-5 py-3.5 sm:px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $item->id }})"
                                        class="rounded-md px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="delete({{ $item->id }})"
                                        wire:confirm="Hapus data {{ strtolower($name) }} ini?"
                                        class="rounded-md px-2.5 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-xs text-slate-400">
                                Belum ada data {{ strtolower($name) }} yang tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="mt-4 pt-4 border-t border-slate-100">
                {{ $items->links() }}
            </div>
        @endif
    </x-card>

    {{-- Modal Add / Edit Master --}}
    <x-modal :show="$showModal" :title="$editing ? 'Edit ' . $name : 'Tambah ' . $name . ' Baru'">
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input wire:model="name" :label="$nameKey === 'code' ? 'Kode / Singkatan' : 'Nama Lengkap ' . $name" required :error="$errors->first('name')" />
            </div>
            @if($secondaryLabel)
                <div>
                    <x-input wire:model="secondary" :label="$secondaryLabel" />
                </div>
            @endif
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
                    Simpan Data
                </button>
            </div>
        </form>
    </x-modal>
</div>
