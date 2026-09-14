@props(['title' => 'Dashboard'])
<div class="space-y-6">
    <x-page-header
        :title="'Selamat Datang, ' . auth()->user()->name"
        subtitle="Aktivitas peminjaman dan riwayat bacaan perpustakaan Anda."
    >
        <x-slot:actions>
            <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                <span>Cari Buku Baru</span>
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Member Quick Metrics --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <x-stat-card
            label="Total Pernah Dipinjam"
            :value="$totalBorrowed"
            color="brand"
            sub="Riwayat transaksi"
        >
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card
            label="Sedang Dipinjam"
            :value="$activeLoans->count()"
            color="{{ $activeLoans->where(fn($l) => $l->isOverdue())->count() > 0 ? 'red' : 'gold' }}"
            :sub="$activeLoans->where(fn($l) => $l->isOverdue())->count() > 0 ? 'Ada yang terlambat!' : 'Buku aktif'"
        >
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card
            label="Koleksi Favorit"
            :value="$totalFavorite"
            color="amber"
            sub="Disimpan di profil"
        >
            <x-slot:icon>
                <svg class="h-5 w-5 text-amber-600 fill-amber-600" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card
            label="Buku Rekomendasi"
            :value="$popular->count()"
            color="blue"
            sub="Koleksi pilihan"
        >
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" /></svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    {{-- Main Sections --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            {{-- Active Loans Box --}}
            <x-card heading="Buku yang Sedang Anda Pinjam" subheading="Harap kembalikan buku tepat waktu sebelum tanggal jatuh tempo">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                </x-slot:icon>

                <div class="space-y-3">
                    @forelse($activeLoans as $loan)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-lg border border-slate-200/80 bg-slate-50/60 p-4 transition hover:bg-slate-50">
                            <div class="min-w-0">
                                <a href="{{ route('catalog.show', $loan->bookCopy->book) }}" class="font-bold text-slate-900 hover:text-emerald-700 text-sm block truncate">
                                    {{ $loan->bookCopy->book->title }}
                                </a>
                                <p class="mt-0.5 text-xs text-slate-500 font-mono">Kode Eksemplar: {{ $loan->bookCopy->inventory_code }}</p>
                            </div>
                            <div class="sm:text-right shrink-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Jatuh Tempo</p>
                                <p class="text-sm font-bold {{ $loan->isOverdue() ? 'text-rose-600' : 'text-slate-800' }}">
                                    {{ $loan->due_at->format('d M Y') }}
                                </p>
                                @if($loan->isOverdue())
                                    <span class="mt-1 inline-flex items-center gap-1 rounded bg-rose-100 px-2 py-0.5 text-[11px] font-bold text-rose-700">
                                        Terlambat {{ $loan->daysOverdue() }} hari
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-slate-200 p-8 text-center">
                            <svg class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                            <p class="text-xs font-semibold text-slate-700">Tidak ada peminjaman buku yang sedang aktif</p>
                            <p class="mt-1 text-[11px] text-slate-400">Jelajahi koleksi perpustakaan dan temukan buku menarik untuk dipinjam.</p>
                            <a href="{{ route('catalog.index') }}" class="mt-3 inline-block text-xs font-semibold text-emerald-700 hover:text-emerald-800">
                                Lihat Katalog Buku →
                            </a>
                        </div>
                    @endforelse
                </div>
            </x-card>

            {{-- Favorite Books Grid --}}
            <x-card heading="Koleksi Buku Favorit Anda" subheading="Buku yang Anda simpan untuk dibaca nanti">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-amber-500 fill-amber-500" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                </x-slot:icon>

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                    @forelse($favorites as $fav)
                        <x-book-card :book="$fav->book" />
                    @empty
                        <div class="col-span-full py-8 text-center text-xs text-slate-400">
                            Belum ada buku favorit. Klik tombol bintang pada detail buku untuk menambahkannya ke sini.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>

        {{-- Right Column Widgets --}}
        <div class="space-y-6">
            {{-- Announcements --}}
            <x-card heading="Pengumuman Perpustakaan" subheading="Informasi kegiatan dan jadwal">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.455a28.047 28.047 0 01-2.16-6.038m3.822 1.819l3.86-7.72m-3.86 7.72c.451.04 1.054.08 1.8.08 2.08 0 3.75-1.67 3.75-3.75S13.43 8.25 11.35 8.25c-.746 0-1.349.04-1.8.08m0 7.51l3.86-7.72m-3.86 7.72H10.34" /></svg>
                </x-slot:icon>

                <div class="space-y-3">
                    @forelse($announcements as $a)
                        <div class="rounded-lg bg-slate-50 p-3.5 border border-slate-100">
                            <p class="text-xs font-bold text-slate-800">{{ $a->title }}</p>
                            <p class="mt-1 text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ $a->content }}</p>
                            <p class="mt-2 text-[10px] font-medium text-slate-400">{{ $a->published_at?->format('d M Y') ?? $a->created_at->format('d M Y') }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Tidak ada pengumuman.</p>
                    @endforelse
                </div>
            </x-card>

            {{-- Recommendations --}}
            <x-card heading="Rekomendasi Pilihan" subheading="Buku yang sering dibaca">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" /></svg>
                </x-slot:icon>

                <div class="grid grid-cols-2 gap-3">
                    @foreach($popular->take(4) as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>
            </x-card>
        </div>
    </div>
</div>
