@props(['title' => 'Peminjaman'])
<div class="space-y-6">
    <x-page-header
        title="Layanan Sirkulasi"
        subtitle="Peminjaman, pengembalian, dan perpanjangan koleksi buku perpustakaan."
    />

    {{-- Tabs Navigation --}}
    <div class="flex items-center gap-2 border-b border-slate-200/80 pb-3">
        @foreach(['list' => 'Daftar Peminjaman', 'checkout' => 'Peminjaman Baru', 'return' => 'Pengembalian Buku'] as $key => $label)
            @continue($key === 'checkout' && auth()->user()->cannot('peminjaman.create'))
            @continue($key === 'return' && auth()->user()->cannot('peminjaman.edit'))
            <button
                type="button"
                wire:click="setTab('{{ $key }}')"
                class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs sm:text-sm font-semibold transition duration-150 cursor-pointer {{ $tab === $key ? 'bg-emerald-700 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-300/80 hover:bg-slate-50' }}"
            >
                @if($key === 'list')
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.007 5.25H3.75v.008h.007V12zm0 5.25H3.75v.008h.007v-.008z" /></svg>
                @elseif($key === 'checkout')
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                @else
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
                @endif
                <span>{{ $label }}</span>
            </button>
        @endforeach
    </div>

    {{-- TAB 1: CHECKOUT --}}
    @if($tab === 'checkout' && auth()->user()->can('peminjaman.create'))
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Step 1: Member Selection --}}
            <x-card heading="1. Pilih Anggota Peminjam" subheading="Cari berdasarkan nama lengkap atau email">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                </x-slot:icon>

                <x-input
                    wire:model.live.debounce.250ms="memberQuery"
                    placeholder="Ketik nama atau email anggota..."
                />

                @if($selectedMember)
                    @php($m = App\Models\User::find($selectedMember))
                    @if($m)
                        <div class="mt-3.5 flex items-center justify-between rounded-lg border border-emerald-200 bg-emerald-50/60 p-3.5">
                            <div class="flex items-center gap-3">
                                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-emerald-700 text-xs font-bold text-white uppercase">
                                    {{ str($m->name)->substr(0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 text-sm truncate">{{ $m->name }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ $m->roles->first()->name ?? 'Anggota' }} · {{ $m->email }}</p>
                                </div>
                            </div>
                            <button
                                type="button"
                                wire:click="$set('selectedMember', null)"
                                class="text-xs font-semibold text-rose-600 hover:text-rose-700 transition cursor-pointer"
                            >
                                Ganti
                            </button>
                        </div>
                    @endif
                @else
                    @if($memberQuery)
                        <div class="mt-3 max-h-56 overflow-y-auto space-y-2 divide-y divide-slate-100 rounded-lg border border-slate-200 p-2">
                            @forelse($members as $m)
                                <button
                                    type="button"
                                    wire:click="pickMember({{ $m->id }})"
                                    class="w-full rounded-md p-2.5 text-left hover:bg-slate-50 transition flex items-center justify-between gap-3 cursor-pointer"
                                >
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 text-xs sm:text-sm truncate">{{ $m->name }}</p>
                                        <p class="text-[11px] text-slate-500 truncate">{{ $m->roles->first()->name ?? 'Anggota' }} · {{ $m->email }}</p>
                                    </div>
                                    <span class="text-xs font-semibold text-emerald-700 shrink-0">Pilih →</span>
                                </button>
                            @empty
                                <p class="py-4 text-center text-xs text-slate-400">Tidak ada anggota yang cocok dengan pencarian.</p>
                            @endforelse
                        </div>
                    @else
                        <p class="mt-3 text-xs text-slate-400">Silakan masukkan nama siswa atau guru pada kotak pencarian di atas.</p>
                    @endif
                @endif
            </x-card>

            {{-- Step 2: Book Copy Selection & Finalize --}}
            <x-card heading="2. Eksemplar Buku & Durasi" subheading="Scan QR / ketik nomor inventaris eksemplar">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" /></svg>
                </x-slot:icon>

                <div class="space-y-3">
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <x-input wire:model.live="copyCode" placeholder="Kode inventaris (contoh: INV-...)" />
                        </div>
                        <button
                            type="button"
                            wire:click="scanCopy"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition cursor-pointer shrink-0"
                        >
                            Cari Buku
                        </button>
                    </div>

                    @if($selectedCopy)
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50/60 p-3.5">
                            <p class="font-bold text-slate-900 text-sm">{{ $selectedCopy->book->title }}</p>
                            <p class="font-mono text-xs text-emerald-800 mt-0.5">Kode: {{ $selectedCopy->inventory_code }}</p>
                            <div class="mt-2 flex items-center gap-2 text-xs">
                                <x-badge color="brand" size="sm">Eksemplar Siap Pinjam</x-badge>
                                <span class="text-slate-500">Rak: {{ $selectedCopy->book->shelf?->code ?? '-' }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="border-t border-slate-100 pt-3">
                        <x-input
                            wire:model="dueDays"
                            label="Durasi Peminjaman (Hari)"
                            type="number"
                            min="1"
                            hint="Durasi standar default mengikuti kebijakan perpustakaan."
                        />
                    </div>

                    <button
                        type="button"
                        wire:click="checkout"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        Konfirmasi Peminjaman Buku
                    </button>
                </div>
            </x-card>
        </div>

    {{-- TAB 2: RETURN --}}
    @elseif($tab === 'return' && auth()->user()->can('peminjaman.edit'))
        <div class="mx-auto max-w-xl">
            <x-card heading="Proses Pengembalian Buku" subheading="Scan QR pada buku atau masukkan kode inventaris">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
                </x-slot:icon>

                <div class="space-y-4">
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <x-input
                                wire:model.live="returnCode"
                                placeholder="Scan QR atau ketik kode inventaris..."
                            />
                        </div>
                        <button
                            type="button"
                            wire:click="scanReturn"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition cursor-pointer shrink-0"
                        >
                            Cek Status
                        </button>
                    </div>

                    @if($returnInfo)
                        <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4 sm:p-5 space-y-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Data Peminjam</p>
                                    <p class="font-bold text-slate-900 text-sm mt-0.5">{{ $returnInfo->user->name }}</p>
                                    <p class="text-xs text-slate-500 mt-1 font-medium">{{ $returnInfo->bookCopy->book->title }}</p>
                                    <p class="font-mono text-xs text-slate-400">{{ $returnInfo->bookCopy->inventory_code }}</p>
                                </div>
                                <x-badge color="{{ $returnInfo->isOverdue() ? 'red' : 'green' }}" size="md" dot>
                                    {{ $returnInfo->isOverdue() ? 'Terlambat ' . $returnInfo->daysOverdue() . ' Hari' : 'Tepat Waktu' }}
                                </x-badge>
                            </div>

                            <div class="grid grid-cols-2 gap-3 border-t border-slate-200/80 pt-3 text-xs">
                                <div>
                                    <p class="text-slate-400">Tanggal Pinjam</p>
                                    <p class="font-semibold text-slate-800 mt-0.5">{{ $returnInfo->borrowed_at->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Batas Pengembalian</p>
                                    <p class="font-semibold text-slate-800 mt-0.5">{{ $returnInfo->due_at->format('d M Y') }}</p>
                                </div>
                            </div>

                            @if($fine > 0)
                                <div class="rounded-lg bg-amber-50 border border-amber-200/80 p-3 text-xs">
                                    <div class="flex items-center justify-between font-bold text-amber-900">
                                        <span>Denda Keterlambatan:</span>
                                        <span class="text-sm">Rp {{ number_format($fine, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endif

                            <button
                                type="button"
                                wire:click="processReturn"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Konfirmasi Pengembalian Buku
                            </button>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>

    {{-- TAB 3: LIST --}}
    @else
        <x-card heading="Daftar Riwayat & Status Sirkulasi" subheading="Seluruh data peminjaman buku aktif dan riwayat pengembalian">
            <x-slot:icon>
                <svg class="h-4.5 w-4.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.007 5.25H3.75v.008h.007V12zm0 5.25H3.75v.008h.007v-.008z" /></svg>
            </x-slot:icon>

            <div class="mb-4 max-w-md">
                <x-input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama anggota, judul buku, atau kode..."
                />
            </div>

            <div class="overflow-x-auto -mx-5 -mb-5 sm:-mx-6 sm:-mb-6">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-y border-slate-100 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3 sm:px-6">Anggota</th>
                            <th class="px-4 py-3">Buku Dipinjam</th>
                            <th class="px-4 py-3">Tgl Pinjam</th>
                            <th class="px-4 py-3">Jatuh Tempo</th>
                            <th class="px-4 py-3">Denda</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-5 py-3 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($loans as $loan)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3.5 sm:px-6">
                                    <p class="font-bold text-slate-900 text-xs sm:text-sm">{{ $loan->user->name }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $loan->user->email }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="font-medium text-slate-800 line-clamp-1 text-xs sm:text-sm">{{ $loan->bookCopy->book->title }}</p>
                                    <p class="font-mono text-[11px] text-slate-400">{{ $loan->bookCopy->inventory_code }}</p>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                    {{ $loan->borrowed_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                    {{ $loan->due_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs font-semibold {{ $loan->fine_amount > 0 ? 'text-amber-700' : 'text-slate-400' }}">
                                    {{ $loan->fine_amount > 0 ? 'Rp ' . number_format($loan->fine_amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if($loan->status === 'dikembalikan')
                                        <x-badge color="green" size="sm" dot>Kembali</x-badge>
                                    @elseif($loan->isOverdue())
                                        <x-badge color="red" size="sm" dot>Terlambat ({{ $loan->daysOverdue() }}h)</x-badge>
                                    @else
                                        <x-badge color="amber" size="sm" dot>Dipinjam</x-badge>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 sm:px-6 text-right whitespace-nowrap">
                                    @if($loan->status !== 'dikembalikan' && !$loan->isOverdue())
                                        @can('peminjaman.edit')
                                        <button
                                            type="button"
                                            wire:click="extendLoan({{ $loan->id }})"
                                            wire:confirm="Perpanjang masa pinjam buku ini selama 7 hari?"
                                            class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 border border-emerald-200/80 hover:bg-emerald-100 transition cursor-pointer"
                                        >
                                            Perpanjang
                                        </button>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-xs text-slate-400">
                                    Belum ada transaksi peminjaman tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($loans->hasPages())
                <div class="mt-4 pt-4 border-t border-slate-100">
                    {{ $loans->links() }}
                </div>
            @endif
        </x-card>
    @endif
</div>
