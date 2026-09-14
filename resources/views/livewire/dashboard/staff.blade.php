@props(['title' => 'Dashboard', 'subtitle' => null])
<div class="space-y-6">
    <x-page-header :title="$title ?? 'Dashboard Administrasi'" :subtitle="$subtitle ?? 'Ikhtisar operasional dan sirkulasi Perpustakaan MA Assadah.'">
        <x-slot:actions>
            <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
                <span>Transaksi Sirkulasi</span>
            </a>
            <a href="{{ route('books.index') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Kelola Koleksi</span>
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Top Key Metrics --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <x-stat-card
            label="Total Judul Buku"
            :value="$totalBooks"
            color="brand"
            :sub="$totalCopies . ' eksemplar fisik & digital'"
        >
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card
            label="Eksemplar Tersedia"
            :value="$availableCopies"
            color="green"
            :sub="$totalCopies . ' total terdaftar'"
        >
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card
            label="Anggota Aktif"
            :value="$totalMembers"
            color="blue"
            sub="Siswa dan Guru"
        >
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card
            label="Peminjaman Aktif"
            :value="$activeLoans"
            color="{{ $overdue > 0 ? 'red' : 'gold' }}"
            :sub="$overdue . ' terlambat · ' . $dueSoon . ' segera jatuh tempo'"
        >
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    {{-- Main Sections --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Recent Loans Table --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card heading="Peminjaman Terbaru" subheading="Transaksi sirkulasi terkini oleh siswa dan guru">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
                </x-slot:icon>
                <x-slot:actions>
                    <a href="{{ route('loans.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">
                        Lihat Semua →
                    </a>
                </x-slot:actions>

                <div class="overflow-x-auto -mx-5 -mb-5 sm:-mx-6 sm:-mb-6">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 border-y border-slate-100 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3 sm:px-6">Peminjam</th>
                                <th class="px-4 py-3">Buku & Judul</th>
                                <th class="px-4 py-3">Jatuh Tempo</th>
                                <th class="px-5 py-3 sm:px-6 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($recentLoans as $loan)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                     <td class="px-5 py-3.5 sm:px-6">
                                         @php($bName = $loan->notes ? str_replace('Peminjam: ', '', $loan->notes) : ($loan->user->name === 'Peminjam Statis (Komputer Perpustakaan)' ? 'Nama Peminjam' : $loan->user->name))
                                         <p class="font-bold text-slate-900 text-xs sm:text-sm">{{ $bName }}</p>
                                         <p class="text-[11px] text-slate-400">Peminjam</p>
                                     </td>
                                    <td class="px-4 py-3.5">
                                        <p class="font-medium text-slate-800 line-clamp-1 text-xs sm:text-sm">{{ $loan->bookCopy->book->title }}</p>
                                        <p class="font-mono text-[11px] text-slate-400">{{ $loan->bookCopy->inventory_code }}</p>
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                        {{ $loan->due_at->format('d M Y') }}
                                    </td>
                                    <td class="px-5 py-3.5 sm:px-6 text-right whitespace-nowrap">
                                        @if($loan->status === 'dikembalikan')
                                            <x-badge color="green" size="sm" dot>Dikembalikan</x-badge>
                                        @elseif($loan->isOverdue())
                                            <x-badge color="red" size="sm" dot>Terlambat ({{ $loan->daysOverdue() }}h)</x-badge>
                                        @else
                                            <x-badge color="amber" size="sm" dot>Dipinjam</x-badge>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-xs text-slate-400">
                                        Belum ada aktivitas transaksi peminjaman.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            {{-- Monthly Circulation Summary --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-soft flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Peminjaman Bulan Ini</p>
                        <p class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $monthlyLoans }}</p>
                    </div>
                    <div class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" /></svg>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-soft flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pengembalian Bulan Ini</p>
                        <p class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $monthlyReturns }}</p>
                    </div>
                    <div class="grid h-10 w-10 place-items-center rounded-xl bg-sky-50 text-sky-700 border border-sky-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" /></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Widgets --}}
        <div class="space-y-6">
            {{-- Popular Books --}}
            <x-card heading="Buku Terpopuler" subheading="Koleksi paling sering dipinjam">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
                </x-slot:icon>
                <ol class="divide-y divide-slate-100">
                    @forelse($popular as $i => $book)
                        <li class="py-2.5 first:pt-0 last:pb-0 flex items-center gap-3">
                            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-md font-bold text-xs {{ $i === 0 ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-slate-100 text-slate-600' }}">
                                {{ $i + 1 }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('catalog.show', $book) }}" class="truncate text-xs font-bold text-slate-800 hover:text-emerald-700 block transition">
                                    {{ $book->title }}
                                </a>
                                <p class="text-[11px] text-slate-400 truncate">{{ $book->borrow_count }}x dipinjam</p>
                            </div>
                        </li>
                    @empty
                        <li class="py-4 text-center text-xs text-slate-400">Belum ada statistik peminjaman.</li>
                    @endforelse
                </ol>
            </x-card>

            {{-- Announcements --}}
            <x-card heading="Pengumuman" subheading="Informasi aktif perpustakaan">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.455a28.047 28.047 0 01-2.16-6.038m3.822 1.819l3.86-7.72m-3.86 7.72c.451.04 1.054.08 1.8.08 2.08 0 3.75-1.67 3.75-3.75S13.43 8.25 11.35 8.25c-.746 0-1.349.04-1.8.08m0 7.51l3.86-7.72m-3.86 7.72H10.34" /></svg>
                </x-slot:icon>
                <div class="space-y-3">
                    @forelse($announcements as $a)
                        <div class="rounded-lg bg-slate-50 p-3 border border-slate-100">
                            <p class="text-xs font-bold text-slate-800">{{ $a->title }}</p>
                            <p class="mt-1 text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ $a->content }}</p>
                            <p class="mt-2 text-[10px] font-medium text-slate-400">{{ $a->published_at?->format('d M Y') ?? $a->created_at->format('d M Y') }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Tidak ada pengumuman baru.</p>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</div>
