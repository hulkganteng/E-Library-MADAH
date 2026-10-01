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

        <div class="space-y-6">
            {{-- Backup Card --}}
            <x-card heading="Cadangkan Data (.JSON)" subheading="Ekspor database ke berkas JSON">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" /></svg>
                </x-slot:icon>

                <p class="text-xs text-slate-500 leading-relaxed">
                    Unduh seluruh data buku, kategori, penulis, guru, dan riwayat sirkulasi dalam format JSON portabel.
                </p>

                @if($lastBackup)
                    <div class="mt-4 rounded-lg bg-emerald-50/70 border border-emerald-200/80 p-3 text-xs">
                        <p class="font-bold text-emerald-900">Arsip Cadangan Terakhir:</p>
                        <p class="mt-1 text-slate-700 font-medium truncate">{{ $lastBackup }}</p>
                        <p class="mt-0.5 text-slate-500 font-mono text-[11px]">{{ number_format($backupSize / 1024, 1) }} KB</p>
                    </div>
                @else
                    <div class="mt-4 rounded-lg bg-slate-50 border border-slate-200/80 p-3 text-xs text-slate-400 text-center">
                        Belum ada arsip cadangan tersimpan.
                    </div>
                @endif

                <button
                    type="button"
                    wire:click="backup"
                    wire:loading.attr="disabled"
                    wire:target="backup"
                    class="mt-4 w-full inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-700 px-4 py-2.5 text-xs font-semibold text-white shadow-xs hover:bg-emerald-800 transition cursor-pointer disabled:opacity-50"
                >
                    <svg wire:loading.remove wire:target="backup" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    <svg wire:loading wire:target="backup" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="backup">Unduh Cadangan .JSON</span>
                    <span wire:loading wire:target="backup">Menyiapkan berkas...</span>
                </button>
            </x-card>

            {{-- Restore Card --}}
            <x-card heading="Pulihkan Data (.JSON)" subheading="Restorasi database dari berkas cadangan">
                <x-slot:icon>
                    <svg class="h-4.5 w-4.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                </x-slot:icon>

                <div class="rounded-lg border border-amber-200 bg-amber-50/80 p-3 text-xs text-amber-900 leading-relaxed">
                    <strong>Peringatan:</strong> Proses restorasi akan menimpa data yang ada saat ini sesuai isi berkas cadangan JSON.
                </div>

                <form wire:submit="restore" class="mt-4 space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Berkas Cadangan (.json)</label>
                        <input
                            type="file"
                            wire:model="restoreFile"
                            accept=".json,application/json"
                            class="block w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer border border-slate-300 rounded-lg p-1 bg-white"
                        >
                        @error('restoreFile')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="restore,restoreFile"
                        wire:confirm="PERINGATAN: Apakah Anda yakin ingin memulihkan database dari berkas JSON ini? Data yang ada akan ditimpa."
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white shadow-xs hover:bg-amber-700 transition cursor-pointer disabled:opacity-50"
                    >
                        <svg wire:loading.remove wire:target="restore" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <svg wire:loading wire:target="restore" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="restore">Pulihkan Database dari JSON</span>
                        <span wire:loading wire:target="restore">Memulihkan data...</span>
                    </button>
                </form>
            </x-card>
        </div>
    </div>
</div>
