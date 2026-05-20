@extends('layouts.app')

@section('title', 'Data Batch')

@section('content')
<div class="content-card p-4 mb-4">
    <div class="content-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1">Data Batch</h4>
            <p class="text-muted mb-0">Kelola batch produk dan tanggal kedaluwarsa dengan mudah.</p>
        </div>
        <div class="module-actions">
            <a href="{{ route('produk.index') }}" class="btn btn-back btn-sm">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali 
            </a>
            <a href="{{ route('batch.create') }}" class="btn btn-gold btn-sm">
                <i class="fa-solid fa-plus me-2"></i> Tambah Batch
            </a>
        </div>
    </div>

    <div class="row align-items-center mb-4">
        <div class="col-lg-8">
            <form action="{{ route('batch.index') }}" method="GET">
                <div class="input-icon w-100">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control search-input" placeholder="Cari nomor batch, nama produk, atau kode produk..." value="{{ old('search', $search) }}">
                </div>
            </form>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <span class="badge badge-total py-2 px-3">Total Batch: {{ $batches->total() }}</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-modern table-batch align-middle mb-0 w-100">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nomor Batch</th>
                    <th scope="col">Produk</th>
                    <th scope="col">Produksi</th>
                    <th scope="col">Expired</th>
                    <th scope="col">Status</th>
                    <th scope="col">Keterangan</th>
                    <th scope="col" class="text-center table-action-heading">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batches as $index => $batch)
                    <tr>
                        <td>{{ $batches->firstItem() + $index }}</td>
                        <td class="fw-semibold text-dark">{{ $batch->nomor_batch }}</td>
                        <td>
                            <span class="d-block fw-semibold text-dark">{{ $batch->produk?->nama_produk ?? '-' }}</span>
                            <span class="text-muted small">{{ $batch->produk?->kode_produk ?? '-' }}</span>
                        </td>
                        <td class="text-muted text-money">{{ $batch->tanggal_produksi?->format('d M Y') ?? '-' }}</td>
                        <td class="text-muted text-money">{{ $batch->tanggal_expired->format('d M Y') }}</td>
                        <td>
                            @if($batch->tanggal_expired->isPast())
                                <span class="badge bg-danger stock-badge">Kadaluarsa</span>
                            @elseif($batch->tanggal_expired->lte(now()->addDays(30)))
                                <span class="badge bg-warning text-dark stock-badge">Segera Expired</span>
                            @else
                                <span class="badge bg-success stock-badge">Aktif</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $batch->keterangan ?? '-' }}</td>
                        <td class="text-center table-action-cell">
                            <div class="table-actions">
                                <a href="{{ route('batch.edit', $batch) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-pencil me-1"></i> Edit
                                </a>
                                <form action="{{ route('batch.destroy', $batch) }}" method="POST" class="m-0 js-delete-form" data-delete-message="Apakah Anda yakin ingin menghapus batch {{ $batch->nomor_batch }}?">
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
                        <td colspan="8" class="text-center text-muted py-4">Tidak ada batch yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        {{ $batches->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
