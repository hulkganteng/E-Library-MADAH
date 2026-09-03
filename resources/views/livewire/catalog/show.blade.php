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
                <div class="relative flex aspect-[3/4] w-full items-center justify-center overflow-hidden rounded-lg bg-gradient-to-b from-slate-100 to-slate-200/80 p-5">
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
                            <div class="mt-1 flex items-baseline justify-between">
                                <span class="text-xl font-extrabold {{ $avail > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                    {{ $avail }} / {{ $total }} Eksemplar
                                </span>
                                <span class="text-xs font-semibold {{ $avail > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                    {{ $avail > 0 ? 'Tersedia di Perpustakaan' : 'Sedang Dipinjam' }}
                                </span>
                            </div>
                        </div>

                        @auth
                            <div class="space-y-2">
                                <button
                                    wire:click="toggleFavorite"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition cursor-pointer shadow-2xs"
                                >
                                    @if(auth()->user()->favorites()->where('book_id', $book->id)->exists())
                                        <svg class="h-4 w-4 text-amber-500 fill-amber-500" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                        <span>Buku Favorit Anda</span>
                                    @else
                                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
                                        <span>Tambah ke Favorit</span>
                                    @endif
                                </button>

                                @if($avail === 0)
                                    <button
                                        wire:click="reserve"
                                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-amber-700 transition cursor-pointer"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span>Reservasi Antrean Buku</span>
                                    </button>
                                @endif
                            </div>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="flex w-full items-center justify-center gap-1.5 rounded-lg border border-emerald-600/50 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-800 hover:bg-emerald-100/80 transition"
                            >
                                <span>Masuk untuk Meminjam</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                            </a>
                        @endauth
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
</div>
