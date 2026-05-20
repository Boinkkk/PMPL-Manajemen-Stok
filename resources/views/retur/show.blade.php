@extends('layouts.app')

@section('title', 'Detail Retur Produk')

@section('content')
<div class="content-card p-4 mb-4">
    <div class="content-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1">Detail Retur #{{ $retur->id_retur }}</h4>
            <p class="text-muted mb-0">
                Status:
                @if($retur->status === 'pending')
                    <span class="badge bg-warning text-dark">Pending</span>
                @elseif($retur->status === 'disetujui')
                    <span class="badge bg-success">Disetujui</span>
                @else
                    <span class="badge bg-danger">Ditolak</span>
                @endif
            </p>
        </div>
        <a href="{{ route('retur.index') }}" class="btn btn-outline-secondary-custom btn-sm">
            <i class="fa-solid fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="border rounded-3 p-4 h-100">
                <h5 class="mb-4">Informasi Retur</h5>
                <dl class="detail-list mb-0">
                    <dt>Distributor</dt>
                    <dd>{{ $retur->distributor?->nama_distributor ?? 'N/A' }}</dd>

                    <dt>Produk</dt>
                    <dd>{{ $retur->produk?->nama_produk ?? 'N/A' }}</dd>

                    <dt>Jumlah Retur</dt>
                    <dd>{{ $retur->jumlah_retur }}</dd>

                    <dt>Alasan</dt>
                    <dd>{{ $retur->alasan }}</dd>

                    <dt>Tanggal Lapor</dt>
                    <dd>{{ $retur->tanggal_lapor?->format('Y-m-d H:i') }}</dd>

                    <dt>Supplier</dt>
                    <dd>{{ $retur->supplier?->nama_supplier ?? 'Default supplier' }}</dd>

                    <dt>Admin Verifikator</dt>
                    <dd>{{ $retur->adminVerifikator?->nama_lengkap ?? '-' }}</dd>

                    <dt>Alasan Penolakan</dt>
                    <dd>{{ $retur->alasan_penolakan ?? '-' }}</dd>

                    <dt>Tanggal Selesai</dt>
                    <dd class="mb-0">{{ $retur->tanggal_selesai?->format('Y-m-d H:i') ?? '-' }}</dd>
                </dl>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="border rounded-3 p-4 h-100">
                <h5 class="mb-4">Bukti Foto</h5>
                @if($retur->foto_bukti)
                    <img src="{{ asset('storage/' . $retur->foto_bukti) }}" alt="Bukti retur" class="img-fluid rounded-3 border w-100" style="max-height: 360px; object-fit: cover;">
                @else
                    <div class="border rounded-3 bg-light p-5 text-center text-muted">Tidak ada foto bukti</div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($retur->status === 'pending')
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <form action="{{ route('retur.setujui', $retur) }}" method="POST" class="content-card p-4 h-100">
                @csrf
                <h5 class="mb-3">Setujui Retur</h5>
                <p class="text-muted">Retur yang disetujui akan mengurangi stok produk terkait.</p>
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-check me-2"></i> Setujui
                </button>
            </form>
        </div>

        <div class="col-lg-6">
            <form action="{{ route('retur.tolak', $retur) }}" method="POST" class="content-card p-4 h-100">
                @csrf
                <h5 class="mb-3">Tolak Retur</h5>
                <label class="form-label fw-semibold">Alasan Penolakan</label>
                <textarea name="alasan_penolakan" rows="4" class="form-control"></textarea>
                <button type="submit" class="btn btn-danger mt-3">
                    <i class="fa-solid fa-xmark me-2"></i> Tolak
                </button>
            </form>
        </div>
    </div>
@endif

<div class="content-card p-4">
    <div class="content-header d-flex align-items-center justify-content-between">
        <div>
            <h5 class="mb-1">Riwayat Audit</h5>
            <p class="text-muted mb-0">Aktivitas perubahan pada data retur ini.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Aksi</th>
                    <th>Pengguna</th>
                    <th>Modul</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditTrail as $audit)
                    <tr>
                        <td class="fw-semibold text-dark">{{ $audit->aksi }}</td>
                        <td class="text-muted">{{ $audit->pengguna?->nama_lengkap ?? 'Sistem' }}</td>
                        <td class="text-muted">{{ $audit->modul }}</td>
                        <td class="text-muted">{{ $audit->waktu_aksi?->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada audit untuk retur ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
