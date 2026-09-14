@props(['title' => 'Peminjaman'])
<div class="space-y-6" x-data="{
    showScanner: false,
    scanTarget: '',
    html5QrCode: null,
    scanError: '',
    openScanner(target) {
        this.scanTarget = target;
        this.scanError = '';
        this.showScanner = true;
        $nextTick(() => {
            if (!this.html5QrCode) {
                this.html5QrCode = new Html5Qrcode('camera-reader');
            }
            this.html5QrCode.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 220, height: 220 } },
                (decodedText) => {
                    if (this.scanTarget === 'checkout') {
                        $wire.set('copyCode', decodedText);
                        $wire.scanCopy();
                    } else if (this.scanTarget === 'return') {
                        $wire.set('returnCode', decodedText);
                        $wire.scanReturn();
                    }
                    this.closeScanner();
                },
                (errorMessage) => {}
            ).catch(err => {
                this.scanError = 'Gagal mengakses kamera. Pastikan izin kamera telah diberikan.';
            });
        });
    },
    closeScanner() {
        if (this.html5QrCode && this.html5QrCode.isScanning) {
            this.html5QrCode.stop().then(() => {
                this.showScanner = false;
            }).catch(() => {
                this.showScanner = false;
            });
        } else {
            this.showScanner = false;
        }
    }
}">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <x-page-header
        title="Layanan Sirkulasi"
        subtitle="Peminjaman, pengembalian, dan perpanjangan koleksi buku perpustakaan."
    />

    {{-- Tabs Navigation --}}
    <div class="flex items-center gap-2 border-b border-slate-200/80 pb-3 overflow-x-auto no-scrollbar sm:flex-wrap">
        @foreach(['list' => 'Daftar Peminjaman', 'checkout' => 'Peminjaman Baru', 'return' => 'Pengembalian Buku'] as $key => $label)
            @continue($key === 'checkout' && auth()->check() && auth()->user()->cannot('peminjaman.create'))
            @continue($key === 'return' && auth()->check() && auth()->user()->cannot('peminjaman.edit'))
            <button
                type="button"
                wire:click="setTab('{{ $key }}')"
                class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs sm:text-sm font-semibold transition duration-150 cursor-pointer whitespace-nowrap {{ $tab === $key ? 'bg-emerald-700 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-300/80 hover:bg-slate-50' }}"
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
    @if($tab === 'checkout' && (!auth()->check() || auth()->user()->can('peminjaman.create')))
        <div class="mx-auto max-w-xl">
            <x-card heading="Peminjaman Buku" subheading="Scan QR / ketik kode inventaris eksemplar buku">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </x-slot:icon>

                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row gap-2">
                        <div class="flex-1 min-w-0">
                            <x-input wire:model.live="copyCode" placeholder="Scan QR / ketik kode (contoh: ELIB-0001-01)..." />
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button
                                type="button"
                                @click="openScanner('checkout')"
                                class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 rounded-lg bg-slate-900 px-3.5 py-2.5 text-xs font-semibold text-white shadow-xs hover:bg-slate-800 transition cursor-pointer"
                            >
                                <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" /></svg>
                                <span>Scan Kamera</span>
                            </button>
                            <button
                                type="button"
                                wire:click="scanCopy"
                                class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition cursor-pointer"
                            >
                                Cari Buku
                            </button>
                        </div>
                    </div>
                            Cari Buku
                        </button>
                    </div>

                    @if($selectedCopy)
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50/60 p-3.5 space-y-1">
                            <p class="font-bold text-slate-900 text-sm">{{ $selectedCopy->book->title }}</p>
                            <p class="font-mono text-xs text-emerald-800">Kode: {{ $selectedCopy->inventory_code }}</p>
                            <div class="pt-1 flex items-center gap-2 text-xs">
                                <x-badge color="green" size="sm">Eksemplar Siap Pinjam</x-badge>
                                <span class="text-slate-500">Rak: {{ $selectedCopy->book->shelf?->code ?? '-' }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="border-t border-slate-100 pt-3 space-y-3">
                        <x-input wire:model="borrowerName" label="Nama Peminjam *" placeholder="Contoh: Ahmad Subagyo" />
                        @error('borrowerName') <p class="text-xs text-rose-600 -mt-2">{{ $message }}</p> @enderror
                        <x-input wire:model="borrowerClass" label="Kelas *" placeholder="Contoh: XII IPA 1" />
                        @error('borrowerClass') <p class="text-xs text-rose-600 -mt-2">{{ $message }}</p> @enderror
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
    @elseif($tab === 'return' && (!auth()->check() || auth()->user()->can('peminjaman.edit')))
        <div class="mx-auto max-w-xl">
            <x-card heading="Proses Pengembalian Buku" subheading="Scan QR pada buku atau masukkan kode inventaris">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
                </x-slot:icon>

                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row gap-2">
                        <div class="flex-1 min-w-0">
                            <x-input
                                wire:model.live="returnCode"
                                placeholder="Scan QR atau ketik kode inventaris..."
                            />
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button
                                type="button"
                                @click="openScanner('return')"
                                class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 rounded-lg bg-slate-900 px-3.5 py-2.5 text-xs font-semibold text-white shadow-xs hover:bg-slate-800 transition cursor-pointer"
                            >
                                <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" /></svg>
                                <span>Scan Kamera</span>
                            </button>
                            <button
                                type="button"
                                wire:click="scanReturn"
                                class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition cursor-pointer"
                            >
                                Cek Status
                            </button>
                        </div>
                    </div>
                            Cek Status
                        </button>
                    </div>

                    @if($returnInfo)
                        <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4 sm:p-5 space-y-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Data Peminjam</p>
                                    <p class="font-bold text-slate-900 text-sm mt-0.5">{{ $returnInfo->borrower_name ?? $returnInfo->user->name }}</p>
                                    @if($returnInfo->borrower_class)<p class="text-xs text-emerald-700 font-semibold">Kelas: {{ $returnInfo->borrower_class }}</p>@endif
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

            @if($pendingCount > 0)
                <div class="mb-4 rounded-lg bg-amber-50 border border-amber-200/80 p-3 flex items-center justify-between gap-3 text-xs text-amber-900">
                    <div class="flex items-center gap-2 font-semibold">
                        <svg class="h-4 w-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                        <span>Ada <strong>{{ $pendingCount }}</strong> pengajuan peminjaman baru dari katalog yang menunggu persetujuan petugas.</span>
                    </div>
                </div>
            @endif

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
                            <th class="px-4 py-3 sm:px-6">Anggota</th>
                            <th class="px-4 py-3">Buku Dipinjam</th>
                            <th class="hidden sm:table-cell px-4 py-3">Tgl Pinjam</th>
                            <th class="px-4 py-3">Jatuh Tempo</th>
                            <th class="hidden md:table-cell px-4 py-3">Denda</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($loans as $loan)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-4 py-3.5 sm:px-6">
                                    @php($bName = $loan->borrower_name ?? ($loan->notes ? str_replace('Peminjam: ', '', $loan->notes) : ($loan->user->name === 'Peminjam Statis (Komputer Perpustakaan)' ? 'Nama Peminjam' : $loan->user->name)))
                                    <p class="font-bold text-slate-900 text-xs sm:text-sm">{{ $bName }}</p>
                                    <p class="text-[11px] {{ $loan->borrower_class ? 'text-emerald-600 font-semibold' : 'text-slate-400' }}">{{ $loan->borrower_class ?? 'Peminjam' }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="font-medium text-slate-800 line-clamp-1 text-xs sm:text-sm">{{ $loan->bookCopy->book->title }}</p>
                                    <p class="font-mono text-[11px] text-slate-400">{{ $loan->bookCopy->inventory_code }}</p>
                                </td>
                                <td class="hidden sm:table-cell px-4 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                    {{ $loan->borrowed_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                    <p class="font-medium">{{ $loan->due_at->format('d/m/Y') }}</p>
                                    @if($loan->fine_amount > 0)
                                        <p class="md:hidden text-[10px] text-rose-600 font-semibold mt-0.5">Denda: Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</p>
                                    @endif
                                </td>
                                <td class="hidden md:table-cell px-4 py-3.5 whitespace-nowrap text-xs font-semibold {{ $loan->fine_amount > 0 ? 'text-amber-700' : 'text-slate-400' }}">
                                    {{ $loan->fine_amount > 0 ? 'Rp ' . number_format($loan->fine_amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if($loan->status === 'menunggu')
                                        <x-badge color="amber" size="sm" dot>Menunggu Persetujuan</x-badge>
                                    @elseif($loan->status === 'ditolak')
                                        <x-badge color="red" size="sm">Ditolak</x-badge>
                                    @elseif($loan->status === 'dikembalikan')
                                        <x-badge color="green" size="sm" dot>Kembali</x-badge>
                                    @elseif($loan->isOverdue())
                                        <x-badge color="red" size="sm" dot>Terlambat ({{ $loan->daysOverdue() }}h)</x-badge>
                                    @else
                                        <x-badge color="blue" size="sm" dot>Dipinjam</x-badge>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 sm:px-6 text-right whitespace-nowrap space-x-1">
                                    @if($loan->status === 'menunggu')
                                        @if(!auth()->check() || auth()->user()->can('peminjaman.edit'))
                                            <button
                                                type="button"
                                                wire:click="approveLoan({{ $loan->id }})"
                                                wire:confirm="Setujui pengajuan peminjaman buku ini?"
                                                class="inline-flex items-center gap-1 rounded-md bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-emerald-700 transition cursor-pointer shadow-2xs"
                                            >
                                                Setujui
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="rejectLoan({{ $loan->id }})"
                                                wire:confirm="Tolak pengajuan peminjaman ini?"
                                                class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 border border-rose-200 hover:bg-rose-100 transition cursor-pointer"
                                            >
                                                Tolak
                                            </button>
                                        @endif
                                    @elseif($loan->status === 'dipinjam' && !$loan->isOverdue())
                                        @if(!auth()->check() || auth()->user()->can('peminjaman.edit'))
                                        <button
                                            type="button"
                                            wire:click="extendLoan({{ $loan->id }})"
                                            wire:confirm="Perpanjang masa pinjam buku ini selama 7 hari?"
                                            class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 border border-emerald-200/80 hover:bg-emerald-100 transition cursor-pointer"
                                        >
                                            Perpanjang
                                        </button>
                                        @endif
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

    {{-- Scanner Modal --}}
    <div
        x-show="showScanner"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs"
    >
        <div class="w-full max-w-md bg-white rounded-xl shadow-xl overflow-hidden p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" /></svg>
                    <span>Scan QR Buku Kamera</span>
                </h3>
                <button type="button" @click="closeScanner()" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
            </div>

            <div id="camera-reader" class="overflow-hidden rounded-lg border border-slate-200 bg-slate-900 min-h-[240px]"></div>

            <template x-if="scanError">
                <p class="text-xs font-semibold text-rose-600 text-center" x-text="scanError"></p>
            </template>

            <p class="text-xs text-center text-slate-500">Arahkan kamera HP / Webcam ke QR Code pada stiker buku.</p>

            <button
                type="button"
                @click="closeScanner()"
                class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition cursor-pointer"
            >
                Tutup Kamera
            </button>
        </div>
    </div>
</div>
