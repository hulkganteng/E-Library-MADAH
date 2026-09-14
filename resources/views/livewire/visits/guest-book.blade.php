@props(['title' => 'Buku Kunjungan Tamu'])
<div class="space-y-6 max-w-5xl mx-auto py-2 sm:py-6">
    {{-- Header Banner --}}
    <div class="rounded-2xl bg-gradient-to-r from-brand-950 via-brand-900 to-emerald-950 p-5 sm:p-8 text-white shadow-soft relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-48 h-48 rounded-full bg-emerald-500/10 blur-2xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                    Buku Tamu Elektronik
                </span>
                <h1 class="text-xl sm:text-3xl font-bold tracking-tight text-white">Presensi & Kunjungan Tamu</h1>
                <p class="text-xs sm:text-sm text-emerald-200/80 mt-1 max-w-xl">
                    Silakan isi formulir kehadiran di bawah ini untuk mencatat kunjungan Anda di Perpustakaan MA Assadah.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <div
                    x-data="{
                        time: '',
                        date: '',
                        updateTime() {
                            const now = new Date();
                            this.date = now.toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'short', year: 'numeric' });
                            this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
                        }
                    }"
                    x-init="updateTime(); setInterval(() => updateTime(), 1000)"
                    class="bg-white/10 backdrop-blur-xs border border-white/10 rounded-xl px-4 py-3 text-center min-w-36 hidden sm:block"
                >
                    <p class="text-[11px] font-semibold text-emerald-200 uppercase tracking-wider" x-text="date"></p>
                    <p class="text-xl sm:text-2xl font-bold font-mono text-white mt-0.5" x-text="time"></p>
                </div>
                <div class="bg-white/10 backdrop-blur-xs border border-white/10 rounded-xl px-4 py-3 text-center w-full sm:w-auto min-w-28">
                    <p class="text-[11px] font-semibold text-emerald-200 uppercase tracking-wider">Pengunjung Hari Ini</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-white mt-0.5">{{ number_format($todayCount) }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($submitted)
        <div class="rounded-xl border border-emerald-200 bg-emerald-50/80 p-5 shadow-xs transition duration-200">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="rounded-full bg-emerald-600 p-2 text-white shrink-0 shadow-xs">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-emerald-950">Terima Kasih, {{ $lastVisitorName }}!</h3>
                        <p class="text-xs sm:text-sm text-emerald-800 mt-0.5">
                            Kunjungan Anda pada {{ now()->translatedFormat('l, d F Y - H:i') }} WIB berhasil dicatat ke dalam buku tamu perpustakaan. Selamat menikmati layanan kami.
                        </p>
                    </div>
                </div>
                <button
                    type="button"
                    wire:click="resetSuccess"
                    class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 underline cursor-pointer"
                >
                    Tutup
                </button>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form Kunjungan --}}
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-slate-200/90 bg-white p-6 shadow-soft">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-5 flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                    </svg>
                    Formulir Kedatangan
                </h2>

                <form wire:submit="save" class="space-y-4">
                    {{-- Nama Lengkap --}}
                    <div>
                        <x-input
                            wire:model="name"
                            label="Nama Lengkap"
                            placeholder="Contoh: Muhammad Ihsan, S.Pd / Siti Nurhaliza"
                            required
                            :error="$errors->first('name')"
                        />
                    </div>

                    {{-- Jenis Pengunjung & No Identitas --}}
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
                                label="No. Identitas / NISN / NIP (Opsional)"
                                placeholder="NISN / NIP / NIK"
                                :error="$errors->first('identifier')"
                            />
                        </div>
                    </div>

                    {{-- Instansi & No HP --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input
                                wire:model="institution"
                                label="Instansi / Asal Lembaga / Kelas"
                                placeholder="Contoh: Kelas XII IPA 1 / Kemenag / Universitas"
                                :error="$errors->first('institution')"
                            />
                        </div>
                        <div>
                            <x-input
                                wire:model="phone"
                                label="No. Handphone / WhatsApp (Opsional)"
                                placeholder="Contoh: 08123456789"
                                :error="$errors->first('phone')"
                            />
                        </div>
                    </div>

                    {{-- Keperluan Kunjungan --}}
                    <div>
                        <x-select
                            wire:model="purpose"
                            label="Keperluan / Tujuan Kunjungan"
                            required
                            :error="$errors->first('purpose')"
                        >
                            @foreach($purposes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </x-select>
                    </div>

                    {{-- Catatan / Keterangan --}}
                    <div>
                        <x-textarea
                            wire:model="notes"
                            label="Catatan Tambahan (Opsional)"
                            placeholder="Tuliskan keterangan spesifik atau keperluan Anda..."
                            rows="2"
                            :error="$errors->first('notes')"
                        />
                    </div>

                    <div class="pt-2 flex items-center justify-end">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-700 px-6 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-emerald-800 focus:ring-3 focus:ring-emerald-600/30 transition cursor-pointer disabled:opacity-50"
                        >
                            <svg wire:loading.remove class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                            <span wire:loading.remove>Kirim Presensi Kunjungan</span>
                            <span wire:loading>Menyimpan data...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar: Daftar Pengunjung Terkini Hari Ini --}}
        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-soft">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tamu Terkini Hari Ini
                    </h3>
                    <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                        {{ $todayCount }} orang
                    </span>
                </div>

                @if($recentVisitors->isEmpty())
                    <div class="py-8 text-center text-slate-400">
                        <svg class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <p class="text-xs">Belum ada catatan kunjungan hari ini.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentVisitors as $visitor)
                            <div class="flex items-start gap-3 p-2.5 rounded-lg hover:bg-slate-50 border border-slate-100 transition">
                                <div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-800 uppercase">
                                    {{ str($visitor->name)->substr(0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-bold text-slate-800">{{ $visitor->name }}</p>
                                    <p class="truncate text-[11px] text-slate-500">
                                        {{ $visitor->institution ?: $visitor->type_label }}
                                    </p>
                                    <div class="flex items-center gap-1.5 mt-1 text-[10px] text-slate-400">
                                        <span>{{ $visitor->purpose_label }}</span>
                                        <span>•</span>
                                        <span>{{ $visitor->visited_at->format('H:i') }} WIB</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Info Card --}}
            <div class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-4 text-xs text-emerald-900">
                <p class="font-bold flex items-center gap-1.5 mb-1 text-emerald-950">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                    Tata Tertib Perpustakaan
                </p>
                <ul class="list-disc list-inside space-y-1 text-emerald-800 text-[11px] mt-1.5">
                    <li>Harap menjaga ketenangan dan ketertiban.</li>
                    <li>Kembalikan buku bacaan ke meja baca / rak yang sesuai.</li>
                    <li>Dilarang merusak atau mencoret koleksi buku.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
