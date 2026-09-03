@props(['title' => 'Manajemen Buku'])
<div class="space-y-6">
    <x-page-header
        title="Manajemen Koleksi Buku"
        subtitle="Kelola katalog buku, data bibliografi, dan ketersediaan eksemplar."
    >
        <x-slot:actions>
            @can('buku.import')
            <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                <svg class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                <span>Import Excel</span>
                <input type="file" wire:model="importFile" class="hidden">
            </label>
            @if($importFile)
                <button
                    wire:click="import"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    <span>Proses Import File</span>
                </button>
            @endif
            @endcan
            @can('buku.create')
            <button
                type="button"
                wire:click="create"
                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Tambah Buku</span>
            </button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    {{-- Search Filter --}}
    <div class="max-w-md">
        <x-input
            wire:model.live.debounce.300ms="search"
            placeholder="Cari judul buku, nomor ISBN, atau penulis..."
        />
    </div>

    {{-- Data Table Card --}}
    <x-card>
        <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5 sm:px-6">Judul & ISBN</th>
                        <th class="hidden px-4 py-3.5 sm:table-cell">Kategori</th>
                        <th class="hidden px-4 py-3.5 md:table-cell">Penulis</th>
                        <th class="px-4 py-3.5">Format & Stok</th>
                        <th class="px-5 py-3.5 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($books as $book)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5 sm:px-6">
                                <p class="font-bold text-slate-900 text-xs sm:text-sm">{{ $book->title }}</p>
                                <p class="font-mono text-[11px] text-slate-400 mt-0.5">{{ $book->isbn ?: 'Tanpa ISBN' }}</p>
                            </td>
                            <td class="hidden px-4 py-3.5 sm:table-cell text-xs text-slate-600">
                                {{ $book->category?->name ?? '-' }}
                            </td>
                            <td class="hidden px-4 py-3.5 md:table-cell text-xs text-slate-600">
                                {{ $book->authors->pluck('name')->implode(', ') ?: '-' }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <x-badge color="{{ $book->type === 'ebook' ? 'blue' : 'brand' }}" size="sm">
                                        {{ $book->type === 'ebook' ? 'E-Book' : $book->copies->count() . ' Eksemplar' }}
                                    </x-badge>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 sm:px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a
                                        href="{{ route('catalog.show', $book) }}"
                                        class="rounded-md px-2.5 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 transition"
                                    >
                                        Lihat
                                    </a>
                                    @can('buku.edit')
                                    <button
                                        type="button"
                                        wire:click="edit({{ $book->id }})"
                                        class="rounded-md px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                                    >
                                        Edit
                                    </button>
                                    @endcan
                                    @can('buku.delete')
                                    <button
                                        type="button"
                                        wire:click="delete({{ $book->id }})"
                                        wire:confirm="Yakin ingin menghapus buku ini beserta seluruh eksemplarnya?"
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
                            <td colspan="5" class="px-6 py-12 text-center text-xs text-slate-400">
                                Belum ada koleksi buku yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($books->hasPages())
            <div class="mt-4 pt-4 border-t border-slate-100">
                {{ $books->links() }}
            </div>
        @endif
    </x-card>

    {{-- Modal Add / Edit Book --}}
    <x-modal :show="$showModal" :title="$editing ? 'Edit Informasi Buku' : 'Tambah Buku Baru'" maxWidth="max-w-2xl">
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input wire:model="title" label="Judul Buku" required placeholder="Contoh: Fiqih Ibadah Praktis" :error="$errors->first('title')" />
            </div>

            <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                <x-select wire:model="category_id" label="Kategori Buku">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </x-select>
                <x-select wire:model="publisher_id" label="Penerbit">
                    <option value="">-- Pilih Penerbit --</option>
                    @foreach($publishers as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </x-select>
            </div>

            <div>
                <x-select wire:model="authors" label="Penulis (Bisa pilih lebih dari satu)" multiple size="4" hint="Tahan Ctrl / Cmd untuk memilih beberapa penulis">
                    @foreach($authorList as $a)
                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                    @endforeach
                </x-select>
            </div>

            <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-3">
                <x-input wire:model="isbn" label="ISBN" placeholder="978-..." :error="$errors->first('isbn')" />
                <x-input wire:model="publish_year" label="Tahun Terbit" type="number" placeholder="2024" />
                <x-input wire:model="edition" label="Edisi / Cetakan" placeholder="Cetakan ke-1" />
            </div>

            <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-3">
                <x-input wire:model="page_count" label="Jumlah Halaman" type="number" placeholder="250" />
                <x-select wire:model="type" label="Format Buku">
                    <option value="fisik">Buku Fisik</option>
                    <option value="ebook">E-Book Digital</option>
                </x-select>
                <x-select wire:model="shelf_id" label="Lokasi Rak">
                    <option value="">-- Pilih Rak --</option>
                    @foreach($shelves as $s)
                        <option value="{{ $s->id }}">{{ $s->code }}</option>
                    @endforeach
                </x-select>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700">Foto Sampul Buku</label>
                <input type="file" wire:model="cover" class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-emerald-800 hover:file:bg-emerald-100">
                @error('cover')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
            </div>

            @if(!$editing)
                <div>
                    <x-input wire:model="initial_copies" label="Jumlah Eksemplar Awal" type="number" min="0" placeholder="1" hint="Jumlah eksemplar barcode yang akan dibuat otomatis." />
                </div>
            @endif

            <div>
                <x-textarea wire:model="description" label="Sinopsis / Deskripsi Buku" rows="3" placeholder="Ringkasan isi buku..." />
            </div>

            <div class="rounded-lg bg-slate-50 p-3 border border-slate-200/80">
                <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-800 cursor-pointer">
                    <input type="checkbox" wire:model="is_repository" class="rounded border-slate-300 text-emerald-700 focus:ring-emerald-500">
                    <span>Tandai sebagai Karya Repository Mandiri (Karya Siswa / Guru MA Assadah)</span>
                </label>
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
                    Simpan Data Buku
                </button>
            </div>
        </form>
    </x-modal>
</div>
