@props(['title' => 'Kunjungan Tamu'])
<div class="space-y-6">
    <x-page-header
        title="Buku Tamu & Kunjungan Perpustakaan"
        subtitle="Kelola dan pantau presensi kehadiran pengunjung, siswa, guru, dan tamu eksternal perpustakaan."
    >
        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('visits.guest-book') }}"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 transition cursor-pointer"
                >
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 12v7.5A2.25 2.25 0 005.25 21.75h13.5A2.25 2.25 0 0021 19.5V13.5m-8.25-6.75l7.5-7.5m0 0H15m5.25 0V6" />
                    </svg>
                    <span>Layar Buku Tamu (Kiosk)</span>
                </a>

                @can('kunjungan.export')
                <button
                    type="button"
                    wire:click="exportCsv"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 bg-emerald-50 px-3.5 py-2 text-xs font-semibold text-emerald-800 shadow-xs hover:bg-emerald-100 transition cursor-pointer"
                >
                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Ekspor CSV</span>
                </button>
                @endcan

                @can('kunjungan.create')
                <button
                    type="button"
                    wire:click="create"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Catat Kunjungan</span>
                </button>
                @endcan
            </div>
        </x-slot:actions>
    </x-page-header>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="rounded-xl border border-slate-200/90 bg-white p-3.5 sm:p-4 shadow-soft">
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider truncate">Kunjungan Hari Ini</p>
            <p class="text-xl sm:text-2xl font-extrabold text-emerald-700 mt-1">{{ number_format($stats['today']) }}</p>
            <p class="text-[11px] text-slate-400 mt-1 truncate">Presensi {{ now()->format('d M Y') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/90 bg-white p-3.5 sm:p-4 shadow-soft">
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider truncate">Minggu Ini</p>
            <p class="text-xl sm:text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($stats['this_week']) }}</p>
            <p class="text-[11px] text-slate-400 mt-1 truncate">Senin - Minggu</p>
        </div>
        <div class="rounded-xl border border-slate-200/90 bg-white p-3.5 sm:p-4 shadow-soft">
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider truncate">Bulan Ini</p>
            <p class="text-xl sm:text-2xl font-extrabold text-blue-600 mt-1">{{ number_format($stats['this_month']) }}</p>
            <p class="text-[11px] text-slate-400 mt-1 truncate">{{ now()->translatedFormat('F Y') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/90 bg-white p-3.5 sm:p-4 shadow-soft">
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider truncate">Total Kunjungan</p>
            <p class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($stats['total']) }}</p>
            <p class="text-[11px] text-slate-400 mt-1 truncate">Seluruh arsip presensi</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="rounded-xl border border-slate-200/90 bg-white p-4 shadow-soft space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <x-input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama, identitas, instansi..."
                />
            </div>
            <div>
                <select
                    wire:model.live="filterType"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 transition outline-none focus:border-emerald-600"
                >
                    <option value="">Semua Kategori Pengunjung</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select
                    wire:model.live="filterPurpose"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 transition outline-none focus:border-emerald-600"
                >
                    <option value="">Semua Keperluan</option>
                    @foreach($purposes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select
                    wire:model.live="dateFilter"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 transition outline-none focus:border-emerald-600"
                >
                    <option value="all">Semua Waktu</option>
                    <option value="today">Hari Ini</option>
                    <option value="this_week">Minggu Ini</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="custom">Kustom Tanggal</option>
                </select>
            </div>
        </div>

        @if($dateFilter === 'custom')
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100 text-xs">
                <span class="text-slate-500">Dari:</span>
                <input
                    type="date"
                    wire:model.live="dateFrom"
                    class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs"
                />
                <span class="text-slate-500">Sampai:</span>
                <input
                    type="date"
                    wire:model.live="dateTo"
                    class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs"
                />
            </div>
        @endif
    </div>

    {{-- Data Table --}}
    <x-card>
        <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5 sm:px-6">Waktu</th>
                        <th class="px-4 py-3.5">Nama & Kategori</th>
                        <th class="hidden px-4 py-3.5 md:table-cell">Instansi / Kelas</th>
                        <th class="px-4 py-3.5">Keperluan</th>
                        <th class="hidden px-4 py-3.5 lg:table-cell">Kontak / Catatan</th>
                        <th class="px-5 py-3.5 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($visits as $visit)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5 sm:px-6 whitespace-nowrap">
                                <p class="text-xs font-bold text-slate-800">
                                    {{ $visit->visited_at ? $visit->visited_at->format('d/m/Y') : '-' }}
                                </p>
                                <p class="text-[11px] text-slate-400 font-mono">
                                    {{ $visit->visited_at ? $visit->visited_at->format('H:i') . ' WIB' : '-' }}
                                </p>
                            </td>
                            <td class="px-4 py-3.5">
                                <p class="font-bold text-slate-900 text-xs sm:text-sm">{{ $visit->name }}</p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <x-badge color="{{ $visit->visitor_type === 'siswa' ? 'blue' : ($visit->visitor_type === 'guru' ? 'amber' : 'green') }}" size="xs">
                                        {{ $visit->type_label }}
                                    </x-badge>
                                    @if($visit->identifier)
                                        <span class="text-[10px] text-slate-400 font-mono">#{{ $visit->identifier }}</span>
                                    @endif
                                </div>
                                @if($visit->institution)
                                    <p class="md:hidden text-[11px] text-slate-500 mt-0.5 truncate">{{ $visit->institution }}</p>
                                @endif
                            </td>
                            <td class="hidden px-4 py-3.5 md:table-cell text-xs text-slate-600">
                                {{ $visit->institution ?: '-' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800">
                                    {{ $visit->purpose_label }}
                                </span>
                            </td>
                            <td class="hidden px-4 py-3.5 lg:table-cell text-xs text-slate-500">
                                @if($visit->phone)
                                    <p class="font-mono text-[11px] text-slate-600">{{ $visit->phone }}</p>
                                @endif
                                @if($visit->notes)
                                    <p class="text-[11px] text-slate-400 truncate max-w-xs">{{ $visit->notes }}</p>
                                @endif
                                @if(!$visit->phone && !$visit->notes)
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 sm:px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @can('kunjungan.edit')
                                    <button
                                        type="button"
                                        wire:click="edit({{ $visit->id }})"
                                        class="rounded-md px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                                    >
                                        Edit
                                    </button>
                                    @endcan
                                    @can('kunjungan.delete')
                                    <button
                                        type="button"
                                        wire:click="delete({{ $visit->id }})"
                                        wire:confirm="Hapus catatan kunjungan ini?"
                                        class="rounded-md px-2.5 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-xs text-slate-400">
                                <svg class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                Tidak ada data kunjungan yang sesuai kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($visits->hasPages())
            <div class="mt-4 border-t border-slate-100 pt-4">
                {{ $visits->links() }}
            </div>
        @endif
    </x-card>

    {{-- Modal Add/Edit --}}
    <x-modal :title="$editingId ? 'Edit Catatan Kunjungan' : 'Catat Kunjungan Tamu'">
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input
                    wire:model="name"
                    label="Nama Pengunjung"
                    placeholder="Nama lengkap"
                    required
                    :error="$errors->first('name')"
                />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-select
                        wire:model="visitor_type"
                        label="Kategori Pengunjung"
                        required
                        :error="$errors->first('visitor_type')"
                    >
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <x-input
                        wire:model="identifier"
                        label="No. Identitas / NISN / NIP"
                        placeholder="Opsional"
                        :error="$errors->first('identifier')"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input
                        wire:model="institution"
                        label="Instansi / Asal / Kelas"
                        placeholder="Contoh: Kelas X A / Dinas Pendidikan"
                        :error="$errors->first('institution')"
                    />
                </div>
                <div>
                    <x-input
                        wire:model="phone"
                        label="No. Telepon / WhatsApp"
                        placeholder="Opsional"
                        :error="$errors->first('phone')"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-select
                        wire:model="purpose"
                        label="Tujuan / Keperluan"
                        required
                        :error="$errors->first('purpose')"
                    >
                        @foreach($purposes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <x-input
                        type="datetime-local"
                        wire:model="visited_at"
                        label="Waktu Kunjungan"
                        required
                        :error="$errors->first('visited_at')"
                    />
                </div>
            </div>

            <div>
                <x-textarea
                    wire:model="notes"
                    label="Catatan Tambahan"
                    placeholder="Keterangan keperluan..."
                    rows="2"
                    :error="$errors->first('notes')"
                />
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button
                    type="button"
                    @click="open = false"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="rounded-lg bg-emerald-700 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
                >
                    Simpan
                </button>
            </div>
        </form>
    </x-modal>
</div>
