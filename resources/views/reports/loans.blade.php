<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Sirkulasi Peminjaman Buku</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; margin: 0; padding: 24px; }
        .header { border-bottom: 2px solid #047857; padding-bottom: 12px; margin-bottom: 16px; }
        .title { font-size: 16px; font-weight: bold; color: #064e3b; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .subtitle { font-size: 11px; color: #64748b; margin: 4px 0 0; }
        .meta { margin-bottom: 14px; font-size: 10.5px; color: #475569; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background-color: #f1f5f9; color: #334155; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        td { border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 10.5px; color: #334155; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 24px; text-align: right; font-size: 10px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Laporan Sirkulasi Peminjaman Buku</h1>
        <p class="subtitle">Perpustakaan Madrasah Aliyah Assadah</p>
    </div>

    <div class="meta">
        <strong>Periode Laporan:</strong> {{ $from ? \Carbon\Carbon::parse($from)->format('d M Y') : 'Awal' }} s/d {{ $to ? \Carbon\Carbon::parse($to)->format('d M Y') : 'Sekarang' }} &bull;
        <strong>Total Transaksi:</strong> {{ count($loans) }} baris
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th>Nama Anggota</th>
                <th>Judul Buku</th>
                <th>Kode Eksemplar</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
                <th class="text-right">Denda (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $i => $loan)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td><strong>{{ $loan->user->name }}</strong></td>
                    <td>{{ $loan->bookCopy->book->title }}</td>
                    <td style="font-family: monospace;">{{ $loan->bookCopy->inventory_code }}</td>
                    <td>{{ $loan->borrowed_at?->format('d/m/Y') }}</td>
                    <td>{{ $loan->due_at?->format('d/m/Y') }}</td>
                    <td>{{ $loan->returned_at?->format('d/m/Y') ?: '-' }}</td>
                    <td>{{ ucfirst($loan->status) }}</td>
                    <td class="text-right">{{ $loan->fine_amount > 0 ? number_format($loan->fine_amount, 0, ',', '.') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 16px; color: #94a3b8;">
                        Tidak ada data peminjaman untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis dari Sistem E-Library MA Assadah pada {{ now()->format('d M Y, H:i') }} WIB
    </div>
</body>
</html>
