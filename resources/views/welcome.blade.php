@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $totalProduk = \App\Models\Produk::count();
    $totalKategori = \App\Models\Kategori::count();
    $totalSatuan = \App\Models\Satuan::count();
    $produkMenipis = \App\Models\Produk::whereColumn('stok_terkini', '<=', 'stok_minimum')->count();
    $produkTerbaru = \App\Models\Produk::with(['kategori', 'satuan'])
        ->latest('created_at')
        ->limit(5)
        ->get();
@endphp

@section('content')
<div class="dashboard-shell">
    <div class="dashboard-hero mb-4">
        <div>
            <span class="dashboard-eyebrow">Ringkasan Sistem</span>
            <h1 class="mb-2">Dashboard</h1>
            <p class="mb-0">Pantau data produk, kategori, satuan, dan kondisi stok jamu Madura dalam satu tampilan.</p>
        </div>
        <a href="{{ route('produk.create') }}" class="btn btn-gold">
            <i class="fa-solid fa-plus me-2"></i> Tambah Produk
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="dashboard-stat stat-product">
                <div class="stat-icon">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div>
                    <span>Total Produk</span>
                    <strong>{{ $totalProduk }}</strong>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="dashboard-stat stat-category">
                <div class="stat-icon">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <div>
                    <span>Total Kategori</span>
                    <strong>{{ $totalKategori }}</strong>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="dashboard-stat stat-unit">
                <div class="stat-icon">
                    <i class="fa-solid fa-ruler-combined"></i>
                </div>
                <div>
                    <span>Total Satuan</span>
                    <strong>{{ $totalSatuan }}</strong>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="dashboard-stat stat-warning">
                <div class="stat-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <span>Produk Menipis</span>
                    <strong>{{ $produkMenipis }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="content-card p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <h4 class="mb-1">Produk Terbaru</h4>
                <p class="text-muted mb-0">Ringkasan produk yang terakhir ditambahkan.</p>
            </div>
            <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary-custom btn-sm">
                Lihat Semua
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produkTerbaru as $produk)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">{{ $produk->nama_produk }}</div>
                                <small class="text-muted">{{ $produk->kode_produk }}</small>
                            </td>
                            <td>{{ $produk->kategori?->nama_kategori ?? '-' }}</td>
                            <td>{{ $produk->satuan?->nama_satuan ?? '-' }}</td>
                            <td>
                                @if($produk->stok_terkini == 0)
                                    <span class="badge bg-danger">Habis</span>
                                @elseif($produk->stok_terkini <= $produk->stok_minimum)
                                    <span class="badge bg-warning text-dark">Menipis ({{ $produk->stok_terkini }})</span>
                                @else
                                    <span class="badge bg-success">Tersedia ({{ $produk->stok_terkini }})</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .dashboard-shell {
        max-width: 1180px;
        margin: 0 auto;
    }

    .dashboard-hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1.5rem;
        background: linear-gradient(135deg, #5C3317 0%, #3f220f 100%);
        color: #fff;
        border-radius: 1.25rem;
        padding: 2rem;
        box-shadow: 0 24px 50px rgba(92, 51, 23, 0.16);
    }

    .dashboard-hero h1 {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        letter-spacing: 0;
    }

    .dashboard-hero p {
        color: rgba(255, 255, 255, 0.78);
        max-width: 640px;
    }

    .dashboard-eyebrow {
        display: inline-flex;
        color: #F6D78B;
        font-weight: 700;
        margin-bottom: 0.6rem;
    }

    .dashboard-stat {
        display: flex;
        align-items: center;
        gap: 1rem;
        min-height: 132px;
        padding: 1.3rem;
        border-radius: 1.25rem;
        color: #fff;
        box-shadow: 0 18px 36px rgba(43, 26, 16, 0.12);
    }

    .dashboard-stat span {
        display: block;
        opacity: 0.85;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .dashboard-stat strong {
        display: block;
        font-size: 2rem;
        line-height: 1;
    }

    .stat-icon {
        width: 58px;
        height: 58px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.18);
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .stat-product {
        background: linear-gradient(135deg, #5C3317, #7A4A24);
    }

    .stat-category {
        background: linear-gradient(135deg, #C8952A, #A57115);
    }

    .stat-unit {
        background: linear-gradient(135deg, #2F7D5C, #1F5F45);
    }

    .stat-warning {
        background: linear-gradient(135deg, #B45309, #8A3508);
    }

    @media (max-width: 767.98px) {
        .dashboard-hero {
            align-items: stretch;
            flex-direction: column;
            padding: 1.5rem;
        }
    }
</style>
@endpush
