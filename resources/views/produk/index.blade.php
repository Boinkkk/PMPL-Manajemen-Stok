@extends('layouts.app')

@section('title', 'Data Produk')

@section('content')
<div class="content-card p-4 mb-4">
    <div class="content-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1">Data Produk</h4>
            <p class="text-muted mb-0">Kelola daftar produk dengan cepat dan mudah.</p>
        </div>
        <a href="{{ route('produk.create') }}" class="btn btn-gold btn-sm">
            <i class="fa-solid fa-plus me-2"></i> Tambah Produk
        </a>
    </div>

    <div class="row align-items-center mb-4">
        <div class="col-lg-8">
            <form action="{{ route('produk.index') }}" method="GET">
                <div class="input-icon w-100">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control search-input" placeholder="Cari nama produk atau kode produk..." value="{{ old('search', $search) }}">
                </div>
            </form>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <span class="badge badge-total py-2 px-3">Total Produk: {{ $produks->total() }}</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-modern table-product align-middle mb-0 w-100">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Kode Produk</th>
                    <th scope="col">Nama Produk</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Harga</th>
                    <th scope="col">Stok</th>
                    <th scope="col" class="text-center table-action-heading">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produks as $index => $produk)
                    <tr>
                        <td>{{ $produks->firstItem() + $index }}</td>
                        <td class="fw-semibold text-dark">{{ $produk->kode_produk }}</td>
                        <td class="text-dark">{{ $produk->nama_produk }}</td>
                        <td class="text-muted">{{ $produk->kategori?->nama_kategori ?? '-' }}</td>
                        <td class="text-muted">{{ $produk->satuan?->nama_satuan ?? '-' }}</td>
                        <td class="text-muted text-money">{{ $produk->formatted_harga }}</td>
                        <td>
                            @if($produk->stok_terkini == 0)
                                <span class="badge bg-danger stock-badge">Habis</span>
                            @elseif($produk->stok_terkini <= $produk->stok_minimum)
                                <span class="badge bg-warning text-dark stock-badge">Menipis ({{ $produk->stok_terkini }})</span>
                            @else
                                <span class="badge bg-success stock-badge">Tersedia ({{ $produk->stok_terkini }})</span>
                            @endif
                        </td>
                        <td class="text-center table-action-cell">
                            <div class="table-actions">
                                <a href="{{ route('produk.edit', $produk->id_produk) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-pencil me-1"></i> Edit
                                </a>
                                <form action="{{ route('produk.destroy', $produk->id_produk) }}" method="POST" class="m-0 js-delete-form" data-delete-message="Apakah Anda yakin ingin menghapus produk {{ $produk->nama_produk }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-trash me-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Tidak ada produk yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        {{ $produks->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
