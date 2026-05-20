@extends('layouts.app')

@section('title', 'Daftar Kategori')

@section('content')
<div class="content-card p-4 mb-4">
    <div class="content-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1">Daftar Kategori</h4>
            <p class="text-muted mb-0">Kelola kategori produk Anda dengan cepat dan mudah.</p>
        </div>
        <a href="{{ route('kategori.create') }}" class="btn btn-gold btn-sm">
            <i class="fa-solid fa-plus me-2"></i> Tambah Kategori
        </a>
    </div>

    <div class="row align-items-center mb-4">
        <div class="col-lg-8">
            <form action="{{ route('kategori.index') }}" method="GET">
                <div class="input-icon w-100">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control search-input" placeholder="Cari kategori..." value="{{ old('search', $search) }}">
                </div>
            </form>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <span class="badge badge-total py-2 px-3">Total Kategori: {{ $kategoris->total() }}</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0 w-100">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nama Kategori</th>
                    <th scope="col">Deskripsi</th>
                    <th scope="col" class="text-center table-action-heading">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategoris as $index => $kategori)
                    <tr>
                        <td>{{ $kategoris->firstItem() + $index }}</td>
                        <td class="fw-semibold text-dark">{{ $kategori->nama_kategori }}</td>
                        <td class="text-muted">{{ $kategori->deskripsi ?? '-' }}</td>
                        <td class="text-center table-action-cell">
                            <div class="table-actions">
                                <a href="{{ route('kategori.edit', $kategori) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-pencil me-1"></i> Edit
                                </a>
                                <form action="{{ route('kategori.destroy', $kategori) }}" method="POST" class="m-0 js-delete-form" data-delete-message="Apakah Anda yakin ingin menghapus kategori {{ $kategori->nama_kategori }}?">
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
                        <td colspan="4" class="text-center text-muted py-4">Tidak ada kategori yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        {{ $kategoris->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
