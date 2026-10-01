@props(['title' => 'Buku Digital SIBI & Kemenag'])
<div class="space-y-6">
    {{-- Header Halaman --}}
    <div class="rounded-xl border border-slate-200/90 bg-white p-5 sm:p-6 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="space-y-1 max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Koleksi Digital Terbuka</p>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                    Buku Digital SIBI & Kemenag Madrasah
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    Koleksi resmi Kemendikdasmen RI dan Kemenag RI untuk Kurikulum Merdeka, KMA 183, dan Pengayaan.
                </p>
            </div>

            @can('buku.create')
                <div class="shrink-0 bg-emerald-50 border border-emerald-200/80 p-3 rounded-lg text-xs space-y-1 max-w-xs text-emerald-950">
                    <div class="font-bold text-emerald-900">Akses Pustakawan</div>
                    <p class="text-emerald-800 text-[11px]">Klik "Impor ke Koleksi" untuk menyimpan buku ke katalog perpustakaan sekolah.</p>
                </div>
            @endcan
        </div>
    </div>

    {{-- Filter & Tab Navigasi --}}
    <div class="rounded-xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-soft space-y-4">
        {{-- Kategori Kurikulum --}}
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-100 pb-4">
            <button
                wire:click="$set('curriculum', 'merdeka')"
                class="px-4 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-2 {{ $curriculum === 'merdeka' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                <span class="w-2 h-2 rounded-full {{ $curriculum === 'merdeka' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                Kurikulum Merdeka (Teks)
            </button>
            <button
                wire:click="$set('curriculum', 'kemenag')"
                class="px-4 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-2 {{ $curriculum === 'kemenag' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                <span class="w-2 h-2 rounded-full {{ $curriculum === 'kemenag' ? 'bg-white' : 'bg-teal-500' }}"></span>
                Madrasah Kemenag RI (KMA 183)
            </button>
            <button
                wire:click="$set('curriculum', 'k13')"
                class="px-4 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-2 {{ $curriculum === 'k13' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                <span class="w-2 h-2 rounded-full {{ $curriculum === 'k13' ? 'bg-white' : 'bg-blue-500' }}"></span>
                Kurikulum 2013 (K-13)
            </button>
            <button
                wire:click="$set('curriculum', 'nonteks')"
                class="px-4 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-2 {{ $curriculum === 'nonteks' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                <span class="w-2 h-2 rounded-full {{ $curriculum === 'nonteks' ? 'bg-white' : 'bg-amber-500' }}"></span>
                Buku Pengayaan (Non-Teks)
            </button>
            <button
                wire:click="$set('curriculum', 'btp')"
                class="px-4 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-2 {{ $curriculum === 'btp' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                <span class="w-2 h-2 rounded-full {{ $curriculum === 'btp' ? 'bg-white' : 'bg-purple-500' }}"></span>
                Katalog BTP Kemendikdasmen
            </button>
        </div>

        {{-- Search bar & Jenjang & Kelas --}}
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-12">
            <div class="sm:col-span-6 relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input
                    wire:model.live.debounce.400ms="search"
                    type="search"
                    placeholder="Cari buku SMA/MA (Matematika, Biologi, Kimia, Sejarah, PAI)..."
                    class="w-full rounded-lg border border-slate-300 bg-white pl-10 pr-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-600 focus:ring-3 focus:ring-emerald-600/15 placeholder:text-slate-400"
                >
            </div>

            <div class="sm:col-span-3">
                <select
                    wire:model.live="level"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-600 focus:ring-3 focus:ring-emerald-600/15 font-medium"
                >
                    <option value="SMA">Jenjang SMA / MA (Khusus)</option>
                    <option value="SMK">Jenjang SMK / MAK</option>
                    <option value="ALL">Semua Jenjang (SD s/d SMA)</option>
                </select>
            </div>

            <div class="sm:col-span-3">
                <select
                    wire:model.live="selectedClass"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none transition focus:border-emerald-600 focus:ring-3 focus:ring-emerald-600/15"
                >
                    <option value="">Semua Tingkat Kelas</option>
                    <option value="10">Kelas 10 / X (Fase E)</option>
                    <option value="11">Kelas 11 / XI (Fase F)</option>
                    <option value="12">Kelas 12 / XII (Fase F)</option>
                </select>
            </div>
        </div>

        {{-- Status Ringkasan & Loading Spinner --}}
        <div class="flex items-center justify-between pt-1 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span>Ditemukan <strong>{{ number_format($total) }}</strong> buku SMA/MA</span>
                @if($search)
                    <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-2 py-0.5 text-slate-700">
                        Kata kunci: "<strong>{{ $search }}</strong>"
                    </span>
                @endif
                @if($level && $level !== 'ALL')
                    <span class="inline-flex items-center gap-1 rounded bg-blue-50 px-2 py-0.5 text-blue-700 font-medium">
                        Tingkat: {{ $level }}
                    </span>
                @endif
                @if($selectedClass)
                    <span class="inline-flex items-center gap-1 rounded bg-emerald-50 px-2 py-0.5 text-emerald-700 font-medium">
                        Kelas: {{ $selectedClass }}
                    </span>
                @endif
            </div>

            <div wire:loading class="flex items-center gap-1.5 text-emerald-600 font-medium">
                <svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Mengambil data dari SIBI...
            </div>
        </div>
    </div>

    {{-- Alert Error jika API gagal --}}
    @if($error)
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="font-semibold">Pemberitahuan API Kemendikdasmen</p>
                    <p class="text-xs text-rose-700 mt-0.5">{{ $error }}</p>
                </div>
            </div>
            <button
                type="button"
                wire:click="refreshCatalog"
                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-600 text-white hover:bg-rose-700 active:bg-rose-800 transition"
            >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                Coba Lagi
            </button>
        </div>
    @endif

    {{-- Grid Buku --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($books as $b)
            @php
                $isImported = $this->isAlreadyImported($b['isbn'] ?? null, $b['title'] ?? '');
            @endphp
            <div class="flex flex-col rounded-xl border border-slate-200/90 bg-white p-4 shadow-soft hover:shadow-md transition group">
                {{-- Cover & Badges --}}
                <div class="relative aspect-[3/4] w-full overflow-hidden rounded-lg bg-slate-100 flex items-center justify-center mb-3">
                    @if(!empty($b['cover']))
                        <img
                            src="{{ $b['cover'] }}"
                            alt="{{ $b['title'] }}"
                            loading="lazy"
                            class="h-full w-full object-contain p-1 transition duration-300 group-hover:scale-105"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >
                        <div class="hidden h-full w-full items-center justify-center bg-slate-100 text-slate-400 text-xs text-center p-4">
                            Cover Tidak Tersedia
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center p-4 text-center text-slate-400">
                            <svg class="h-12 w-12 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                            <span class="text-xs">Buku SIBI</span>
                        </div>
                    @endif

                    {{-- Badges Pojok --}}
                    <div class="absolute top-2 left-2 flex flex-col gap-1">
                        @if(!empty($b['level']))
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-600 text-white shadow-xs">
                                {{ $b['level'] }}
                            </span>
                        @endif
                        @if(!empty($b['class']))
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-600 text-white shadow-xs">
                                {{ $b['class'] }}
                            </span>
                        @endif
                    </div>

                    @if($isImported)
                        <div class="absolute top-2 right-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Tersimpan
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Detail Info --}}
                <div class="flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm leading-snug line-clamp-2 group-hover:text-emerald-700 transition" title="{{ $b['title'] }}">
                            {{ $b['title'] }}
                        </h3>

                        <p class="text-xs text-slate-500 mt-1 line-clamp-1">
                            <span class="font-medium text-slate-600">Penulis:</span> {{ $b['writer'] ?: 'Tim Pusbuk Kemendikdasmen' }}
                        </p>

                        @if(!empty($b['isbn']))
                            <p class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                ISBN: {{ $b['isbn'] }}
                            </p>
                        @endif
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="pt-2 border-t border-slate-100 space-y-2">
                        <div class="grid grid-cols-2 gap-2">
                            @if(!empty($b['attachment']))
                                <button
                                    type="button"
                                    wire:click="openReader('{{ $b['attachment'] }}', '{{ addslashes($b['title']) }}')"
                                    class="w-full inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 transition"
                                >
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Baca PDF
                                </button>
                                <a
                                    href="{{ $b['attachment'] }}"
                                    target="_blank"
                                    download
                                    class="w-full inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    Unduh
                                </a>
                            @else
                                <span class="col-span-2 text-center text-[11px] text-slate-400 italic py-1">
                                    Format cetak resmi
                                </span>
                            @endif
                        </div>

                        {{-- Tombol Impor Admin / Pustakawan --}}
                        @can('buku.create')
                            <div>
                                @if($isImported)
                                    <button
                                        type="button"
                                        disabled
                                        class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 text-slate-400 cursor-not-allowed"
                                    >
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Sudah di Katalog Lokal
                                    </button>
                                @else
                                    <button
                                        type="button"
                                        wire:click="import({{ json_encode($b) }})"
                                        wire:loading.attr="disabled"
                                        class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 active:bg-emerald-800 shadow-xs transition"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Impor ke Koleksi
                                    </button>
                                @endif
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-slate-100 text-slate-400 mb-3">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <h4 class="text-sm font-semibold text-slate-800">Tidak ada buku ditemukan</h4>
                <p class="mt-1 text-xs text-slate-500">Coba kata kunci lain atau ubah filter kurikulum.</p>
            </div>
        @endforelse
    </div>

    {{-- Navigasi Halaman (Pagination) --}}
    @if($totalPages > 1)
        <div class="flex items-center justify-between rounded-xl border border-slate-200/90 bg-white px-4 py-3 shadow-soft">
            <div class="text-xs text-slate-500">
                Halaman <strong>{{ $page }}</strong> dari <strong>{{ $totalPages }}</strong>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    wire:click="previousPage"
                    @disabled($page <= 1)
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition"
                >
                    &larr; Sebelumnya
                </button>
                <button
                    type="button"
                    wire:click="nextPage({{ $totalPages }})"
                    @disabled($page >= $totalPages)
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition"
                >
                    Selanjutnya &rarr;
                </button>
            </div>
        </div>
    @endif

    {{-- Modal Reader PDF --}}
    @if($showReaderModal && $activePdfUrl)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 bg-slate-900/80 backdrop-blur-xs">
            <div class="relative w-full max-w-5xl h-[92vh] flex flex-col bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200">
                {{-- Reader Header --}}
                <div class="flex items-center justify-between px-5 py-3.5 bg-slate-900 text-white shrink-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="p-1.5 rounded-lg bg-emerald-600 text-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                        </span>
                        <div class="truncate">
                            <p class="text-xs text-emerald-400 font-semibold uppercase tracking-wider">E-Book Reader Resmi SIBI / Kemenag</p>
                            <h3 class="text-sm font-bold truncate text-white" title="{{ $activeBookTitle }}">
                                {{ $activeBookTitle }}
                            </h3>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a
                            href="{{ $activePdfUrl }}"
                            target="_blank"
                            class="px-2.5 py-1 rounded text-xs font-semibold bg-white/10 hover:bg-white/20 text-slate-200 transition"
                        >
                            Buka di Tab Baru
                        </a>
                        <button
                            type="button"
                            wire:click="closeReader"
                            class="grid h-8 w-8 place-items-center rounded-lg text-slate-400 hover:bg-white/10 hover:text-white transition"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Embedded PDF Viewer --}}
                <div class="flex-1 bg-slate-800 relative">
                    <iframe
                        src="{{ $activePdfUrl }}#toolbar=1"
                        class="w-full h-full border-0"
                        title="{{ $activeBookTitle }}"
                    ></iframe>
                </div>
            </div>
        </div>
    @endif
</div>
