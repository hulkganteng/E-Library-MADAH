@props(['title' => 'Manajemen Buku'])
<div class="space-y-6">
    <x-page-header
        title="Manajemen Koleksi Buku"
        subtitle="Kelola katalog buku, data bibliografi, dan ketersediaan eksemplar."
    >
        <x-slot:actions>
            @can('buku.import')
            <button
                type="button"
                wire:click="downloadTemplate"
                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition cursor-pointer"
                title="Unduh format file Excel untuk import data buku"
            >
                <svg class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M12 12.75l-3-3m0 0l3-3m-3 3h12" /></svg>
                <span>Unduh Template Excel</span>
            </button>

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
                wire:click="openApiModal"
                class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50/70 px-3.5 py-2 text-xs font-semibold text-indigo-700 shadow-2xs hover:bg-indigo-100 transition cursor-pointer"
                title="Uji coba ambil data buku dari Google Books API atau Open Library API"
            >
                <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                </svg>
                <span>Uji Coba API Buku</span>
            </button>
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
                                <p class="sm:hidden text-[11px] text-emerald-700 font-medium mt-0.5">
                                    {{ $book->category?->name ?? 'Umum' }}
                                    @if($book->authors->isNotEmpty())
                                        • {{ $book->authors->pluck('name')->implode(', ') }}
                                    @endif
                                </p>
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
            {{-- Quick Autofill via API (Hanya saat Tambah Baru) --}}
            @if(!$editing)
                <div class="rounded-xl border border-indigo-200/90 bg-indigo-50/50 p-3 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-900">
                            <svg class="h-3.5 w-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                            <span>Isi Otomatis dari Internet</span>
                        </span>
                        <div class="flex items-center gap-2 text-[11px] text-slate-600">
                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                <input type="radio" wire:model.live="apiSource" value="openlibrary" class="text-indigo-600"> Open Library
                            </label>
                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                <input type="radio" wire:model.live="apiSource" value="google" class="text-indigo-600"> Google Books
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <input
                            type="text"
                            wire:model="apiQuery"
                            wire:keydown.enter.prevent="searchApiInModal"
                            placeholder="Ketik judul buku atau scan ISBN (contoh: Laskar Pelangi)..."
                            class="flex-1 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-hidden focus:ring-1 focus:ring-indigo-500"
                        />
                        <button
                            type="button"
                            wire:click="searchApiInModal"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-1 rounded-lg bg-indigo-600 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700 transition cursor-pointer disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="searchApiInModal">Cari</span>
                            <span wire:loading wire:target="searchApiInModal" class="inline-block h-3.5 w-3.5 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                        </button>
                    </div>

                    {{-- Quick Results Cards --}}
                    @if(count($apiQuickResults) > 0)
                        <div class="space-y-1.5 pt-1">
                            <p class="text-[11px] font-semibold text-slate-600">Pilih buku yang sesuai untuk mengisi form otomatis:</p>
                            @foreach($apiQuickResults as $qIdx => $qb)
                                <div class="flex items-center justify-between gap-2 rounded-lg border border-slate-200 bg-white p-2 text-xs shadow-2xs hover:border-indigo-300 transition">
                                    <div class="flex items-center gap-2 min-w-0">
                                        @if(!empty($qb['cover_url']))
                                            <img src="{{ $qb['cover_url'] }}" class="h-9 w-6.5 object-cover rounded shrink-0" alt="Cover">
                                        @else
                                            <div class="h-9 w-6.5 rounded bg-slate-100 flex items-center justify-center shrink-0 text-slate-400 text-[10px]">📖</div>
                                        @endif
                                        <div class="min-w-0 truncate">
                                            <p class="font-bold text-slate-900 truncate">{{ $qb['title'] }}</p>
                                            <p class="text-[11px] text-slate-500 truncate">{{ !empty($qb['authors']) ? implode(', ', $qb['authors']) : 'Penulis tidak diketahui' }} {{ $qb['publish_year'] ? '('.$qb['publish_year'].')' : '' }}</p>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="applyQuickApiBook({{ $qIdx }})"
                                        class="shrink-0 rounded bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-600 hover:text-white transition cursor-pointer"
                                    >
                                        Gunakan
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- 1. Informasi Pokok --}}
            <div class="space-y-3.5">
                <div>
                    <x-input wire:model="title" label="Judul Buku" required placeholder="Contoh: Fiqih Ibadah Praktis" :error="$errors->first('title')" />
                </div>

                {{-- Penulis Interaktif & Mudah --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Penulis Buku</label>
                    
                    {{-- Badge Penulis Terpilih --}}
                    @if(count($authors) > 0)
                        <div class="flex flex-wrap items-center gap-1.5">
                            @foreach($authorList->whereIn('id', $authors) as $a)
                                <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-xs font-medium text-emerald-800">
                                    <span>{{ $a->name }}</span>
                                    <button type="button" wire:click="removeAuthor({{ $a->id }})" class="hover:text-rose-600 font-bold ml-0.5 cursor-pointer">&times;</button>
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Form Input / Pilih Penulis --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <select wire:change="selectAuthor($event.target.value); $event.target.value='';" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 focus:border-emerald-600 focus:outline-hidden">
                            <option value="">-- Pilih dari Daftar Penulis --</option>
                            @foreach($authorList as $a)
                                @if(!in_array((string)$a->id, $authors, true))
                                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <div class="flex gap-1.5">
                            <input
                                type="text"
                                wire:model="newAuthorName"
                                wire:keydown.enter.prevent="addAuthor"
                                placeholder="Atau ketik nama penulis baru..."
                                class="flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 focus:border-emerald-600 focus:outline-hidden"
                            />
                            <button
                                type="button"
                                wire:click="addAuthor"
                                class="rounded-lg border border-slate-300 bg-slate-50 px-2.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer shrink-0"
                            >
                                + Tambah
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Kategori & Penerbit --}}
                <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                    <x-select wire:model="category_id" label="Kategori Buku">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </x-select>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Penerbit</label>
                            <button
                                type="button"
                                wire:click="$toggle('showNewPublisher')"
                                class="text-[11px] font-medium text-emerald-700 hover:underline cursor-pointer"
                            >
                                {{ $showNewPublisher ? 'Pilih Yang Ada' : '+ Buat Penerbit Baru' }}
                            </button>
                        </div>
                        @if($showNewPublisher)
                            <div class="flex gap-1.5">
                                <input
                                    type="text"
                                    wire:model="newPublisherName"
                                    wire:keydown.enter.prevent="createPublisher"
                                    placeholder="Nama penerbit baru..."
                                    class="flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 focus:border-emerald-600 focus:outline-hidden"
                                />
                                <button
                                    type="button"
                                    wire:click="createPublisher"
                                    class="rounded-lg bg-emerald-700 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-800 transition cursor-pointer shrink-0"
                                >
                                    Simpan
                                </button>
                            </div>
                        @else
                            <select wire:model="publisher_id" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 focus:border-emerald-600 focus:outline-hidden">
                                <option value="">-- Pilih Penerbit --</option>
                                @foreach($publishers as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>

                {{-- ISBN & Eksemplar Awal --}}
                <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                    <x-input wire:model="isbn" label="Nomor ISBN" placeholder="978-..." :error="$errors->first('isbn')" />
                    @if(!$editing)
                        <x-input wire:model="initial_copies" label="Jumlah Eksemplar Fisik" type="number" min="0" placeholder="1" hint="Jumlah eksemplar & barcode yang langsung dicetak." />
                    @else
                        <x-select wire:model="type" label="Format Buku">
                            <option value="fisik">Buku Fisik</option>
                            <option value="ebook">E-Book Digital</option>
                        </x-select>
                    @endif
                </div>
            </div>

            {{-- 2. Detail Tambahan (Collapsible / Mudah) --}}
            <div class="rounded-xl border border-slate-200/80 bg-slate-50/50 overflow-hidden">
                <button
                    type="button"
                    wire:click="$toggle('showAdvanced')"
                    class="w-full flex items-center justify-between p-3 text-xs font-semibold text-slate-700 hover:bg-slate-100/70 transition cursor-pointer text-left"
                >
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-slate-500 transition-transform duration-200 {{ $showAdvanced ? 'rotate-90' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                        <span>Detail Lanjutan (Tahun, Halaman, Rak, Cover, Sinopsis)</span>
                    </div>
                    <span class="text-[11px] font-normal text-slate-400">{{ $showAdvanced ? 'Sembunyikan' : 'Buka Opsi Tambahan' }}</span>
                </button>

                @if($showAdvanced)
                    <div class="p-3.5 pt-1 space-y-3.5 border-t border-slate-200/60 bg-white">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <x-input wire:model="publish_year" label="Tahun Terbit" type="number" placeholder="2024" />
                            <x-input wire:model="edition" label="Edisi / Cetakan" placeholder="Cetakan ke-1" />
                            <x-input wire:model="page_count" label="Jumlah Halaman" type="number" placeholder="250" />
                        </div>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @if(!$editing)
                                <x-select wire:model="type" label="Format Buku">
                                    <option value="fisik">Buku Fisik</option>
                                    <option value="ebook">E-Book Digital</option>
                                </x-select>
                            @endif
                            <x-select wire:model="shelf_id" label="Lokasi Rak">
                                <option value="">-- Pilih Rak --</option>
                                @foreach($shelves as $s)
                                    <option value="{{ $s->id }}">{{ $s->code }}</option>
                                @endforeach
                            </x-select>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-700">Foto Sampul Buku</label>
                            <input type="file" wire:model="cover" class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-emerald-800 hover:file:bg-emerald-100">
                            @error('cover')<p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror

                            @if($apiCoverUrl && !$cover)
                                <div class="mt-2 flex items-center gap-3 p-2 rounded-lg bg-indigo-50 border border-indigo-100">
                                    <img src="{{ $apiCoverUrl }}" class="h-12 w-9 object-cover rounded shadow-xs" alt="Cover API">
                                    <div class="text-xs text-indigo-950">
                                        <p class="font-medium">Sampul otomatis dari API</p>
                                        <p class="text-[11px] text-indigo-600">Akan diunduh otomatis saat menyimpan jika tidak memilih file baru.</p>
                                    </div>
                                    <button type="button" wire:click="$set('apiCoverUrl', null)" class="ml-auto text-xs font-semibold text-rose-600 hover:underline cursor-pointer">Batal</button>
                                </div>
                            @endif
                        </div>

                        <div>
                            <x-textarea wire:model="description" label="Sinopsis / Deskripsi Buku" rows="2" placeholder="Ringkasan isi buku..." />
                        </div>

                        <div class="rounded-lg bg-slate-50 p-2.5 border border-slate-200">
                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-800 cursor-pointer">
                                <input type="checkbox" wire:model="is_repository" class="rounded border-slate-300 text-emerald-700 focus:ring-emerald-500">
                                <span>Tandai sebagai Karya Repository Mandiri (Karya Siswa / Guru MA Assadah)</span>
                            </label>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Footer Tombol Aksi --}}
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
                    class="rounded-lg bg-emerald-700 px-5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
                >
                    Simpan Data Buku
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Uji Coba API Buku --}}
    <x-modal :show="$showApiModal" model="showApiModal" title="Uji Coba Ambil Data Buku dari API" maxWidth="max-w-4xl">
        <div class="space-y-4">
            <p class="text-xs text-slate-500 leading-relaxed">
                Uji coba integrasi mengambil metadata buku otomatis melalui <span class="font-medium text-slate-700">Open Library API</span> atau <span class="font-medium text-slate-700">Google Books API</span>. Hasil pencarian dapat langsung digunakan untuk mengisi form atau disimpan langsung ke database.
            </p>

            {{-- Pilihan Sumber API --}}
            <div class="flex flex-wrap items-center gap-2 p-1 rounded-lg bg-slate-100 text-xs font-semibold text-slate-700">
                <button
                    type="button"
                    wire:click="$set('apiSource', 'openlibrary')"
                    class="flex-1 py-1.5 px-3 rounded-md transition text-center cursor-pointer {{ $apiSource === 'openlibrary' ? 'bg-white text-indigo-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
                >
                    Open Library API (openlibrary.org)
                </button>
                <button
                    type="button"
                    wire:click="$set('apiSource', 'google')"
                    class="flex-1 py-1.5 px-3 rounded-md transition text-center cursor-pointer {{ $apiSource === 'google' ? 'bg-white text-indigo-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
                >
                    Google Books API (googleapis.com)
                </button>
            </div>

            {{-- Input Pencarian & Form --}}
            <div class="space-y-2">
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="flex-1">
                        <input
                            type="text"
                            wire:model="apiQuery"
                            wire:keydown.enter="searchApi"
                            placeholder="{{ $apiSource === 'google' ? 'Ketik judul buku, penulis, atau ISBN (contoh: Laskar Pelangi)...' : 'Ketik judul buku atau ISBN (contoh: Bumi Manusia)...' }}"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-hidden focus:ring-1 focus:ring-indigo-500"
                        />
                    </div>
                    <button
                        type="button"
                        wire:click="searchApi"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-indigo-700 transition cursor-pointer disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="searchApi,setQueryAndSearch">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                        </span>
                        <span wire:loading wire:target="searchApi,setQueryAndSearch" class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                        <span>Cari / Uji Coba</span>
                    </button>
                </div>

                {{-- Opsi Kunci API Google jika sumber Google Books --}}
                @if($apiSource === 'google')
                    <div x-data="{ showKey: false }" class="text-[11px] text-slate-500 pt-1">
                        <button type="button" @click="showKey = !showKey" class="text-indigo-600 hover:underline cursor-pointer">
                            <span x-text="showKey ? '− Sembunyikan Pengaturan Google Books API Key' : '+ Tambah / Atur Google Books API Key (Opsional)'"></span>
                        </button>
                        <div x-show="showKey" x-cloak class="mt-2 p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                            <label class="block font-medium text-slate-700 mb-1">Google Books API Key (Opsional jika kuota publik habis)</label>
                            <input
                                type="text"
                                wire:model="googleApiKey"
                                placeholder="Masukkan API Key Google Cloud jika ada (atau simpan di .env: GOOGLE_BOOKS_API_KEY)"
                                class="w-full rounded border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-mono text-slate-900"
                            />
                        </div>
                    </div>
                @endif

                {{-- Preset Uji Coba Cepat --}}
                <div class="flex flex-wrap items-center gap-1.5 text-[11px] text-slate-500 pt-1">
                    <span class="font-medium text-slate-600">Uji Coba Cepat:</span>
                    <button type="button" wire:click="setQueryAndSearch('Laskar Pelangi')" class="rounded bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 px-2 py-0.5 transition cursor-pointer">Laskar Pelangi</button>
                    <button type="button" wire:click="setQueryAndSearch('Bumi Manusia')" class="rounded bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 px-2 py-0.5 transition cursor-pointer">Bumi Manusia</button>
                    <button type="button" wire:click="setQueryAndSearch('Fiqih Sunnah')" class="rounded bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 px-2 py-0.5 transition cursor-pointer">Fiqih Sunnah</button>
                    <button type="button" wire:click="setQueryAndSearch('Clean Code')" class="rounded bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 px-2 py-0.5 transition cursor-pointer">Clean Code</button>
                </div>
            </div>

            {{-- Error Message --}}
            @if($apiError)
                <div class="rounded-lg border border-rose-200 bg-rose-50/80 p-3 text-xs text-rose-800 space-y-1">
                    <div class="flex items-center gap-2 font-bold text-rose-900">
                        <svg class="h-4 w-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                        <span>Gagal Mengambil Data</span>
                    </div>
                    <p>{{ $apiError }}</p>
                    @if($apiSource === 'google' && str_contains($apiError, '429'))
                        <div class="pt-1">
                            <button
                                type="button"
                                wire:click="$set('apiSource', 'openlibrary'); searchApi();"
                                class="inline-flex items-center gap-1 rounded bg-indigo-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-indigo-700 transition cursor-pointer"
                            >
                                Beralih ke Open Library API &rarr;
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Diagnostics Box & URL --}}
            @if($lastExecutedUrl)
                <div class="rounded-lg bg-slate-50 border border-slate-200/80 p-2.5 text-xs text-slate-600 space-y-1.5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded {{ $apiError ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }} px-1.5 py-0.5 font-mono text-[10px] font-bold">
                                {{ $apiError ? 'Error' : '200 OK' }}
                            </span>
                            <span class="text-[11px] text-slate-500">Waktu Respon: <strong class="text-slate-800">{{ $lastResponseTime ?? '-' }} ms</strong></span>
                            <span class="text-[11px] text-slate-500">Hasil: <strong class="text-slate-800">{{ count($apiResults) }} buku</strong></span>
                        </div>
                        @if($apiRawJson)
                            <button
                                type="button"
                                wire:click="$toggle('showRawJson')"
                                class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 cursor-pointer"
                            >
                                {{ $showRawJson ? 'Tutup Raw JSON' : 'Lihat Raw JSON (Payload API)' }}
                            </button>
                        @endif
                    </div>
                    <div class="font-mono text-[11px] text-slate-500 truncate" title="{{ $lastExecutedUrl }}">
                        URL: {{ $lastExecutedUrl }}
                    </div>
                    @if($showRawJson && $apiRawJson)
                        <pre class="mt-2 max-h-56 overflow-auto rounded bg-slate-900 p-3 font-mono text-[11px] text-emerald-400">{{ $apiRawJson }}</pre>
                    @endif
                </div>
            @endif

            {{-- Loading State --}}
            <div wire:loading wire:target="searchApi,setQueryAndSearch" class="w-full py-8 text-center">
                <div class="inline-block h-7 w-7 animate-spin rounded-full border-2 border-indigo-600 border-t-transparent"></div>
                <p class="mt-2 text-xs font-medium text-slate-600">Menghubungi {{ $apiSource === 'google' ? 'Google Books API' : 'Open Library API' }}...</p>
            </div>

            {{-- Hasil Pencarian Buku --}}
            <div wire:loading.remove wire:target="searchApi,setQueryAndSearch">
                @if(count($apiResults) > 0)
                    <div class="space-y-3 max-h-[50vh] overflow-y-auto pr-1">
                        @foreach($apiResults as $idx => $b)
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3.5 rounded-lg border border-slate-200 bg-white p-3.5 hover:border-indigo-300 transition shadow-2xs">
                                {{-- Thumbnail --}}
                                <div class="h-20 w-14 shrink-0 rounded bg-slate-100 overflow-hidden flex items-center justify-center border border-slate-200">
                                    @if(!empty($b['cover_url']))
                                        <img src="{{ $b['cover_url'] }}" alt="{{ $b['title'] }}" class="h-full w-full object-cover" loading="lazy" />
                                    @else
                                        <svg class="h-6 w-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                                    @endif
                                </div>

                                {{-- Info Buku --}}
                                <div class="flex-1 min-w-0 space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-xs sm:text-sm text-slate-900 truncate" title="{{ $b['title'] }}">{{ $b['title'] }}</h4>
                                        <span class="shrink-0 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600">{{ $b['source'] }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-600 flex flex-wrap items-center gap-x-3 gap-y-0.5">
                                        <span>Penulis: <strong>{{ !empty($b['authors']) ? implode(', ', $b['authors']) : '-' }}</strong></span>
                                        <span>Penerbit: <strong>{{ $b['publisher'] ?: '-' }}</strong></span>
                                        <span>Tahun: <strong>{{ $b['publish_year'] ?: '-' }}</strong></span>
                                        <span>ISBN: <strong class="font-mono">{{ $b['isbn'] ?: '-' }}</strong></span>
                                        @if(!empty($b['page_count']))
                                            <span>Hal: <strong>{{ $b['page_count'] }}</strong></span>
                                        @endif
                                    </div>
                                    @if(!empty($b['description']))
                                        <p class="text-[11px] text-slate-400 line-clamp-2">{{ $b['description'] }}</p>
                                    @endif
                                </div>

                                {{-- Aksi Import --}}
                                <div class="flex sm:flex-col items-center gap-1.5 shrink-0 w-full sm:w-auto justify-end pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                    <button
                                        type="button"
                                        wire:click="useApiBook({{ $idx }})"
                                        class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1 rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                                        title="Pindahkan isi buku ini ke form input buku untuk diedit terlebih dahulu"
                                    >
                                        <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                        <span>Gunakan di Form</span>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="directSaveApiBook({{ $idx }})"
                                        class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1 rounded-md bg-emerald-700 px-2.5 py-1.5 text-xs font-semibold text-white shadow-2xs hover:bg-emerald-800 transition cursor-pointer"
                                        title="Simpan langsung buku ini ke database beserta 1 eksemplar"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                        <span>Simpan Langsung</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif($lastExecutedUrl && !$apiError)
                    <div class="py-8 text-center text-xs text-slate-400">
                        Tidak ada data buku yang ditemukan. Coba kata kunci lain atau gunakan sumber API berbeda.
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                <button
                    type="button"
                    wire:click="$set('showApiModal', false)"
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                >
                    Tutup
                </button>
            </div>
        </div>
    </x-modal>
</div>
