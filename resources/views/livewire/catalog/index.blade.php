@props(['title' => 'Katalog Buku'])
<div class="space-y-6">
    {{-- Page Header --}}
    <x-page-header
        title="Katalog Perpustakaan"
        subtitle="Telusuri koleksi buku fisik, e-book, dan karya ilmiah Madrasah Aliyah Assadah."
    />

    {{-- Search & Filter Toolbar --}}
    <div class="rounded-xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-soft">
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-12">
            {{-- Search Bar --}}
            <div class="sm:col-span-6 relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                </div>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="Cari judul buku, nama penulis, atau nomor ISBN..."
                    class="w-full rounded-lg border border-slate-300 bg-white pl-10 pr-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-600 focus:ring-3 focus:ring-emerald-600/15 placeholder:text-slate-400"
                >
            </div>

            {{-- Category Filter --}}
            <div class="sm:col-span-3">
                <select
                    wire:model.live="category"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-600 focus:ring-3 focus:ring-emerald-600/15"
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
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-600 focus:ring-3 focus:ring-emerald-600/15"
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
                    class="ml-auto inline-flex items-center gap-1 font-semibold text-rose-600 hover:text-rose-700 transition cursor-pointer"
                >
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    Reset Filter
                </button>
            </div>
        @endif
    </div>

    {{-- Books Grid --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5">
        @forelse($books as $book)
            <x-book-card :book="$book" />
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-full bg-slate-100 text-slate-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tidak ada buku ditemukan</h3>
                <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">Coba sesuaikan kata kunci pencarian atau reset filter untuk melihat koleksi lainnya.</p>
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
