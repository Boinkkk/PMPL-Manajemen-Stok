@extends('layouts.app')

@section('title', 'Daftar Retur Produk')

@section('content')
<div class="content-card p-4 mb-4">
    <div class="content-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1">Daftar Retur Produk</h4>
            <p class="text-muted mb-0">Kelola pengajuan retur distributor dan status persetujuan.</p>
        </div>
        <a href="{{ route('retur.create') }}" class="btn btn-gold btn-sm">
            <i class="fa-solid fa-plus me-2"></i> Ajukan Retur
        </a>
    </div>

    <form method="GET" class="row align-items-center g-3 mb-4">
        <div class="col-lg-3">
            <select name="status" class="form-select">
                <option value="">Semua status</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div class="col-lg-7">
            <div class="input-icon">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="{{ old('search', $search) }}" placeholder="Cari produk, distributor, atau pelapor..." class="form-control search-input">
            </div>
        </div>

        <div class="col-lg-2 d-grid">
            <button type="submit" class="btn btn-outline-secondary-custom">Filter</button>
        </div>
    </form>

    <div class="d-flex justify-content-end mb-3">
        <span class="badge badge-total py-2 px-3">Total Retur: {{ $returs->total() }}</span>
    </div>

    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0 w-100">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Distributor</th>
                    <th scope="col">Produk</th>
                    <th scope="col">Jumlah</th>
                    <th scope="col">Status</th>
                    <th scope="col">Tanggal Lapor</th>
                    <th scope="col" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returs as $retur)
                    <tr>
                        <td class="fw-semibold text-dark">#{{ $retur->id_retur }}</td>
                        <td class="text-dark">{{ $retur->distributor?->nama_distributor ?? 'N/A' }}</td>
                        <td class="text-dark">{{ $retur->produk?->nama_produk ?? 'N/A' }}</td>
                        <td class="text-muted">{{ $retur->jumlah_retur }}</td>
                        <td>
                            @if($retur->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($retur->status === 'disetujui')
                                <span class="badge bg-success">Disetujui</span>
                            @else
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $retur->tanggal_lapor?->format('Y-m-d H:i') }}</td>
                        <td class="text-center">
                            <a href="{{ route('retur.show', $retur) }}" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fa-solid fa-eye me-1"></i> Detail
                            </a>
                            @if($retur->status === 'pending')
                                <form action="{{ route('retur.destroy', $retur) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus retur pending?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-trash me-1"></i> Hapus
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada retur produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        {{ $returs->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
