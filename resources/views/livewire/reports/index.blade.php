@props(['title' => 'Laporan'])
<div class="space-y-6">
    <x-page-header
        title="Laporan & Rekap Statistik"
        subtitle="Analisis data peminjaman, perputaran koleksi, dan ekspor dokumen perpustakaan."
    />

    {{-- Metrics Grid --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Total Judul Buku" :value="$stats['totalBooks']" color="brand">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card label="Total Eksemplar" :value="$stats['totalCopies']" color="blue" :sub="$stats['available'] . ' tersedia'">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" /></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card label="Total Anggota" :value="$stats['members']" color="green" :sub="$stats['students'] . ' siswa · ' . $stats['teachers'] . ' guru'">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card label="Total Transaksi" :value="$stats['totalLoans']" color="gold" :sub="$stats['activeLoans'] . ' aktif · ' . $stats['returned'] . ' kembali'">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-2">
        <x-stat-card label="Transaksi Terlambat" :value="$stats['overdue']" color="red" sub="Perlu tindak lanjut pengembalian">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card label="Akumulasi Denda" :value="'Rp ' . number_format($stats['fines'], 0, ',', '.')" color="amber" sub="Denda keterlambatan">
            <x-slot:icon>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    {{-- Main Export & Ranking --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <x-card heading="Ekspor Laporan Peminjaman" subheading="Saring periode tanggal dan format berkas yang diinginkan">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                </x-slot:icon>

                <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-3">
                    <x-input wire:model="from" label="Dari Tanggal" type="date" />
                    <x-input wire:model="to" label="Sampai Tanggal" type="date" />
                    <x-select wire:model="status" label="Filter Status">
                        <option value="">Semua Status</option>
                        <option value="dipinjam">Dipinjam</option>
                        <option value="dikembalikan">Dikembalikan</option>
                        <option value="terlambat">Terlambat</option>
                    </x-select>
                </div>

                @can('laporan.export')
                <div class="mt-5 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-4">
                    <button
                        type="button"
                        wire:click="exportPdf"
                        class="inline-flex items-center gap-2 rounded-lg bg-rose-700 px-4 py-2.5 text-xs font-semibold text-white shadow-xs hover:bg-rose-800 transition cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        <span>Unduh Dokumen PDF</span>
                    </button>
                    <button
                        type="button"
                        wire:click="exportExcel"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2.5 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M1.125 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h19.5" /></svg>
                        <span>Unduh Berkas Excel</span>
                    </button>
                </div>
                @endcan
            </x-card>
        </div>

        {{-- Popular Ranking --}}
        <div>
            <x-card heading="Buku Paling Populer" subheading="Frekuensi sirkulasi peminjaman tertinggi">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
                </x-slot:icon>

                <ol class="divide-y divide-slate-100">
                    @foreach($stats['popular'] as $i => $book)
                        <li class="py-2.5 first:pt-0 last:pb-0 flex items-center gap-3">
                            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-md font-bold text-xs {{ $i === 0 ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-slate-100 text-slate-600' }}">
                                {{ $i + 1 }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('catalog.show', $book) }}" class="truncate text-xs font-bold text-slate-800 hover:text-emerald-700 block transition">
                                    {{ $book->title }}
                                </a>
                                <p class="text-[11px] text-slate-400 truncate">{{ $book->borrow_count }}x peminjaman</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </x-card>
        </div>
    </div>
</div>
