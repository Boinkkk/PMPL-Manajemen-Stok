<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <title>{{ $title }}</title>
        <style>
            body { font-family: Arial, sans-serif; color: #2B1A10; font-size: 11px; }
            h1 { color: #6B3F1D; margin-bottom: 4px; }
            table { width: 100%; border-collapse: collapse; margin-top: 12px; }
            th, td { border: 1px solid #E5D8C5; padding: 5px; }
            th { background: #F6D78B; font-weight: bold; }
            .meta { margin-bottom: 12px; }
            .watermark { position: fixed; top: 42%; left: 18%; transform: rotate(-25deg); font-size: 60px; color: rgba(107, 63, 29, 0.10); font-weight: bold; z-index: -1; }
            .footer { position: fixed; right: 0; bottom: -10px; color: #7C6A58; font-size: 9px; }
            .page-number::after { content: "Halaman " counter(page) " dari " counter(pages); }
            @page { margin: 28px; }
        </style>
    </head>
    <body>
        <div class="watermark">KONFIDENSIAL</div>
        <div class="footer"><span class="page-number"></span></div>
        <h1>{{ $title }}</h1>
        <div class="meta">
            <div>Perusahaan: Jamu Madura</div>
            <div>Waktu cetak: {{ $meta['Waktu Cetak'] ?? '-' }}</div>
            <div>Dicetak oleh: {{ $meta['Dicetak Oleh'] ?? '-' }}</div>
        </div>
        <table>
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th>{{ str($column)->replace('_', ' ')->title() }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach ($columns as $column)
                            <td>{{ $row->{$column} ?? '-' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <table>
            <tbody>
                @foreach ($summary as $label => $value)
                    <tr>
                        <th>{{ $label }}</th>
                        <td>{{ $value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
