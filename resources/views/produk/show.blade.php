@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
<div class="content-card p-4 mb-4">
    <div class="content-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1">Detail Produk</h4>
            <p class="text-muted mb-0">Informasi lengkap produk.</p>
        </div>
        <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary-custom">Kembali</a>
    </div>

    <div class="mt-4">
        <h5>{{ $produk->nama_produk }} <small class="text-muted">({{ $produk->kode_produk }})</small></h5>
        <p><strong>Kategori:</strong> {{ $produk->kategori?->nama_kategori ?? '-' }}</p>
        <p><strong>Satuan:</strong> {{ $produk->satuan?->nama_satuan ?? '-' }}</p>
        <p><strong>Harga:</strong> {{ $produk->formatted_harga }}</p>
        <p><strong>Stok Terkini:</strong> {{ $produk->stok_terkini }}</p>
        <p><strong>Stok Minimum:</strong> {{ $produk->stok_minimum }}</p>
        <p><strong>Deskripsi:</strong></p>
        <div class="card p-3">
            {!! nl2br(e($produk->deskripsi)) !!}
        </div>
    </div>
</div>
@endsection
