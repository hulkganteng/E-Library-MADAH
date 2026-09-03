@props(['title' => 'Pengumuman'])
<div class="space-y-6">
    <x-page-header
        title="Pengumuman Perpustakaan"
        subtitle="Kelola siaran pengumuman, jadwal literasi, dan informasi bagi seluruh civitas madrasah."
    >
        <x-slot:actions>
            @can('pengumuman.create')
            <button
                type="button"
                wire:click="create"
                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Buat Pengumuman Baru</span>
            </button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="mx-auto max-w-3xl space-y-4">
        @forelse($items as $item)
            <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-soft">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-snug">{{ $item->title }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $item->published_at?->format('d M Y, H:i') ?? $item->created_at->format('d M Y, H:i') }} · Oleh {{ $item->author?->name ?? 'Admin' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <x-badge color="{{ $item->is_published ? 'green' : 'slate' }}" size="sm" dot>
                            {{ $item->is_published ? 'Diterbitkan' : 'Draf' }}
                        </x-badge>
                        <x-badge color="blue" size="sm">
                            Audiens: {{ ucfirst($item->audience) }}
                        </x-badge>
                    </div>
                </div>

                <div class="mt-3 text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line border-t border-slate-100 pt-3">
                    {{ $item->content }}
                </div>

                <div class="mt-4 flex items-center justify-end gap-2 border-t border-slate-100 pt-3">
                    @can('pengumuman.edit')
                    <button
                        type="button"
                        wire:click="edit({{ $item->id }})"
                        class="rounded-md px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                    >
                        Edit
                    </button>
                    @endcan
                    @can('pengumuman.delete')
                    <button
                        type="button"
                        wire:click="delete({{ $item->id }})"
                        wire:confirm="Hapus pengumuman ini?"
                        class="rounded-md px-2.5 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                    >
                        Hapus
                    </button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 bg-white py-16 text-center text-xs text-slate-400">
                Belum ada pengumuman yang dibuat.
            </div>
        @endforelse

        @if($items->hasPages())
            <div class="mt-4 pt-4">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Add / Edit Announcement --}}
    <x-modal :show="$showModal" :title="$editing ? 'Edit Pengumuman' : 'Buat Pengumuman Baru'" maxWidth="max-w-xl">
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input wire:model="title" label="Judul Pengumuman" required placeholder="Contoh: Jadwal Pengembalian Buku Akhir Semester" :error="$errors->first('title')" />
            </div>

            <div>
                <x-textarea wire:model="content" label="Isi Lengkap Pengumuman" required rows="5" placeholder="Tuliskan detail isi pengumuman..." :error="$errors->first('content')" />
            </div>

            <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                <x-select wire:model="audience" label="Target Sasaran Audiens">
                    <option value="semua">Semua Pengguna</option>
                    <option value="siswa">Khusus Siswa</option>
                    <option value="guru">Khusus Guru</option>
                    <option value="pustakawan">Pustakawan</option>
                    <option value="admin">Administrator</option>
                </x-select>

                <div class="flex items-center pt-6">
                    <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                        <input type="checkbox" wire:model="is_published" class="rounded border-slate-300 text-emerald-700 focus:ring-emerald-500">
                        <span>Langsung Terbitkan Sekarang</span>
                    </label>
                </div>
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
                    Simpan Pengumuman
                </button>
            </div>
        </form>
    </x-modal>
</div>
