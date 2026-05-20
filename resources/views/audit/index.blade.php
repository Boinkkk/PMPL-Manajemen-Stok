@extends('layouts.app')

@section('title', 'Audit Trail')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1 text-brown">Audit Trail</h1>
            <p class="text-muted mb-0">Riwayat aktivitas sistem manajemen stok dan distribusi.</p>
        </div>
    </div>

    <div class="card audit-card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form id="filterForm" action="{{ route('audit.index') }}" method="GET">
                <div class="row row-cols-1 row-cols-md-4 g-3 align-items-end">
                    <div class="col">
                        <label for="id_pengguna" class="form-label fw-semibold">Pengguna</label>
                        <select class="form-select" id="id_pengguna" name="id_pengguna">
                            <option value="">Semua Pengguna</option>
                            @foreach ($penggunaList as $pengguna)
                                <option value="{{ $pengguna->id_pengguna }}" @selected(request('id_pengguna') == $pengguna->id_pengguna)>
                                    {{ $pengguna->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col">
                        <label for="tanggal_mulai" class="form-label fw-semibold">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                    </div>

                    <div class="col">
                        <label for="tanggal_selesai" class="form-label fw-semibold">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                    </div>

                    <div class="col">
                        <label for="aksi" class="form-label fw-semibold">Aktivitas</label>
                        <select class="form-select" id="aksi" name="aksi">
                            <option value="">Semua Aktivitas</option>
                            @foreach ($aksiList as $aksi)
                                <option value="{{ $aksi }}" @selected(request('aksi') === $aksi)>{{ $aksi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col d-flex gap-2">
                        <button type="submit" class="btn btn-gold flex-fill">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        <button type="button" class="btn btn-outline-secondary flex-fill" onclick="resetFilter()">
                            <i class="fa-solid fa-rotate-left me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card audit-card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <h2 class="h5 fw-bold text-brown mb-0">Daftar Aktivitas</h2>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportCsv()">
                        <i class="fa-solid fa-file-csv me-1"></i> Ekspor Excel/CSV
                    </button>
                    <button type="button" class="btn btn-danger" onclick="exportPdf()">
                        <i class="fa-solid fa-file-pdf me-1"></i> Ekspor PDF
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0 audit-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Aktivitas</th>
                            <th>Modul</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($audits as $audit)
                            <tr>
                                <td>{{ $audits->firstItem() + $loop->index }}</td>
                                <td>{{ $audit->waktu_aksi?->format('d-m-Y H:i:s') }}</td>
                                <td>{{ $audit->pengguna?->nama_lengkap ?? '-' }}</td>
                                <td>{{ $audit->aksi }}</td>
                                <td>{{ $audit->modul }}</td>
                                <td><span class="badge rounded-pill bg-success">Berhasil</span></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info text-white" onclick="showDetail({{ $audit->id_audit }})">
                                        <i class="fa-solid fa-circle-info me-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada data audit trail.
                                    {{-- Contoh dummy: INSERT INTO audit_trail (id_pengguna, aksi, modul, data_lama, data_baru, ip_address, waktu_aksi) VALUES (1, 'Tambah', 'Produk', NULL, '{"nama":"Jamu Kunyit"}', '127.0.0.1', NOW()); --}}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $audits->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-brown">
                    <h5 class="modal-title" id="detailModalLabel">Detail Aktivitas</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="modalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .audit-card {
            border-radius: 14px;
        }

        .text-brown {
            color: #7a3d00;
        }

        .btn-gold {
            background-color: #ffb300;
            border-color: #ffb300;
            color: #3f2200;
            font-weight: 700;
        }

        .btn-gold:hover {
            background-color: #ee7b00;
            border-color: #ee7b00;
            color: #fff;
        }

        .audit-table thead th {
            background-color: #7a3d00;
            color: #fff;
            border-color: #7a3d00;
            white-space: nowrap;
        }

        .audit-table tbody tr:hover {
            --bs-table-hover-bg: rgba(255, 179, 0, 0.14);
        }

        .modal-header-brown {
            background-color: #7a3d00;
            color: #fff;
        }

        .detail-list dt {
            color: #7a3d00;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const filterForm = document.getElementById('filterForm');
        const indexAction = @json(route('audit.index'));
        const csvAction = @json(route('audit.export.csv'));
        const pdfAction = @json(route('audit.export.pdf'));
        const detailUrlTemplate = @json(route('audit.detail', ['id' => '__ID__']));

        function resetFilter() {
            filterForm.querySelectorAll('input, select').forEach((field) => {
                field.value = '';
            });
            filterForm.action = indexAction;
            filterForm.submit();
        }

        function submitExport(action) {
            filterForm.action = action;
            filterForm.submit();
            filterForm.action = indexAction;
        }

        function exportCsv() {
            submitExport(csvAction);
        }

        function exportPdf() {
            submitExport(pdfAction);
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function formatData(data) {
            if (!data || Object.keys(data).length === 0) {
                return '-';
            }

            return Object.entries(data)
                .map(([key, value]) => `${escapeHtml(key)}=${escapeHtml(typeof value === 'object' ? JSON.stringify(value) : value)}`)
                .join(', ');
        }

        async function showDetail(id) {
            const response = await fetch(detailUrlTemplate.replace('__ID__', id), {
                headers: {
                    'Accept': 'application/json',
                },
            });
            const data = await response.json();

            document.getElementById('modalBody').innerHTML = `
                <dl class="row detail-list mb-0">
                    <dt class="col-sm-4">ID Aktivitas</dt>
                    <dd class="col-sm-8">AT-${escapeHtml(data.id)}</dd>
                    <dt class="col-sm-4">Pengguna</dt>
                    <dd class="col-sm-8">${escapeHtml(data.pengguna)}</dd>
                    <dt class="col-sm-4">Modul</dt>
                    <dd class="col-sm-8">${escapeHtml(data.modul)}</dd>
                    <dt class="col-sm-4">Aktivitas</dt>
                    <dd class="col-sm-8">${escapeHtml(data.aktivitas)}</dd>
                    <dt class="col-sm-4">Data Sebelum</dt>
                    <dd class="col-sm-8">${formatData(data.data_lama)}</dd>
                    <dt class="col-sm-4">Data Sesudah</dt>
                    <dd class="col-sm-8">${formatData(data.data_baru)}</dd>
                    <dt class="col-sm-4">Waktu</dt>
                    <dd class="col-sm-8">${escapeHtml(data.waktu)}</dd>
                    <dt class="col-sm-4">IP Address</dt>
                    <dd class="col-sm-8">${escapeHtml(data.ip)}</dd>
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8"><span class="badge rounded-pill bg-success">${escapeHtml(data.status)}</span></dd>
                </dl>
            `;

            new bootstrap.Modal(document.getElementById('detailModal')).show();
        }
    </script>
@endpush
