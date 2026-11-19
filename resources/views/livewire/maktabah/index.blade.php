<div class="space-y-6">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&family=Scheherazade+New:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .font-arabic { font-family: 'Amiri', 'Scheherazade New', 'Traditional Arabic', serif; }
        .font-arabic-heading { font-family: 'Scheherazade New', 'Amiri', serif; }
    </style>

    <header class="flex flex-col gap-5 border-b border-stone-950/10 pb-6 lg:flex-row lg:items-end lg:justify-between">
        <div class="max-w-3xl">
            <p class="font-arabic text-lg text-emerald-800" dir="rtl">المكتبة الرقمية</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight text-stone-950 sm:text-3xl">Maktabah Digital</h1>
            <p class="mt-2 max-w-[68ch] text-base text-stone-600">Khazanah kitab klasik dan kontemporer, pencarian ibarot, serta pembaca kitab digital.</p>
        </div>
        <dl class="flex gap-8 border-l border-stone-950/10 pl-5 text-sm">
            <div>
                <dt class="text-stone-500">Koleksi</dt>
                <dd class="mt-1 font-semibold tabular-nums text-stone-900">7.700+ kitab</dd>
            </div>
            <div>
                <dt class="text-stone-500">Keilmuan</dt>
                <dd class="mt-1 font-semibold tabular-nums text-stone-900">40 fan</dd>
            </div>
        </dl>
    </header>

    {{-- Filter, Search & Tab Navigasi --}}
    <div class="space-y-4 border-b border-stone-950/10 pb-5">
        {{-- Mode Switcher: Katalog vs Cari Ibarot --}}
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="flex gap-1 border-b border-stone-950/10">
                <button
                    wire:click="setMode('catalog')"
                    class="flex cursor-pointer items-center gap-2 border-b-2 px-3 py-2 text-sm font-medium {{ $mode === 'catalog' ? 'border-emerald-800 text-emerald-800' : 'border-transparent text-stone-500 hover:text-stone-900' }}"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    Katalog Kitab
                </button>
                <button
                    wire:click="setMode('ibarot')"
                    class="flex cursor-pointer items-center gap-2 border-b-2 px-3 py-2 text-sm font-medium {{ $mode === 'ibarot' ? 'border-emerald-800 text-emerald-800' : 'border-transparent text-stone-500 hover:text-stone-900' }}"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    Pencarian Lafadz / Ibarot
                </button>
            </div>

            <div class="text-xs text-slate-500 font-medium flex items-center gap-2">
                @if($mode === 'catalog')
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Menampilkan <strong class="text-slate-800">{{ number_format($total) }}</strong> kitab
                @else
                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                    Pencarian teks lintas jutaan halaman kitab
                @endif
            </div>
        </div>

        @if($mode === 'catalog')
            {{-- Search & Kategori Fan --}}
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-12">
                <div class="sm:col-span-8 relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input
                        wire:model.live.debounce.350ms="search"
                        type="search"
                        placeholder="Cari judul kitab atau nama pengarang (Arab / Latin)..."
                        aria-label="Cari judul kitab atau nama pengarang"
                        class="w-full rounded-md border border-stone-950/15 bg-[#fffdf8] py-2.5 pr-10 pl-10 text-base text-stone-800 outline-none focus:border-emerald-700 sm:text-sm"
                    >
                    @if($search)
                        <button
                            wire:click="$set('search', '')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>

                <div class="sm:col-span-4">
                    <select
                        wire:model.live="selectedCategory"
                        name="maktabah_category"
                        aria-label="Filter kategori fan"
                        class="w-full cursor-pointer rounded-md border border-stone-950/15 bg-[#fffdf8] px-3 py-2.5 text-base text-stone-800 outline-none focus:border-emerald-700 sm:text-sm"
                    >
                        <option value="">Semua Kategori Fan (40 Kategori)</option>
                        @foreach($categories as $catId => $catName)
                            <option value="{{ $catId }}">{{ $catName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Kategori Cepat Populer --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar">
                <span class="text-slate-400 shrink-0 font-medium text-[11px] mr-1">Fan Populer:</span>
                <button
                    wire:click="setCategory(null)"
                    class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer {{ is_null($selectedCategory) ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    Semua Fan
                </button>
                @php
                    $popularFanIds = [1, 2, 3, 4, 5, 8, 12, 19, 21];
                @endphp
                @foreach($popularFanIds as $pfId)
                    @if(isset($categories[$pfId]))
                        <button
                            wire:click="setCategory({{ $pfId }})"
                            class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-arabic transition cursor-pointer {{ $selectedCategory == $pfId ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
                        >
                            {{ $categories[$pfId] }}
                        </button>
                    @endif
                @endforeach
            </div>
        @else
            {{-- Form Pencarian Ibarot --}}
            <form wire:submit.prevent="searchIbarot" class="space-y-3">
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                        <input
                            wire:model.defer="ibarotQuery"
                            type="search"
                            dir="auto"
                            placeholder="Ketik potongan lafadz atau ibarot yang dicari (cth: Ø§Ù„Ù†ÙŠØ© Ø´Ø±Ø· ÙÙŠ ØµØ­Ø© Ø§Ù„ÙˆØ¶ÙˆØ¡)..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-2.5 text-sm text-slate-800 outline-none transition focus:bg-white focus:border-emerald-600 focus:ring-3 focus:ring-emerald-600/15 font-arabic text-base"
                        >
                    </div>

                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2 cursor-pointer shadow-xs shrink-0"
                    >
                        <span wire:loading.remove wire:target="searchIbarot" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                            Cari Ibarot
                        </span>
                        <span wire:loading wire:target="searchIbarot" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            Mencari Lafadz...
                        </span>
                    </button>
                </div>

                {{-- Contoh Kata Kunci --}}
                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                    <span class="text-[11px] text-slate-400">Contoh pencarian:</span>
                    <button type="button" wire:click="$set('ibarotQuery', 'Ù†ÙŠØ© Ø§Ù„ØµÙ„Ø§Ø©')" class="font-arabic px-2 py-0.5 rounded bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 transition cursor-pointer">Ù†ÙŠØ© Ø§Ù„ØµÙ„Ø§Ø©</button>
                    <button type="button" wire:click="$set('ibarotQuery', 'Ø´Ø±ÙˆØ· Ø§Ù„ÙˆØ¶ÙˆØ¡')" class="font-arabic px-2 py-0.5 rounded bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 transition cursor-pointer">Ø´Ø±ÙˆØ· Ø§Ù„ÙˆØ¶ÙˆØ¡</button>
                    <button type="button" wire:click="$set('ibarotQuery', 'Ø£Ø±ÙƒØ§Ù† Ø§Ù„Ø¥ÙŠÙ…Ø§Ù†')" class="font-arabic px-2 py-0.5 rounded bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 transition cursor-pointer">Ø£Ø±ÙƒØ§Ù† Ø§Ù„Ø¥ÙŠÙ…Ø§Ù†</button>
                    <button type="button" wire:click="$set('ibarotQuery', 'Ø£Ø­ÙƒØ§Ù… Ø§Ù„Ù…ÙŠØª')" class="font-arabic px-2 py-0.5 rounded bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 transition cursor-pointer">Ø£Ø­ÙƒØ§Ù… Ø§Ù„Ù…ÙŠØª</button>
                </div>
            </form>
        @endif
    </div>

    {{-- Content Area --}}
    @if($mode === 'catalog')
        @if(empty($books))
            <div class="rounded-2xl border border-slate-200/90 bg-white p-12 text-center shadow-xs">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <h3 class="mt-4 text-base font-semibold text-slate-900">Kitab Tidak Ditemukan</h3>
                <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                    Tidak ada kitab yang sesuai dengan pencarian "{{ $search }}". Coba gunakan kata kunci Arab atau pilih kategori fan lainnya.
                </p>
                @if($search || $selectedCategory)
                    <button
                        wire:click="$set('search', ''); $set('selectedCategory', null);"
                        class="mt-4 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition cursor-pointer"
                    >
                        Reset Filter
                    </button>
                @endif
            </div>
        @else
            {{-- Grid Kitab --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($books as $item)
                    @php
                        $isImported = $this->isAlreadyImported($item['title'] ?? '');
                    @endphp
                    <div class="group flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md relative overflow-hidden">
                        {{-- Top Accent Line --}}
                        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <div class="space-y-3">
                            {{-- Category Pill & Status Badge --}}
                            <div class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70 font-arabic">
                                    {{ $item['category'] ?? 'Ø¹Ø§Ù…' }}
                                </span>

                                @if($isImported)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100/80 text-emerald-800">
                                        <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Tersimpan
                                    </span>
                                @endif
                            </div>

                            {{-- Judul Kitab Arab --}}
                            <div dir="rtl" class="text-right space-y-1.5 pt-1">
                                <h3 class="font-arabic font-bold text-lg text-slate-900 group-hover:text-emerald-700 transition leading-relaxed line-clamp-2">
                                    {{ $item['title'] }}
                                </h3>
                                <p class="font-arabic text-xs text-slate-500 flex items-center justify-end gap-1.5 line-clamp-1">
                                    <span>{{ $item['author'] ?? 'Ù…Ø¤Ù„Ù ØºÙŠØ± Ù…Ø¹Ø±ÙˆÙ' }}</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                    </svg>
                                </p>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center justify-between gap-2">
                            <button
                                wire:click="openReader({{ $item['id'] }}, 1)"
                                class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1.5 cursor-pointer shadow-xs"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                                <span>Buka Kitab</span>
                            </button>

                            @can('buku.create')
                                @if(! $isImported)
                                    <button
                                        wire:click="importBook({{ json_encode($item) }})"
                                        title="Impor ke Koleksi Perpustakaan"
                                        class="p-2 border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 rounded-xl transition cursor-pointer shrink-0"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </button>
                                @endif
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($totalPages > 1)
                <div class="flex flex-col sm:flex-row items-center justify-between border-t border-slate-200/90 pt-5 gap-3 text-xs">
                    <span class="text-slate-500 font-medium">
                        Halaman <strong class="text-slate-900">{{ $page }}</strong> dari <strong class="text-slate-900">{{ $totalPages }}</strong> (Total {{ number_format($total) }} kitab)
                    </span>

                    <div class="flex items-center gap-2">
                        <button
                            wire:click="$set('page', 1)"
                            @disabled($page <= 1)
                            class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition"
                            title="Halaman Pertama"
                        >
                            &laquo;
                        </button>
                        <button
                            wire:click="$set('page', {{ max(1, $page - 1) }})"
                            @disabled($page <= 1)
                            class="px-3.5 py-1.5 rounded-lg border border-slate-200 bg-white font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition"
                        >
                            Sebelumnya
                        </button>

                        <span class="px-2 font-semibold text-slate-700">{{ $page }}</span>

                        <button
                            wire:click="$set('page', {{ min($totalPages, $page + 1) }})"
                            @disabled($page >= $totalPages)
                            class="px-3.5 py-1.5 rounded-lg border border-slate-200 bg-white font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition"
                        >
                            Selanjutnya
                        </button>
                        <button
                            wire:click="$set('page', {{ $totalPages }})"
                            @disabled($page >= $totalPages)
                            class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition"
                            title="Halaman Terakhir"
                        >
                            &raquo;
                        </button>
                    </div>
                </div>
            @endif
        @endif

    @else
        {{-- Mode Ibarot: Hasil Pencarian Lafadz --}}
        @if($hasSearchedIbarot)
            @if(!empty($ibarotError))
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 text-rose-800 text-xs flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <span>Terjadi kesalahan saat mencari ibarot: {{ $ibarotError }}</span>
                </div>
            @elseif(empty($ibarotResults))
                <div class="rounded-2xl border border-slate-200/90 bg-white p-12 text-center shadow-xs">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-slate-900">Lafadz Tidak Ditemukan</h3>
                    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                        Tidak ada potongan ibarot yang cocok dengan kata kunci "{{ $ibarotQuery }}". Coba kurangi harakat atau gunakan potongan kata yang lebih umum.
                    </p>
                </div>
            @else
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-700 px-1">
                        <span>Ditemukan {{ count($ibarotResults) }} kutipan ibarot untuk "{{ $ibarotQuery }}":</span>
                    </div>

                    @foreach($ibarotResults as $res)
                        <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs space-y-3.5 hover:border-emerald-300 transition">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span class="font-arabic font-bold text-slate-900 text-base">
                                        {{ $res['book_name'] ?? 'Kitab' }}
                                    </span>
                                    <span class="text-xs text-slate-300">â€¢</span>
                                    <span class="font-arabic text-xs text-slate-600">
                                        {{ $res['author_name'] ?? '' }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60 font-arabic">
                                        {{ $res['category'] ?? '' }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700">
                                        Hal. {{ $res['page'] ?? 1 }}
                                    </span>
                                </div>
                            </div>

                            {{-- Isi Cuplikan Teks Ibarot --}}
                            <div dir="rtl" class="text-right font-arabic text-base sm:text-lg text-slate-900 leading-[2.2] bg-amber-50/40 p-4 rounded-xl border border-amber-200/60 select-text">
                                {!! nl2br(e($res['text'] ?? '')) !!}
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-1">
                                <button
                                    wire:click="openReader({{ $res['book_id'] }}, {{ $res['page'] ?? 1 }})"
                                    class="py-1.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold transition flex items-center gap-1.5 cursor-pointer shadow-xs"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                    </svg>
                                    Buka Kitab Pada Halaman Ini
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-xs">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-base font-semibold text-slate-900">Pencarian Teks & Ibarot Lintas Seluruh Kitab</h3>
                <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                    Ketik teks Arab baik berharakat maupun gundul. Sistem akan memindai jutaan halaman kitab untuk menemukan referensi kutipan secara instan.
                </p>
            </div>
        @endif
    @endif

    {{-- Pembaca kitab: bentang RTL dengan animasi lembar --}}
    @if($showReaderModal)
        @php($readerTotalPages = (int) ($readerBookMeta['total_pages'] ?? $readerCurrentPage))
        <div
            x-data="{
                showToc: false,
                theme: 'parchment',
                searchHeading: '',
                turning: false,
                direction: 'next',
                touchStartX: null,
                turn(direction) {
                    if (this.turning) return;
                    if (direction === 'previous' && {{ $readerCurrentPage }} <= 1) return;
                    if (direction === 'next' && {{ $readerCurrentPage }} >= {{ $readerTotalPages }}) return;

                    this.direction = direction;
                    this.turning = true;

                    window.setTimeout(async () => {
                        if (direction === 'next') {
                            await this.$wire.nextPage();
                        } else {
                            await this.$wire.prevPage();
                        }
                        window.setTimeout(() => this.turning = false, 240);
                    }, 260);
                },
                startSwipe(event) {
                    this.touchStartX = event.changedTouches[0].clientX;
                },
                finishSwipe(event) {
                    if (this.touchStartX === null) return;
                    const distance = event.changedTouches[0].clientX - this.touchStartX;
                    this.touchStartX = null;
                    if (Math.abs(distance) < 55) return;
                    this.turn(distance < 0 ? 'next' : 'previous');
                }
            }"
            :data-theme="theme"
            @keydown.window.arrow-left.prevent="turn('next')"
            @keydown.window.arrow-right.prevent="turn('previous')"
            @touchstart.passive="startSwipe($event)"
            @touchend.passive="finishSwipe($event)"
            class="kitab-reader fixed inset-0 z-50 flex flex-col bg-stone-950/95 antialiased"
            role="dialog"
            aria-modal="true"
            aria-label="Pembaca kitab {{ $readerBookMeta['name'] ?? '' }}"
        >
            <header class="flex min-h-14 shrink-0 items-center justify-between gap-3 border-b border-white/10 bg-stone-950 px-3 text-stone-100 sm:px-5">
                <div class="flex min-w-0 items-center gap-3">
                    @if(!empty($readerBookMeta['headings']))
                        <button
                            type="button"
                            @click="showToc = !showToc"
                            class="relative grid size-9 shrink-0 place-items-center rounded-md text-stone-300 hover:bg-white/8 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500"
                            aria-label="Buka daftar isi"
                            :aria-expanded="showToc"
                        >
                            <span class="absolute left-1/2 top-1/2 size-[max(100%,3rem)] -translate-1/2 pointer-fine:hidden" aria-hidden="true"></span>
                            <svg class="size-4 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6.75h16M4 12h16M4 17.25h10" /></svg>
                        </button>
                    @endif
                    <div class="min-w-0">
                        <h2 class="truncate font-arabic text-lg font-semibold" dir="rtl">{{ $readerBookMeta['name'] ?? 'Pembaca Kitab' }}</h2>
                        <p class="truncate text-sm text-stone-400">{{ $readerBookMeta['author_name'] ?? '' }}@if(!empty($readerBookMeta['category'])) · {{ $readerBookMeta['category'] }}@endif</p>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-1 sm:gap-2">
                    <label class="sr-only" for="reader-theme">Tema pembaca</label>
                    <select
                        id="reader-theme"
                        name="reader_theme"
                        x-model="theme"
                        class="h-9 rounded-md border border-white/15 bg-white/6 px-2 text-sm text-stone-200 outline-none focus:border-emerald-500 max-sm:w-20"
                    >
                        <option value="parchment" class="text-stone-900">Kertas</option>
                        <option value="white" class="text-stone-900">Terang</option>
                        <option value="dark" class="text-stone-900">Malam</option>
                    </select>

                    <div class="flex h-9 items-center border-l border-white/10 pl-1 sm:pl-2">
                        <button type="button" wire:click="decreaseFontSize" class="relative grid size-9 place-items-center rounded-md text-sm text-stone-300 hover:bg-white/8 hover:text-white" aria-label="Kecilkan huruf">
                            <span class="absolute left-1/2 top-1/2 size-[max(100%,3rem)] -translate-1/2 pointer-fine:hidden" aria-hidden="true"></span>A−
                        </button>
                        <span class="hidden min-w-10 text-center text-sm tabular-nums text-stone-400 sm:inline">{{ $readerFontSize }}</span>
                        <button type="button" wire:click="increaseFontSize" class="relative grid size-9 place-items-center rounded-md text-sm text-stone-300 hover:bg-white/8 hover:text-white" aria-label="Besarkan huruf">
                            <span class="absolute left-1/2 top-1/2 size-[max(100%,3rem)] -translate-1/2 pointer-fine:hidden" aria-hidden="true"></span>A+
                        </button>
                    </div>

                    <button type="button" wire:click="closeReader" class="relative grid size-9 place-items-center rounded-md text-stone-300 hover:bg-white/8 hover:text-white" aria-label="Tutup pembaca">
                        <span class="absolute left-1/2 top-1/2 size-[max(100%,3rem)] -translate-1/2 pointer-fine:hidden" aria-hidden="true"></span>
                        <svg class="size-4 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </header>

            <div class="relative flex min-h-0 flex-1 overflow-hidden">
                @if(!empty($readerBookMeta['headings']))
                    <aside
                        x-show="showToc"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="-translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="-translate-x-full"
                        class="absolute inset-y-0 left-0 z-30 flex w-80 max-w-[86vw] flex-col border-r border-stone-950/10 bg-stone-50 text-stone-800 shadow-xl md:static md:shadow-none"
                    >
                        <div class="flex items-center justify-between border-b border-stone-950/8 px-4 py-3">
                            <div>
                                <h3 class="text-sm font-semibold">Daftar isi</h3>
                                <p class="text-sm text-stone-500">{{ count($readerBookMeta['headings']) }} bab</p>
                            </div>
                            <button type="button" @click="showToc = false" class="relative grid size-9 place-items-center rounded-md text-stone-500 hover:bg-stone-950/5 md:hidden" aria-label="Tutup daftar isi">
                                <span class="absolute left-1/2 top-1/2 size-[max(100%,3rem)] -translate-1/2 pointer-fine:hidden" aria-hidden="true"></span>
                                <svg class="size-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="p-3">
                            <label class="sr-only" for="reader-heading-search">Cari judul bab</label>
                            <input id="reader-heading-search" name="reader_heading_search" type="search" x-model="searchHeading" placeholder="Cari judul bab" class="w-full rounded-md border border-stone-950/15 bg-white px-3 py-2.5 text-base outline-none focus:border-emerald-700 sm:py-2 sm:text-sm">
                        </div>
                        <nav class="flex-1 overflow-y-auto px-2 pb-3" aria-label="Daftar bab kitab">
                            @foreach($readerBookMeta['headings'] as $heading)
                                <button
                                    type="button"
                                    x-show="!searchHeading || @js($heading['title']).toLowerCase().includes(searchHeading.toLowerCase())"
                                    wire:click="loadPage({{ $heading['page'] }})"
                                    @click="showToc = false"
                                    dir="rtl"
                                    class="w-full border-b border-stone-950/6 px-3 py-3 text-right font-arabic text-base hover:bg-stone-950/4 {{ $readerCurrentPage == $heading['page'] ? 'text-emerald-800' : 'text-stone-700' }}"
                                >
                                    <span class="line-clamp-2">{{ $heading['title'] }}</span>
                                    <span class="mt-1 font-sans text-sm text-stone-500">Halaman {{ $heading['page'] }}</span>
                                </button>
                            @endforeach
                        </nav>
                    </aside>
                @endif

                <main class="kitab-stage relative min-w-0 flex-1 overflow-hidden p-2 sm:p-4 lg:p-6">
                    <div class="kitab-book">
                        <article class="kitab-page kitab-page-left hidden p-8 md:block lg:p-12" dir="rtl" aria-label="Halaman {{ $readerCurrentPage + 1 }}">
                            <div class="mx-auto max-w-[38ch] whitespace-pre-line text-justify font-arabic [font-size:var(--reader-font-size)] [line-height:2.35]" style="--reader-font-size: {{ $readerFontSize }}px;">
                                {{ $readerNextPageText ?: 'تم الكتاب' }}
                            </div>
                            <p class="sticky bottom-0 mt-8 bg-[var(--kitab-paper)] pt-3 text-center font-sans text-sm tabular-nums text-[var(--kitab-muted)]">{{ min($readerCurrentPage + 1, $readerTotalPages) }}</p>
                        </article>

                        <article class="kitab-page kitab-page-right p-5 sm:p-8 lg:p-12" dir="rtl" aria-label="Halaman {{ $readerCurrentPage }}">
                            <div class="mx-auto max-w-[38ch] whitespace-pre-line text-justify font-arabic [font-size:var(--reader-font-size)] [line-height:2.35]" style="--reader-font-size: {{ $readerFontSize }}px;">
                                {{ $readerPageText }}
                            </div>
                            <p class="sticky bottom-0 mt-8 bg-[var(--kitab-paper)] pt-3 text-center font-sans text-sm tabular-nums text-[var(--kitab-muted)]">{{ $readerCurrentPage }}</p>
                        </article>

                        <div
                            x-show="turning"
                            x-cloak
                            :class="direction === 'next' ? 'is-next' : 'is-previous'"
                            class="kitab-turning-page p-5 sm:p-8 lg:p-12"
                            dir="rtl"
                            aria-hidden="true"
                        >
                            <div class="mx-auto max-w-[38ch] whitespace-pre-line text-justify font-arabic [font-size:var(--reader-font-size)] [line-height:2.35]" style="--reader-font-size: {{ $readerFontSize }}px;">
                                {{ $readerPageText }}
                            </div>
                        </div>
                    </div>

                    <button type="button" @click="turn('next')" @disabled($readerCurrentPage >= $readerTotalPages) class="absolute inset-y-16 left-2 z-20 w-11 cursor-w-resize disabled:hidden" aria-label="Buka halaman berikutnya"></button>
                    <button type="button" @click="turn('previous')" @disabled($readerCurrentPage <= 1) class="absolute inset-y-16 right-2 z-20 w-11 cursor-e-resize disabled:hidden" aria-label="Kembali ke halaman sebelumnya"></button>

                    @if($readerLoading)
                        <div class="absolute inset-0 z-40 grid place-items-center bg-stone-950/35" aria-live="polite">
                            <div class="bg-stone-950 px-4 py-3 text-sm font-medium text-stone-100">Memuat lembar kitab…</div>
                        </div>
                    @endif
                </main>
            </div>

            <footer class="flex min-h-14 shrink-0 flex-wrap items-center justify-between gap-2 border-t border-white/10 bg-stone-950 px-3 text-stone-200 sm:px-5">
                <button type="button" @click="turn('previous')" @disabled($readerCurrentPage <= 1) class="inline-flex h-9 items-center gap-2 rounded-md px-2 text-sm hover:bg-white/8 disabled:cursor-not-allowed disabled:opacity-35">
                    <svg class="size-4 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" /></svg>
                    <span class="max-sm:sr-only">Sebelumnya</span>
                </button>

                <div class="flex items-center gap-2 text-sm text-stone-400">
                    <label for="reader-page-jump">Halaman</label>
                    <input id="reader-page-jump" name="reader_page_jump" type="number" min="1" max="{{ $readerTotalPages }}" value="{{ $readerCurrentPage }}" wire:keydown.enter="loadPage($event.target.value)" class="h-8 w-16 rounded-md border border-white/15 bg-white/6 px-2 text-center text-base tabular-nums text-white outline-none focus:border-emerald-500 sm:text-sm">
                    <span class="tabular-nums">dari {{ $readerTotalPages }}</span>
                </div>

                <div class="flex items-center gap-1">
                    <button
                        type="button"
                        x-data="{ copied: false }"
                        @click="navigator.clipboard.writeText($wire.readerPageText); copied = true; window.setTimeout(() => copied = false, 1800)"
                        class="relative grid size-9 place-items-center rounded-md text-stone-300 hover:bg-white/8 hover:text-white"
                        :aria-label="copied ? 'Teks tersalin' : 'Salin teks halaman'"
                    >
                        <span class="absolute left-1/2 top-1/2 size-[max(100%,3rem)] -translate-1/2 pointer-fine:hidden" aria-hidden="true"></span>
                        <svg class="size-4 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H6.75A2.25 2.25 0 0 0 4.5 10.5v8.25A2.25 2.25 0 0 0 6.75 21h8.25a2.25 2.25 0 0 0 2.25-2.25V16.5M9 8.25V5.625A2.625 2.625 0 0 1 11.625 3h6.75A2.625 2.625 0 0 1 21 5.625v6.75A2.625 2.625 0 0 1 18.375 15H15.75M9 8.25h6.75v6.75H9V8.25Z" /></svg>
                    </button>
                    <button type="button" @click="turn('next')" @disabled($readerCurrentPage >= $readerTotalPages) class="inline-flex h-9 items-center gap-2 rounded-md px-2 text-sm hover:bg-white/8 disabled:cursor-not-allowed disabled:opacity-35">
                        <span class="max-sm:sr-only">Berikutnya</span>
                        <svg class="size-4 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" /></svg>
                    </button>
                </div>
            </footer>
        </div>
    @endif
</div>
