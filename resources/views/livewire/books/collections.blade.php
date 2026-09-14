@props(['title' => 'Eksemplar & QR'])
<div class="space-y-6">
    <x-page-header
        title="Eksemplar & Kode QR"
        subtitle="Manajemen fisik eksemplar buku, nomor inventaris, dan cetak kode QR."
    >
        <x-slot:actions>
            <button
                type="button"
                wire:click="exportPdf"
                class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2.5 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.893-.5-1.47-1.464-1.47-2.529 0-1.637 1.258-2.973 2.868-3.08A4.475 4.475 0 0112 5.25c2.188 0 4.015 1.564 4.402 3.65 1.455.19 2.598 1.424 2.598 2.929 0 1.065-.577 2.03-1.47 2.529M12 12.75v7.5m0 0l-3-3m3 3l3-3" /></svg>
                <span>Cetak Label QR (PDF)</span>
            </button>
        </x-slot:actions>
    </x-page-header>

    {{-- Control Panel --}}
    <div class="grid grid-cols-1 gap-4 rounded-xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-soft sm:grid-cols-12">
        <div class="{{ auth()->user()->can('eksemplar.create') ? 'sm:col-span-5' : 'sm:col-span-12' }}">
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700">Filter Koleksi Buku</label>
            <select wire:model.live="book_id" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-600 focus:ring-3 focus:ring-emerald-600/15">
                <option value="">Semua Judul Buku</option>
                @foreach($books as $b)
                    <option value="{{ $b->id }}">{{ $b->title }} ({{ $b->copies->count() }} eksemplar)</option>
                @endforeach
            </select>
        </div>
        @can('eksemplar.create')
        <div class="sm:col-span-7">
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700">Cetak Tambah Eksemplar Baru</label>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <div class="flex-1 min-w-0">
                    <select wire:model="book_id" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-600 focus:ring-3 focus:ring-emerald-600/15">
                        <option value="">Pilih judul buku...</option>
                        @foreach($books as $b)
                            <option value="{{ $b->id }}">{{ $b->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <input wire:model="addCount" type="number" min="1" class="w-20 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-center outline-none focus:border-emerald-600 focus:ring-3 focus:ring-emerald-600/15" placeholder="Jml" />
                    <button
                        type="button"
                        wire:click="addCopies"
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 rounded-lg bg-emerald-700 px-4 py-2.5 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>Tambah</span>
                    </button>
                </div>
            </div>
        </div>
        @endcan
    </div>

    {{-- Copy Cards Grid --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($copies as $copy)
            <div class="rounded-xl border border-slate-200/90 bg-white p-4 shadow-soft flex flex-col justify-between">
                <div class="flex items-start gap-3.5">
                    {{-- QR Code Container --}}
                    <div class="shrink-0 rounded-lg border border-slate-200 bg-slate-50 p-1.5 shadow-2xs">
                        {!! \QrCode::size(76)->generate(route('catalog.show', $copy->book)) !!}
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-slate-900 text-xs sm:text-sm line-clamp-2 leading-snug">{{ $copy->book->title }}</p>
                        <p class="mt-1 font-mono text-[11px] text-emerald-800 font-semibold bg-emerald-50 px-1.5 py-0.5 rounded inline-block border border-emerald-200/60">
                            {{ $copy->inventory_code }}
                        </p>
                        <div class="mt-2.5 flex flex-wrap gap-1.5">
                            <x-badge color="{{ $copy->condition === 'baik' ? 'green' : 'amber' }}" size="sm">
                                Fisik: {{ ucfirst($copy->condition) }}
                            </x-badge>
                            <x-badge color="{{ $copy->status === 'tersedia' ? 'green' : ($copy->status === 'dipinjam' ? 'blue' : 'red') }}" size="sm" dot>
                                {{ ucfirst($copy->status) }}
                            </x-badge>
                        </div>
                    </div>
                </div>

                <div class="mt-3.5 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-3 text-xs">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[11px] text-slate-400">Status:</span>
                        @can('eksemplar.edit')
                        <select
                            wire:change="updateStatus({{ $copy->id }}, $event.target.value)"
                            class="rounded-md border border-slate-300 bg-white px-2 py-1 text-[11px] font-semibold text-slate-700 outline-none focus:border-emerald-600"
                        >
                            <option value="tersedia" @selected($copy->status === 'tersedia')>Tersedia</option>
                            <option value="rusak" @selected($copy->status === 'rusak')>Rusak</option>
                            <option value="hilang" @selected($copy->status === 'hilang')>Hilang</option>
                        </select>
                        @else
                            <span class="text-[11px] font-semibold text-slate-700">{{ ucfirst($copy->status) }}</span>
                        @endcan
                    </div>

                    <div class="flex items-center gap-1">
                        <a
                            href="{{ route('catalog.show', $copy->book) }}"
                            class="rounded-md px-2 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 transition"
                        >
                            Detail
                        </a>
                        @can('eksemplar.delete')
                        <button
                            type="button"
                            wire:click="delete({{ $copy->id }})"
                            wire:confirm="Hapus eksemplar ini dari inventaris?"
                            class="rounded-md px-2 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                        >
                            Hapus
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-white py-16 text-center text-xs text-slate-400">
                Belum ada eksemplar yang terdaftar.
            </div>
        @endforelse
    </div>

    @if($copies->hasPages())
        <div class="mt-4 pt-4 border-t border-slate-100">
            {{ $copies->links() }}
        </div>
    @endif
</div>
