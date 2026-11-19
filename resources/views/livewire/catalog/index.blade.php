@props(['title' => 'Katalog Buku'])
<div class="space-y-6">
    {{-- Page Header --}}
    <x-page-header
        title="Katalog Perpustakaan"
        subtitle="Telusuri koleksi buku fisik, e-book, dan karya ilmiah Madrasah Aliyah Assadah."
    />

    {{-- Search & Filter Toolbar --}}
    <div class="border-y border-stone-950/10 py-4 sm:py-5">
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-12">
            {{-- Search Bar --}}
            <div class="sm:col-span-6 relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                </div>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    aria-label="Cari judul buku, nama penulis, atau nomor ISBN"
                    placeholder="Cari judul buku, nama penulis, atau nomor ISBN..."
                    class="w-full rounded-md border border-stone-950/15 bg-[#fffdf8] py-2.5 pr-4 pl-10 text-base text-stone-800 outline-none focus:border-emerald-700 sm:text-sm"
                >
            </div>

            {{-- Category Filter --}}
            <div class="sm:col-span-3">
                <select
                    wire:model.live="category"
                    aria-label="Filter kategori buku"
                    class="w-full rounded-md border border-stone-950/15 bg-[#fffdf8] px-3 py-2.5 text-base text-stone-800 outline-none focus:border-emerald-700 sm:text-sm"
                >
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Type Filter --}}
            <div class="sm:col-span-3">
                <select
                    wire:model.live="type"
                    aria-label="Filter format koleksi"
                    class="w-full rounded-md border border-stone-950/15 bg-[#fffdf8] px-3 py-2.5 text-base text-stone-800 outline-none focus:border-emerald-700 sm:text-sm"
                >
                    <option value="">Semua Format</option>
                    <option value="fisik">Buku Fisik</option>
                    <option value="ebook">E-Book Digital</option>
                </select>
            </div>
        </div>

        {{-- Active Filters indicator & Reset --}}
        @if($search || $category || $type)
            <div class="mt-3.5 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3 text-xs">
                <span class="text-slate-500 font-medium">Filter aktif:</span>
                @if($search)
                    <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-slate-700">
                        Pencarian: "<strong>{{ $search }}</strong>"
                    </span>
                @endif
                @if($category)
                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-emerald-800 border border-emerald-200/60">
                        Kategori: <strong>{{ $categories->firstWhere('id', $category)?->name }}</strong>
                    </span>
                @endif
                @if($type)
                    <span class="inline-flex items-center gap-1 rounded-md bg-sky-50 px-2 py-0.5 text-sky-800 border border-sky-200/60">
                        Format: <strong>{{ $type === 'ebook' ? 'E-Book' : 'Fisik' }}</strong>
                    </span>
                @endif

                <button
                    wire:click="clear"
                    class="ml-auto inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md font-semibold text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition cursor-pointer"
                >
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    <span>Reset Filter</span>
                </button>
            </div>
        @endif
    </div>

    {{-- Books Grid --}}
    <div class="grid grid-cols-2 gap-x-3 gap-y-6 sm:grid-cols-3 sm:gap-x-5 md:grid-cols-4 xl:grid-cols-5">
        @forelse($books as $book)
            <x-book-card :book="$book" />
        @empty
            <div class="col-span-full border-y border-stone-950/10 py-16 text-center">
                <h3 class="text-base font-semibold text-stone-800">Tidak ada buku ditemukan</h3>
                <p class="mx-auto mt-1 max-w-sm text-base text-stone-500 sm:text-sm">Coba sesuaikan kata kunci pencarian atau reset filter untuk melihat koleksi lainnya.</p>
                @if($search || $category || $type)
                    <button
                        wire:click="clear"
                        class="mt-4 inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs"
                    >
                        Tampilkan Semua Buku
                    </button>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($books->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $books->links() }}
        </div>
    @endif
</div>
