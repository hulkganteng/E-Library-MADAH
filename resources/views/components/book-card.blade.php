@props(['book'])
<a href="{{ route('catalog.show', $book) }}" class="group flex flex-col overflow-hidden border border-stone-950/10 bg-[#fffdf8] hover:border-emerald-800/35 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">
    {{-- Book Cover Container --}}
    <div class="relative flex aspect-[4/5] w-full items-center justify-center overflow-hidden bg-[#eee9df] p-4">
        @if($book->cover)
            <img src="{{ asset('storage/' . $book->cover) }}" class="h-full max-h-full w-auto object-contain shadow-card" alt="{{ $book->title }}" loading="lazy">
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

        <h3 class="mt-1 line-clamp-2 text-base font-semibold text-stone-900 group-hover:text-emerald-800">
            {{ $book->title }}
        </h3>

        <p class="mt-1 line-clamp-1 text-sm text-stone-500">
            {{ $book->authors->pluck('name')->implode(', ') ?: 'Penulis tidak dicantumkan' }}
        </p>

        <div class="mt-auto flex items-center justify-between border-t border-stone-950/8 pt-3 text-sm">
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
