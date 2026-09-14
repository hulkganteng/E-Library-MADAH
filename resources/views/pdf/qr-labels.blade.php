<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Stiker Label QR Code Eksemplar Buku</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 8mm 10mm 8mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #0f172a;
            background: #fff;
        }
        .label-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4mm 4mm;
        }
        .label-cell {
            width: 33.33%;
            vertical-align: top;
            padding: 0;
        }
        .empty-cell {
            border: none;
            background: transparent;
        }
        .label-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 6px 8px;
            background: #ffffff;
            height: 35mm;
            page-break-inside: avoid;
        }
        .header {
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #047857;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
            margin-bottom: 5px;
            text-align: center;
            letter-spacing: 0.5px;
        }
        .inner-table {
            width: 100%;
            border-collapse: collapse;
        }
        .qr-col {
            width: 54px;
            vertical-align: middle;
            text-align: center;
            padding-right: 6px;
        }
        .qr-col img {
            width: 52px;
            height: 52px;
            display: block;
        }
        .info-col {
            vertical-align: middle;
        }
        .title {
            font-size: 8.5px;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.15;
            max-height: 2.3em;
            overflow: hidden;
            margin-bottom: 4px;
        }
        .inv-code {
            font-family: 'Courier', monospace;
            font-size: 9px;
            font-weight: bold;
            background: #f1f5f9;
            padding: 2px 5px;
            border-radius: 3px;
            border: 1px solid #cbd5e1;
            display: inline-block;
            color: #047857;
            margin-bottom: 3px;
        }
        .shelf {
            font-size: 7.5px;
            color: #64748b;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <table class="label-table">
        @foreach(array_chunk($copies->all(), 3) as $row)
            <tr>
                @foreach($row as $copy)
                    <td class="label-cell">
                        <div class="label-card">
                            <div class="header">PERPUSTAKAAN MA ASSADAH</div>
                            <table class="inner-table">
                                <tr>
                                    <td class="qr-col">
                                        <img src="data:image/svg+xml;base64,{{ base64_encode(\QrCode::format('svg')->size(70)->margin(0)->generate($copy->inventory_code)) }}" alt="QR">
                                    </td>
                                    <td class="info-col">
                                        <div class="title">{{ Str::limit($copy->book->title, 32) }}</div>
                                        <div class="inv-code">{{ $copy->inventory_code }}</div>
                                        <div class="shelf">Rak: {{ $copy->book->shelf?->code ?? ($copy->book->shelf?->name ?? '-') }}</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                @endforeach
                @if(count($row) < 3)
                    @for($i = 0; $i < (3 - count($row)); $i++)
                        <td class="label-cell empty-cell"></td>
                    @endfor
                @endif
            </tr>
        @endforeach
    </table>
</body>
</html>
