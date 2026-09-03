@props(['book'])
<a href="{{ route('catalog.show', $book) }}" class="group flex flex-col overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-soft transition-all duration-200 hover:-translate-y-1 hover:border-slate-300 hover:shadow-card">
    {{-- Book Cover Container --}}
    <div class="relative flex aspect-[4/5] w-full items-center justify-center overflow-hidden bg-gradient-to-b from-slate-100 to-slate-200/70 p-4">
        @if($book->cover)
            <img src="{{ asset('storage/' . $book->cover) }}" class="h-full max-h-full w-auto object-contain rounded-xs shadow-md transition-transform duration-300 group-hover:scale-105" alt="{{ $book->title }}" loading="lazy">
        @else
            <div class="flex h-full w-full flex-col items-center justify-center rounded-xs border border-dashed border-slate-300 bg-white/60 p-3 text-center text-slate-400">
                <svg class="h-10 w-10 text-slate-300 mb-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                <span class="text-[11px] font-medium leading-tight line-clamp-2">{{ $book->title }}</span>
            </div>
        @endif

        {{-- Type badge top-right --}}
        <div class="absolute right-2.5 top-2.5">
            <x-badge color="{{ $book->type === 'ebook' ? 'blue' : 'brand' }}" size="sm">
                {{ $book->type === 'ebook' ? 'E-Book' : 'Fisik' }}
            </x-badge>
        </div>
    </div>

    {{-- Metadata Content --}}
    <div class="flex flex-1 flex-col p-4">
        @if($book->category)
            <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-700">{{ $book->category->name }}</p>
        @endif

        <h3 class="mt-1 line-clamp-2 text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition-colors leading-snug">
            {{ $book->title }}
        </h3>

        <p class="mt-1 line-clamp-1 text-xs text-slate-500">
            {{ $book->authors->pluck('name')->implode(', ') ?: 'Penulis tidak dicantumkan' }}
        </p>

        <div class="mt-auto pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            @if($book->type === 'ebook')
                <span class="inline-flex items-center gap-1 font-semibold text-emerald-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Tersedia
                </span>
            @else
                @php($availableCount = $book->availableCopies->count())
                @if($availableCount > 0)
                    <span class="inline-flex items-center gap-1 font-semibold text-emerald-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        {{ $availableCount }} tersedia
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 font-semibold text-rose-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                        Dipinjam
                    </span>
                @endif
            @endif

            <span class="font-medium text-emerald-700 group-hover:translate-x-0.5 transition-transform">
                Detail →
            </span>
        </div>
    </div>
</a>
