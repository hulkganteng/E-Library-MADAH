@props(['title' => $book->title])
<div class="space-y-6">
    {{-- Navigation Breadcrumb --}}
    <div>
        <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 hover:text-emerald-800 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            Kembali ke Katalog Buku
        </a>
    </div>

    {{-- Feedback Alerts --}}
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200/80 p-4 text-xs font-semibold text-emerald-800 flex items-center gap-2">
            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200/80 p-4 text-xs font-semibold text-rose-800 flex items-center gap-2">
            <svg class="h-4 w-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Main Profile Grid --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left Column: Cover & Quick Actions --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-soft">
                {{-- Book Cover --}}
                <div class="relative flex aspect-[3/4] max-h-80 sm:max-h-none w-full items-center justify-center overflow-hidden rounded-lg bg-gradient-to-b from-slate-100 to-slate-200/80 p-4 sm:p-5">
                    @if($book->cover)
                        <img src="{{ asset('storage/' . $book->cover) }}" class="h-full max-h-full w-auto object-contain rounded-xs shadow-lg" alt="{{ $book->title }}">
                    @else
                        <div class="flex flex-col items-center justify-center text-center text-slate-400">
                            <svg class="h-16 w-16 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                            <span class="text-xs font-semibold text-slate-500">Tidak ada sampul</span>
                        </div>
                    @endif
                </div>

                {{-- Badges --}}
                <div class="mt-4 flex flex-wrap items-center gap-1.5">
                    <x-badge color="{{ $book->type === 'ebook' ? 'blue' : 'brand' }}" size="md">
                        {{ $book->type === 'ebook' ? 'E-Book Digital' : 'Buku Fisik' }}
                    </x-badge>
                    @if($book->is_repository)
                        <x-badge color="gold" size="md">Karya Siswa / Guru</x-badge>
                    @endif
                </div>

                {{-- Availability & Actions --}}
                <div class="mt-5 space-y-3 pt-4 border-t border-slate-100">
                    @if($book->type === 'ebook')
                        <a
                            href="{{ asset('storage/' . $book->ebook_file) }}"
                            target="_blank"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-emerald-800 transition"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" /></svg>
                            Baca E-Book Digital
                        </a>
                    @else
                        <div class="rounded-lg border border-slate-200/80 bg-slate-50/60 p-3.5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status Ketersediaan</p>
                            @php($avail = $book->availableCopies->count())
                            @php($total = $book->copies->count())
                            <div class="mt-1 flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-1">
                                <span class="text-xl font-extrabold {{ $avail > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                    {{ $avail }} / {{ $total }} Eksemplar
                                </span>
                                <span class="text-xs font-semibold {{ $avail > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                    {{ $avail > 0 ? 'Tersedia di Perpustakaan' : 'Sedang Dipinjam' }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-2">
                            @if($avail > 0)
                                <button
                                    wire:click="openBorrowModal"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                    <span>Isi Form Peminjaman Buku</span>
                                </button>
                            @else
                                <button
                                    wire:click="reserve"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-amber-700 transition cursor-pointer"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span>Reservasi Antrean Buku</span>
                                </button>
                            @endif

                            <button
                                wire:click="toggleFavorite"
                                class="flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition cursor-pointer shadow-2xs"
                            >
                                @if(auth()->check() && auth()->user()->favorites()->where('book_id', $book->id)->exists())
                                    <svg class="h-4 w-4 text-amber-500 fill-amber-500" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                    <span>Buku Favorit Anda</span>
                                @else
                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
                                    <span>Tambah ke Favorit</span>
                                @endif
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Detailed Information --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                {{-- Book Title & Author --}}
                <div class="border-b border-slate-100 pb-5">
                    @if($book->category)
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">{{ $book->category->name }}</p>
                    @endif
                    <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl leading-snug">
                        {{ $book->title }}
                    </h1>
                    <p class="mt-2 text-sm text-slate-600 font-medium">
                        Oleh: <span class="text-slate-900 font-semibold">{{ $book->authors->pluck('name')->implode(', ') ?: 'Penulis tidak dicantumkan' }}</span>
                    </p>
                </div>

                {{-- Detailed Specs Grid --}}
                <div class="py-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Informasi Detail Buku</h3>
                    <dl class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                        <div class="rounded-lg bg-slate-50/70 p-3 border border-slate-100">
                            <dt class="text-[11px] font-semibold uppercase text-slate-500">ISBN</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-800 font-mono">{{ $book->isbn ?: '-' }}</dd>
                        </div>
                        <div class="rounded-lg bg-slate-50/70 p-3 border border-slate-100">
                            <dt class="text-[11px] font-semibold uppercase text-slate-500">Penerbit</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $book->publisher?->name ?: '-' }}</dd>
                        </div>
                        <div class="rounded-lg bg-slate-50/70 p-3 border border-slate-100">
                            <dt class="text-[11px] font-semibold uppercase text-slate-500">Tahun Terbit</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $book->publish_year ?: '-' }}</dd>
                        </div>
                        <div class="rounded-lg bg-slate-50/70 p-3 border border-slate-100">
                            <dt class="text-[11px] font-semibold uppercase text-slate-500">Edisi / Cetakan</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $book->edition ?: '-' }}</dd>
                        </div>
                        <div class="rounded-lg bg-slate-50/70 p-3 border border-slate-100">
                            <dt class="text-[11px] font-semibold uppercase text-slate-500">Jumlah Halaman</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $book->page_count ? $book->page_count . ' hlm' : '-' }}</dd>
                        </div>
                        <div class="rounded-lg bg-slate-50/70 p-3 border border-slate-100">
                            <dt class="text-[11px] font-semibold uppercase text-slate-500">Lokasi Rak</dt>
                            <dd class="mt-1 text-sm font-bold text-emerald-800">{{ $book->shelf?->code ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Book Description --}}
                @if($book->description)
                    <div class="border-t border-slate-100 pt-5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Sinopsis / Ringkasan</h3>
                        <div class="prose prose-sm max-w-none text-slate-600 leading-relaxed">
                            {{ $book->description }}
                        </div>
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    {{-- Form Modal Peminjaman Buku --}}
    @if($showBorrowModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-md bg-white rounded-xl shadow-xl overflow-hidden p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>Form Pengajuan Peminjaman</span>
                    </h3>
                    <button type="button" wire:click="$set('showBorrowModal', false)" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
                </div>

                <div class="rounded-lg bg-emerald-50 border border-emerald-200/80 p-3 text-xs text-emerald-900 space-y-1">
                    <p class="font-bold">{{ $book->title }}</p>
                    <p class="text-[11px] text-emerald-700">Penulis: {{ $book->authors->pluck('name')->implode(', ') ?: '-' }}</p>
                </div>

                <div class="space-y-3">
                    <x-input
                        wire:model="borrowerName"
                        label="Nama Peminjam *"
                        placeholder="Contoh: Ahmad Subagyo"
                    />
                    @error('borrowerName') <p class="text-xs text-rose-600 -mt-2">{{ $message }}</p> @enderror
                    <x-input
                        wire:model="borrowerClass"
                        label="Kelas *"
                        placeholder="Contoh: XII IPA 1"
                    />
                    @error('borrowerClass') <p class="text-xs text-rose-600 -mt-2">{{ $message }}</p> @enderror

                    @if($book->availableCopies->count() > 1)
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Kode Eksemplar</label>
                            <select wire:model="selectedCopyId" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs text-slate-800 focus:border-emerald-500 focus:ring-emerald-500">
                                @foreach($book->availableCopies as $copy)
                                    <option value="{{ $copy->id }}">{{ $copy->inventory_code }} (Kondisi: {{ ucfirst($copy->condition) }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <x-input
                        wire:model="dueDays"
                        label="Durasi Peminjaman (Hari)"
                        type="number"
                        min="1"
                    />
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button
                        type="button"
                        wire:click="$set('showBorrowModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="submitBorrowRequest"
                        class="px-4 py-2 text-xs font-semibold text-white bg-emerald-700 hover:bg-emerald-800 rounded-lg transition cursor-pointer"
                    >
                        Kirim Pengajuan Peminjaman
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
