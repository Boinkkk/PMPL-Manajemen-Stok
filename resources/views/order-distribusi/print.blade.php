@php
    $isFinal = $order->status === 'selesai' && $order->stokKeluar->isNotEmpty();
    $stokKeluar = $order->stokKeluar->first();
@endphp

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <title>Surat Jalan {{ $order->nomor_order }}</title>
        <style>
            body { font-family: Arial, sans-serif; color: #2B1A10; margin: 32px; }
            .header { border-bottom: 2px solid #6B3F1D; padding-bottom: 16px; margin-bottom: 24px; }
            .brand { font-size: 20px; font-weight: bold; color: #6B3F1D; }
            .title { font-size: 26px; font-weight: bold; text-align: center; margin: 24px 0; }
            .grid { display: table; width: 100%; margin-bottom: 20px; }
            .col { display: table-cell; width: 50%; vertical-align: top; }
            table { width: 100%; border-collapse: collapse; margin-top: 16px; }
            th, td { border: 1px solid #E5D8C5; padding: 8px; font-size: 12px; }
            th { background: #F6D78B; text-align: left; }
            .right { text-align: right; }
            .badge { display: inline-block; border: 1px solid #E5D8C5; padding: 4px 8px; border-radius: 4px; }
            .watermark { position: fixed; top: 42%; left: 12%; transform: rotate(-25deg); font-size: 72px; color: rgba(185, 28, 28, 0.14); font-weight: bold; z-index: -1; }
            .signatures { display: table; width: 100%; margin-top: 48px; }
            .signature { display: table-cell; width: 50%; text-align: center; }
            .line { margin: 70px auto 0; width: 220px; border-top: 1px solid #2B1A10; padding-top: 6px; }
            @media print { button { display: none; } body { margin: 20px; } }
        </style>
    </head>
    <body>
        @unless ($isFinal)
            <div class="watermark">BELUM FINAL</div>
        @endunless

        <button onclick="window.print()" style="margin-bottom: 16px;">Cetak</button>

        <div class="header">
            <div class="brand">Jamu Madura</div>
            <div>Sistem Informasi Manajemen Stok dan Distribusi</div>
        </div>

        <div class="title">SURAT JALAN</div>

        <div class="grid">
            <div class="col">
                <p><strong>Nomor Surat:</strong> {{ $isFinal ? $stokKeluar?->nomor_transaksi : $order->nomor_order }}</p>
                <p><strong>Tanggal:</strong> {{ $isFinal ? $stokKeluar?->tanggal_keluar_formatted : $order->tanggal_order_formatted }}</p>
                <p><strong>Status:</strong> <span class="badge">{{ ucfirst($order->status) }}</span></p>
            </div>
            <div class="col">
                <p><strong>Kepada:</strong> {{ $order->distributor?->nama_distributor ?? '-' }}</p>
                <p><strong>Alamat:</strong> {{ $order->distributor?->alamat ?? '-' }}</p>
                <p><strong>Kontak:</strong> {{ $order->distributor?->kontak_person ?? $order->distributor?->telepon ?? '-' }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    @if ($isFinal)
                        <th>Batch</th>
                    @endif
                    <th class="right">Jumlah</th>
                    <th>Satuan</th>
                    <th class="right">Harga</th>
                    <th class="right">Subtotal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @if ($isFinal)
                    @foreach ($stokKeluar->detailStokKeluar as $detail)
                        <tr>
                            <td>{{ $detail->produk?->nama_produk ?? '-' }}</td>
                            <td>{{ $detail->batch?->nomor_batch ?? '-' }}</td>
                            <td class="right">{{ $detail->jumlah }}</td>
                            <td>{{ $detail->produk?->satuan?->nama_satuan ?? '-' }}</td>
                            <td class="right">Rp {{ number_format((float) $detail->harga_jual, 0, ',', '.') }}</td>
                            <td class="right">Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</td>
                            <td>{{ $detail->batch?->tanggal_expired?->locale('id')->translatedFormat('d F Y') ?? '-' }}</td>
                        </tr>
                    @endforeach
                @else
                    @foreach ($order->detailOrders as $detail)
                        <tr>
                            <td>{{ $detail->produk?->nama_produk ?? '-' }}</td>
                            <td class="right">{{ $detail->jumlah_diminta }}</td>
                            <td>{{ $detail->produk?->satuan?->nama_satuan ?? '-' }}</td>
                            <td class="right">Rp {{ number_format((float) $detail->harga_satuan, 0, ',', '.') }}</td>
                            <td class="right">Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</td>
                            <td>{{ $detail->catatan ?? '-' }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <p><strong>Total item:</strong> {{ $isFinal ? $stokKeluar->detailStokKeluar->sum('jumlah') : $order->detailOrders->sum('jumlah_diminta') }}</p>
        <p><strong>Total nilai:</strong> Rp {{ number_format((float) ($isFinal ? $stokKeluar->detailStokKeluar->sum('subtotal') : $order->totalNilai()), 0, ',', '.') }}</p>

        <div class="signatures">
            <div class="signature">
                <p>Pengirim</p>
                <div class="line">Nama dan Jabatan</div>
            </div>
            <div class="signature">
                <p>Penerima</p>
                <div class="line">Nama dan Tanggal Terima</div>
            </div>
        </div>

        <p style="margin-top: 32px; font-size: 12px;">Catatan: barang diterima sesuai jumlah dan kondisi pada tabel di atas.</p>
    </body>
</html>
