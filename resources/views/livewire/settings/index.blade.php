@props(['title' => 'Pengaturan'])
<div class="space-y-6">
    <x-page-header
        title="Pengaturan Sistem"
        subtitle="Konfigurasi identitas madrasah, kebijakan peminjaman buku, dan pencadangan database."
    />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-card heading="Identitas Madrasah & Perpustakaan" subheading="Informasi kop dan data kontak resmi">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.333A48.42 48.42 0 0012 9.75c-2.551 0-5.056.2-7.5.583V21" /></svg>
                </x-slot:icon>

                <form wire:submit="save" class="space-y-5">
                    {{-- Logo & Favicon Branding Section --}}
                    <div>
                        <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Logo & Identitas Visual</h4>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            {{-- Logo Upload --}}
                            <div class="rounded-xl border border-slate-200/90 bg-slate-50/60 p-4">
                                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">Logo Madrasah / E-Library</label>
                                <div class="flex items-center gap-4">
                                    <div class="grid h-16 w-16 shrink-0 place-items-center rounded-xl bg-white border border-slate-200 shadow-2xs overflow-hidden p-1">
                                        @if($logo)
                                            <img src="{{ $logo->temporaryUrl() }}" class="h-full w-full object-contain" alt="Preview Logo">
                                        @elseif($currentLogo)
                                            <img src="{{ asset('storage/' . $currentLogo) }}" class="h-full w-full object-contain" alt="Logo">
                                        @else
                                            <div class="grid h-full w-full place-items-center rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 text-lg font-bold text-brand-950">
                                                م
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1 space-y-1.5">
                                        <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                                            <svg class="h-3.5 w-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                                            <span>Pilih File Logo</span>
                                            <input type="file" wire:model="logo" accept="image/png,image/jpeg,image/svg+xml,image/webp" class="hidden">
                                        </label>
                                        @if($currentLogo && !$logo)
                                            <button
                                                type="button"
                                                wire:click="deleteLogo"
                                                wire:confirm="Hapus logo kustom dan gunakan emblem standar?"
                                                class="block text-[11px] font-semibold text-rose-600 hover:text-rose-700 transition cursor-pointer"
                                            >
                                                Hapus Logo
                                            </button>
                                        @endif
                                        <p class="text-[11px] text-slate-400">Format PNG, JPG, SVG, atau WebP (Maks. 2MB)</p>
                                    </div>
                                </div>
                                @error('logo')<p class="mt-2 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                            </div>

                            {{-- Favicon Upload --}}
                            <div class="rounded-xl border border-slate-200/90 bg-slate-50/60 p-4">
                                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">Favicon Browser</label>
                                <div class="flex items-center gap-4">
                                    <div class="grid h-16 w-16 shrink-0 place-items-center rounded-xl bg-white border border-slate-200 shadow-2xs overflow-hidden p-2">
                                        @if($favicon)
                                            <img src="{{ $favicon->temporaryUrl() }}" class="h-8 w-8 object-contain" alt="Preview Favicon">
                                        @elseif($currentFavicon)
                                            <img src="{{ asset('storage/' . $currentFavicon) }}" class="h-8 w-8 object-contain" alt="Favicon">
                                        @else
                                            <div class="grid h-8 w-8 place-items-center rounded-md bg-emerald-700 text-xs font-bold text-white">
                                                م
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1 space-y-1.5">
                                        <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                                            <svg class="h-3.5 w-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                                            <span>Pilih Favicon</span>
                                            <input type="file" wire:model="favicon" accept=".ico,image/png,image/svg+xml,image/webp" class="hidden">
                                        </label>
                                        @if($currentFavicon && !$favicon)
                                            <button
                                                type="button"
                                                wire:click="deleteFavicon"
                                                wire:confirm="Hapus favicon kustom?"
                                                class="block text-[11px] font-semibold text-rose-600 hover:text-rose-700 transition cursor-pointer"
                                            >
                                                Hapus Favicon
                                            </button>
                                        @endif
                                        <p class="text-[11px] text-slate-400">Format ICO, PNG, atau SVG (Maks. 1MB)</p>
                                    </div>
                                </div>
                                @error('favicon')<p class="mt-2 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4">
                        <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Identitas Madrasah</h4>
                        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                            <x-input wire:model="settings.school_name" label="Nama Madrasah / Sekolah" required :error="$errors->first('settings.school_name')" placeholder="MA Assadah" />
                            <x-input wire:model="settings.library_name" label="Nama Unit Perpustakaan" placeholder="Perpustakaan MA Assadah" />
                        </div>
                    </div>

                    <div>
                        <x-textarea wire:model="settings.address" label="Alamat Lengkap Madrasah" rows="2" placeholder="Jalan Raya..." />
                    </div>

                    <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                        <x-input wire:model="settings.phone" label="Nomor Telepon Kantor" placeholder="(021) ..." />
                        <x-input wire:model="settings.email" label="Alamat Email Resmi" placeholder="perpustakaan@assaadah.sch.id" />
                    </div>

                    <div class="border-t border-slate-100 pt-5">
                        <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Kebijakan Sirkulasi & Denda</h4>
                        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-3">
                            <x-input wire:model="settings.loan_duration_days" label="Durasi Pinjam (Hari)" type="number" min="1" :error="$errors->first('settings.loan_duration_days')" />
                            <x-input wire:model="settings.max_borrow" label="Maksimal Buku / Siswa" type="number" min="1" :error="$errors->first('settings.max_borrow')" />
                            <x-input wire:model="settings.fine_per_day" label="Denda per Hari (Rp)" type="number" min="0" :error="$errors->first('settings.fine_per_day')" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-4 py-2.5 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            <span>Simpan Konfigurasi</span>
                        </button>
                    </div>
                </form>
            </x-card>
        </div>

        <div>
            <x-card heading="Pencadangan Database" subheading="Salinan cadangan database SQLite/MySQL">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" /></svg>
                </x-slot:icon>

                <p class="text-xs text-slate-500 leading-relaxed">
                    Cadangkan seluruh tabel koleksi buku, siswa, guru, dan transaksi peminjaman untuk menjaga keamanan data.
                </p>

                @if($lastBackup)
                    <div class="mt-4 rounded-lg bg-emerald-50/70 border border-emerald-200/80 p-3.5 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="grid h-5 w-5 place-items-center rounded bg-emerald-700 text-white font-bold text-[10px]">✓</span>
                            <p class="font-bold text-emerald-900">Cadangan Terakhir Tersedia</p>
                        </div>
                        <p class="mt-1.5 text-slate-600 font-medium">{{ $lastBackup }}</p>
                        <p class="mt-0.5 text-slate-400 font-mono text-[11px]">{{ number_format($backupSize / 1024, 1) }} KB</p>
                    </div>
                @else
                    <div class="mt-4 rounded-lg bg-slate-50 border border-slate-200/80 p-3.5 text-xs text-slate-400 text-center">
                        Belum ada arsip pencadangan database.
                    </div>
                @endif

                <button
                    type="button"
                    wire:click="backup"
                    class="mt-4 w-full inline-flex items-center justify-center gap-2 rounded-lg bg-slate-800 px-4 py-2.5 text-xs font-semibold text-white shadow-xs hover:bg-slate-900 transition cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                    <span>Buat Cadangan Database Sekarang</span>
                </button>
            </x-card>
        </div>
    </div>
</div>
