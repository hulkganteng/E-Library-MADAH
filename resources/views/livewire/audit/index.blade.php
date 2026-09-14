@props(['title' => 'Audit Log'])
<div class="space-y-6">
    <x-page-header
        title="Audit Trail & Riwayat Sistem"
        subtitle="Log pencatatan aktivitas pengguna, modifikasi data, dan integritas keamanan sistem."
    />

    {{-- Filter Panel --}}
    <div class="grid grid-cols-1 gap-3.5 rounded-xl border border-slate-200/90 bg-white p-4 shadow-soft sm:grid-cols-3">
        <x-select wire:model.live="actionFilter" label="Filter Jenis Aktivitas">
            <option value="">Semua Aktivitas</option>
            <option value="create">Pembuatan Data (Create)</option>
            <option value="update">Pembaruan Data (Update)</option>
            <option value="delete">Penghapusan Data (Delete)</option>
            <option value="login">Masuk Akun (Login)</option>
            <option value="logout">Keluar Akun (Logout)</option>
        </x-select>
        <x-input wire:model.live="dateFrom" label="Dari Tanggal" type="date" />
        <x-input wire:model.live="dateTo" label="Sampai Tanggal" type="date" />
    </div>

    {{-- Data Table --}}
    <x-card>
        <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5 sm:px-6">Waktu Kejadian</th>
                        <th class="px-4 py-3.5">Pelaksana / Pengguna</th>
                        <th class="px-4 py-3.5">Aktivitas</th>
                        <th class="hidden px-4 py-3.5 md:table-cell">Entitas / Model</th>
                        <th class="hidden px-4 py-3.5 lg:table-cell">Alamat IP</th>
                        <th class="px-5 py-3.5 sm:px-6 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5 sm:px-6 whitespace-nowrap text-xs text-slate-500 font-mono">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <p class="font-bold text-slate-900 text-xs">{{ $log->causer?->name ?? 'Sistem Otomatis' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $log->causer?->roles->first()->name ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <x-badge
                                    color="{{ match($log->event ?? $log->description) {
                                        'created', 'create' => 'green',
                                        'updated', 'update' => 'amber',
                                        'deleted', 'delete' => 'red',
                                        'login' => 'blue',
                                        default => 'slate'
                                    } }}"
                                    size="sm"
                                    dot
                                >
                                    {{ strtoupper($log->event ?? $log->description ?? 'LOG') }}
                                </x-badge>
                            </td>
                            <td class="hidden px-4 py-3.5 md:table-cell whitespace-nowrap text-xs font-mono text-slate-600">
                                {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                            </td>
                            <td class="hidden px-4 py-3.5 lg:table-cell whitespace-nowrap text-xs font-mono text-slate-400">
                                {{ $log->properties['ip'] ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 sm:px-6 text-right whitespace-nowrap">
                                <button
                                    type="button"
                                    wire:click="showDetail({{ $log->id }})"
                                    class="rounded-md px-2.5 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 transition cursor-pointer"
                                >
                                    Periksa JSON
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-xs text-slate-400">
                                Belum ada log aktivitas tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="mt-4 pt-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </x-card>

    {{-- Detail Modal --}}
    <x-modal :show="$showModal" title="Detail Perubahan Data (Payload)">
        @if($selectedLog)
            <div class="space-y-3">
                <div class="rounded-lg bg-slate-50 p-3 text-xs space-y-1 border border-slate-200/80">
                    <p><span class="font-semibold text-slate-700">Pelaksana:</span> {{ $selectedLog->causer?->name ?? 'Sistem' }}</p>
                    <p><span class="font-semibold text-slate-700">Aksi:</span> {{ strtoupper($selectedLog->event ?? $selectedLog->description) }} pada {{ class_basename($selectedLog->subject_type) }} #{{ $selectedLog->subject_id }}</p>
                    <p><span class="font-semibold text-slate-700">Waktu:</span> {{ $selectedLog->created_at->format('d M Y, H:i:s') }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Perubahan Nilai Kolom</label>
                    <pre class="max-h-80 overflow-auto rounded-lg bg-slate-900 p-4 font-mono text-xs text-emerald-300 leading-relaxed">{{ json_encode($selectedLog->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>

                <div class="flex justify-end pt-3 border-t border-slate-100">
                    <button
                        type="button"
                        wire:click="$set('showModal', false)"
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        @endif
    </x-modal>
</div>
