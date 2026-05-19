@extends('layouts.app')

@section('title', 'Data Satuan')

@section('content')
<div class="content-card p-4 mb-4">
    <div class="content-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1">Data Satuan</h4>
            <p class="text-muted mb-0">Kelola daftar satuan produk dengan cepat dan mudah.</p>
        </div>
        <a href="{{ route('satuan.create') }}" class="btn btn-gold btn-sm">
            <i class="fa-solid fa-plus me-2"></i> Tambah Satuan
        </a>
    </div>

    <div class="row align-items-center mb-4">
        <div class="col-lg-8">
            <form action="{{ route('satuan.index') }}" method="GET" class="d-flex gap-2">
                <div class="input-icon w-100">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control search-input" placeholder="Cari nama satuan atau singkatan..." value="{{ old('search', $search) }}">
                </div>
                <button type="submit" class="btn btn-outline-secondary-custom">Cari</button>
            </form>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <span class="badge badge-total py-2 px-3">Total Satuan: {{ $satuans->total() }}</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0 w-100">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nama Satuan</th>
                    <th scope="col">Singkatan</th>
                    <th scope="col">Tanggal Dibuat</th>
                    <th scope="col" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($satuans as $index => $satuan)
                    <tr>
                        <td>{{ $satuans->firstItem() + $index }}</td>
                        <td class="fw-semibold text-dark">{{ $satuan->nama_satuan }}</td>
                        <td class="text-muted">{{ $satuan->singkatan }}</td>
                        <td class="text-muted">{{ $satuan->created_at ? $satuan->created_at->format('d M Y') : '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('satuan.edit', $satuan) }}" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fa-solid fa-pencil me-1"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-action="{{ route('satuan.destroy', $satuan) }}" data-name="{{ $satuan->nama_satuan }}">
                                <i class="fa-solid fa-trash me-1"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Tidak ada satuan yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        {{ $satuans->links('pagination::bootstrap-5') }}
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Yakin ingin menghapus satuan <strong class="modal-satuan-name"></strong>?</p>
                    <p class="text-muted mb-0">Tindakan ini tidak dapat dikembalikan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-trash me-1"></i> Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
