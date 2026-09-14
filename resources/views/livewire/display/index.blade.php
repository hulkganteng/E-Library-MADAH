<div class="min-h-screen bg-slate-100/75 text-slate-800 font-sans" x-data="{ detailOpen: false }">
    {{-- Header + Search --}}
    @php($kioskLogo = \App\Models\Setting::get('logo'))
    @php($kioskSchool = \App\Models\Setting::get('school_name', 'Madrasah Aliyah Assadah'))
    @php($kioskLib = \App\Models\Setting::get('library_name', 'Katalog Kiosk Perpustakaan'))

    <header class="sticky top-0 z-20 border-b border-slate-200/90 bg-white/95 px-4 py-4 backdrop-blur-md sm:px-10 sm:py-5 shadow-2xs">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-wrap items-center justify-between gap-3 sm:gap-4">
                <div class="flex items-center gap-3 sm:gap-3.5 min-w-0">
                    <div class="grid h-10 w-10 sm:h-12 sm:w-12 shrink-0 place-items-center rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-serif font-bold text-lg sm:text-xl shadow-2xs overflow-hidden p-1">
                        @if($kioskLogo)
                            <img src="{{ asset('storage/' . $kioskLogo) }}" class="h-full w-full object-contain" alt="Logo">
                        @else
                            <div class="grid h-full w-full place-items-center rounded-lg bg-emerald-700 text-white font-serif font-bold text-lg sm:text-xl">
                                م
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-base sm:text-2xl font-extrabold tracking-tight text-slate-900 font-serif truncate">{{ $kioskLib ?: 'Katalog Kiosk Perpustakaan' }}</h1>
                        <p class="text-[11px] sm:text-xs text-slate-500 font-medium truncate">{{ $kioskSchool ?: 'Madrasah Aliyah Assadah' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        x-data="{
                            time: '',
                            updateTime() {
                                const now = new Date();
                                this.time = now.toLocaleDateString('id-ID', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' }) + ' • ' + now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
                            }
                        }"
                        x-init="updateTime(); setInterval(() => updateTime(), 1000)"
                        class="hidden sm:flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 px-3.5 py-1 text-xs font-mono font-semibold text-emerald-900 shadow-2xs"
                    >
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span x-text="time"></span>
                    </div>
                    <span class="rounded-full bg-slate-100 border border-slate-200/80 px-3 py-1 sm:px-4 sm:py-1.5 text-[11px] sm:text-xs font-semibold text-slate-700">
                        {{ $books->total() }} Judul
                    </span>
                </div>
            </div>

            {{-- Search & Format Filter Bar --}}
            <div class="mt-5 flex flex-wrap gap-3">
                <div class="relative w-full sm:max-w-xl">
                    <svg class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    <input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari judul buku, nama pengarang, atau nomor ISBN..."
                        class="w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 py-3 text-sm sm:text-base text-slate-800 placeholder-slate-400 shadow-2xs outline-none transition focus:border-emerald-600 focus:ring-3 focus:ring-emerald-600/15"
                    >
                </div>

                <button
                    type="button"
                    wire:click="$set('type', type === 'fisik' ? '' : 'fisik')"
                    class="inline-flex items-center gap-1.5 rounded-xl border px-4 py-3 text-xs sm:text-sm font-semibold transition cursor-pointer {{ $type === 'fisik' ? 'border-emerald-700 bg-emerald-700 text-white shadow-xs' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50 shadow-2xs' }}"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                    <span>Buku Fisik</span>
                </button>

                <button
                    type="button"
                    wire:click="$set('type', type === 'ebook' ? '' : 'ebook')"
                    class="inline-flex items-center gap-1.5 rounded-xl border px-4 py-3 text-xs sm:text-sm font-semibold transition cursor-pointer {{ $type === 'ebook' ? 'border-emerald-700 bg-emerald-700 text-white shadow-xs' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50 shadow-2xs' }}"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>
                    <span>E-Book Digital</span>
                </button>

                @if($search || $type || $category)
                    <button
                        type="button"
                        wire:click="clear"
                        class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs sm:text-sm font-semibold text-rose-700 hover:bg-rose-100 transition cursor-pointer"
                    >
                        Reset Filter
                    </button>
                @endif
            </div>

            {{-- Categories Filter Pills --}}
            <div class="mt-3.5 flex overflow-x-auto pb-2 sm:pb-0 sm:flex-wrap gap-1.5 no-scrollbar">
                <button
                    type="button"
                    wire:click="$set('category', null)"
                    class="rounded-full px-3.5 py-1 text-xs font-semibold transition cursor-pointer whitespace-nowrap {{ $category === null ? 'bg-emerald-700 text-white shadow-2xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}"
                >
                    Semua Kategori
                </button>
                @foreach($categories as $c)
                    <button
                        type="button"
                        wire:click="$set('category', {{ $c->id }})"
                        class="rounded-full px-3.5 py-1 text-xs font-semibold transition cursor-pointer whitespace-nowrap {{ $category === $c->id ? 'bg-emerald-700 text-white shadow-2xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}"
                    >
                        {{ $c->name }}
                    </button>
                @endforeach
            </div>
        </div>
    </header>

    {{-- Book Grid --}}
    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-10 sm:py-8">
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
            @forelse($books as $book)
                <button
                    type="button"
                    wire:click="openDetail({{ $book->id }})"
                    @click="detailOpen = true"
                    class="group flex flex-col overflow-hidden rounded-xl bg-white text-left border border-slate-200/90 shadow-soft transition duration-200 hover:-translate-y-1 hover:border-emerald-600 hover:shadow-card cursor-pointer"
                >
                    <div class="relative flex aspect-[4/5] w-full items-center justify-center overflow-hidden bg-slate-100 p-2.5">
                        @if($book->cover)
                            <img src="{{ asset('storage/' . $book->cover) }}" class="h-full w-full object-cover rounded-sm shadow-md" alt="{{ $book->title }}">
                        @else
                            <div class="flex flex-col items-center justify-center p-3 text-center">
                                <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                                <span class="mt-1 text-[10px] text-slate-500 font-serif font-semibold line-clamp-2">{{ $book->title }}</span>
                            </div>
                        @endif
                        <span class="absolute right-2 top-2 rounded px-2 py-0.5 text-[10px] font-bold shadow-2xs {{ $book->type === 'ebook' ? 'bg-sky-600 text-white' : 'bg-emerald-700 text-white' }}">
                            {{ $book->type === 'ebook' ? 'E-Book' : 'Fisik' }}
                        </span>
                    </div>

                    <div class="flex flex-1 flex-col p-3">
                        <h4 class="line-clamp-2 text-xs sm:text-sm font-bold leading-snug text-slate-900 group-hover:text-emerald-700 transition">{{ $book->title }}</h4>
                        <p class="mt-1 line-clamp-1 text-[11px] text-slate-500">{{ $book->authors->pluck('name')->implode(', ') ?: 'MA Assadah' }}</p>
                        <p class="mt-auto pt-2.5 text-[11px] font-semibold flex items-center gap-1.5 {{ $book->availableCopies->count() > 0 || $book->type === 'ebook' ? 'text-emerald-700' : 'text-rose-600' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $book->availableCopies->count() > 0 || $book->type === 'ebook' ? 'bg-emerald-600' : 'bg-rose-500' }}"></span>
                            <span>{{ $book->type === 'ebook' ? 'Tersedia Digital' : ($book->availableCopies->count() > 0 ? $book->availableCopies->count() . ' Tersedia' : 'Sedang Dipinjam') }}</span>
                        </p>
                    </div>
                </button>
            @empty
                <div class="col-span-full py-20 text-center text-sm text-slate-400">
                    Tidak ditemukan buku yang cocok dengan kata kunci pencarian.
                </div>
            @endforelse
        </div>

        @if($books->hasPages())
            <div class="mt-8">
                {{ $books->links() }}
            </div>
        @endif
    </main>

    {{-- Detail Modal Overlay --}}
    @if($selected)
        <div
            x-show="detailOpen"
            x-cloak
            @keydown.escape.window="detailOpen = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs"
        >
            <div
                class="flex max-h-[90vh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl bg-white text-slate-800 shadow-2xl border border-slate-200/90"
                @click.outside="detailOpen = false"
            >
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Informasi Detail Koleksi</p>
                    <button
                        type="button"
                        @click="detailOpen = false; $wire.closeDetail()"
                        class="grid h-8 w-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-200 hover:text-slate-700 transition cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="grid flex-1 gap-6 overflow-y-auto p-6 sm:grid-cols-3">
                    <div class="flex items-center justify-center rounded-xl bg-slate-100 p-4 border border-slate-200/80">
                        @if($selected->cover)
                            <img src="{{ asset('storage/' . $selected->cover) }}" class="max-h-64 object-contain rounded shadow" alt="{{ $selected->title }}">
                        @else
                            <div class="text-center py-8 text-slate-400">
                                <svg class="mx-auto h-12 w-12 mb-2 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                                <span class="text-xs font-serif">Sampul Tidak Tersedia</span>
                            </div>
                        @endif
                    </div>

                    <div class="sm:col-span-2 space-y-4">
                        <div>
                            <h2 class="text-xl font-extrabold tracking-tight text-slate-900 font-serif leading-snug">{{ $selected->title }}</h2>
                            <p class="mt-1 text-xs text-slate-500">Penulis: {{ $selected->authors->pluck('name')->implode(', ') ?: '-' }}</p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <x-badge color="{{ $selected->type === 'ebook' ? 'blue' : 'brand' }}" size="sm">
                                {{ $selected->type === 'ebook' ? 'E-Book Digital' : 'Buku Fisik' }}
                            </x-badge>
                            @if($selected->is_repository)
                                <x-badge color="amber" size="sm">Karya Civitas MA Assadah</x-badge>
                            @endif
                            <x-badge color="{{ $selected->availableCopies->count() > 0 || $selected->type === 'ebook' ? 'green' : 'red' }}" size="sm" dot>
                                {{ $selected->type === 'ebook' ? 'Tersedia Digital' : $selected->availableCopies->count() . ' dari ' . $selected->copies->count() . ' eksemplar tersedia' }}
                            </x-badge>
                        </div>

                        <dl class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-4 sm:grid-cols-3 text-xs">
                            <div><dt class="font-semibold text-slate-400 uppercase text-[10px]">Kategori</dt><dd class="mt-0.5 text-slate-800 font-medium">{{ $selected->category?->name ?? '-' }}</dd></div>
                            <div><dt class="font-semibold text-slate-400 uppercase text-[10px]">ISBN</dt><dd class="mt-0.5 text-slate-800 font-mono">{{ $selected->isbn ?: '-' }}</dd></div>
                            <div><dt class="font-semibold text-slate-400 uppercase text-[10px]">Penerbit</dt><dd class="mt-0.5 text-slate-800 font-medium">{{ $selected->publisher?->name ?? '-' }}</dd></div>
                            <div><dt class="font-semibold text-slate-400 uppercase text-[10px]">Tahun Terbit</dt><dd class="mt-0.5 text-slate-800 font-medium">{{ $selected->publish_year ?: '-' }}</dd></div>
                            <div><dt class="font-semibold text-slate-400 uppercase text-[10px]">Edisi / Cetakan</dt><dd class="mt-0.5 text-slate-800 font-medium">{{ $selected->edition ?: '-' }}</dd></div>
                            <div><dt class="font-semibold text-slate-400 uppercase text-[10px]">Lokasi Rak</dt><dd class="mt-0.5 text-emerald-800 font-bold">{{ $selected->shelf?->code ?? '-' }}</dd></div>
                        </dl>

                        @if($selected->description)
                            <div class="border-t border-slate-100 pt-3">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Sinopsis / Ringkasan</h4>
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $selected->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
