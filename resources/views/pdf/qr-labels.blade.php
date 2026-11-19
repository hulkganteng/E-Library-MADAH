<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Label QR & Informasi Buku (3x4 cm)</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 7mm 5mm 7mm 5mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 7.5px;
            color: #0f172a;
            background: #fff;
        }
        .label-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 3mm 3mm;
            table-layout: fixed;
        }
        .label-cell {
            width: 25%;
            vertical-align: top;
            padding: 0;
        }
        .empty-cell {
            border: none;
            background: transparent;
        }
        .label-card {
            border: 1px dashed #94a3b8;
            border-radius: 4px;
            padding: 2.5mm 2.8mm 2mm 2.8mm;
            background: #ffffff;
            height: 30.5mm;
            max-height: 30.5mm;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 0.8px solid #059669;
            padding-bottom: 2px;
            margin-bottom: 2.5px;
        }
        .header-logo {
            width: 14px;
            vertical-align: middle;
            text-align: left;
        }
        .header-logo img {
            width: 12px;
            height: 12px;
            object-fit: contain;
            display: block;
        }
        .header-logo-badge {
            width: 12px;
            height: 12px;
            background-color: #047857;
            color: #ffffff;
            font-weight: bold;
            font-size: 8px;
            line-height: 12px;
            text-align: center;
            border-radius: 2px;
        }
        .header-text {
            vertical-align: middle;
            text-align: center;
            padding-left: 2px;
        }
        .header-title {
            font-size: 6.2px;
            font-weight: bold;
            text-transform: uppercase;
            color: #047857;
            letter-spacing: 0.3px;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .header-sub {
            font-size: 5px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            line-height: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
        }
        .qr-col {
            width: 22mm;
            vertical-align: top;
            text-align: center;
            padding-right: 2mm;
        }
        .qr-box {
            border: 0.5px solid #cbd5e1;
            border-radius: 3px;
            padding: 1px;
            background: #ffffff;
            display: inline-block;
        }
        .qr-box img {
            width: 20mm;
            height: 20mm;
            display: block;
        }
        .info-col {
            vertical-align: top;
        }
        .title {
            font-size: 6.8px;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.15;
            height: 15.5px;
            overflow: hidden;
            margin-bottom: 1.5px;
            text-overflow: ellipsis;
        }
        .author {
            font-size: 5.8px;
            color: #475569;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
            line-height: 1;
        }
        .inv-code {
            font-family: 'Courier', monospace;
            font-size: 6.5px;
            font-weight: bold;
            background: #ecfdf5;
            padding: 1px 3px;
            border-radius: 2px;
            border: 0.5px solid #a7f3d0;
            display: inline-block;
            color: #047857;
            margin-bottom: 2px;
            line-height: 1;
        }
        .meta-row {
            font-size: 5.6px;
            color: #64748b;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .meta-row strong {
            color: #1e293b;
        }
    </style>
</head>
<body>
    <table class="label-table">
        @foreach(array_chunk($copies->all(), 4) as $row)
            <tr>
                @foreach($row as $copy)
                    <td class="label-cell">
                        <div class="label-card">
                            <table class="header-table">
                                <tr>
                                    <td class="header-logo">
                                        @if(!empty($logoBase64))
                                            <img src="{{ $logoBase64 }}" alt="Logo">
                                        @else
                                            <div class="header-logo-badge">م</div>
                                        @endif
                                    </td>
                                    <td class="header-text">
                                        <div class="header-title">{{ strtoupper($libraryName ?? 'PERPUSTAKAAN') }}</div>
                                        <div class="header-sub">{{ strtoupper($schoolName ?? 'MA ASSADAH') }}</div>
                                    </td>
                                </tr>
                            </table>

                            <table class="content-table">
                                <tr>
                                    <td class="qr-col">
                                        <div class="qr-box">
                                            <img src="data:image/svg+xml;base64,{{ base64_encode(\QrCode::format('svg')->size(75)->margin(0)->generate(url('/katalog/' . $copy->book_id . '?copy=' . $copy->inventory_code))) }}" alt="QR">
                                        </div>
                                    </td>
                                    <td class="info-col">
                                        <div class="title" title="{{ $copy->book->title }}">{{ Str::limit($copy->book->title, 26) }}</div>
                                        <div class="author">
                                            {{ $copy->book->authors->isNotEmpty() ? Str::limit($copy->book->authors->pluck('name')->join(', '), 18) : '-' }}
                                        </div>
                                        <div class="inv-code">{{ $copy->inventory_code }}</div>
                                        <div class="meta-row">
                                            Rak: <strong>{{ $copy->book->shelf?->code ?? ($copy->book->shelf?->name ?? '-') }}</strong>
                                        </div>
                                        @if($copy->book->category)
                                            <div class="meta-row">
                                                Kat: <strong>{{ Str::limit($copy->book->category->name, 12) }}</strong>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                @endforeach
                @if(count($row) < 4)
                    @for($i = 0; $i < (4 - count($row)); $i++)
                        <td class="label-cell empty-cell"></td>
                    @endfor
                @endif
            </tr>
        @endforeach
    </table>
</body>
</html>