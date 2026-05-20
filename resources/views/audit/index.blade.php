@extends('layouts.app')

@section('title', 'Audit Trail')
@section('page-title', 'Audit Trail')

@section('content')
    {{-- Jika database kosong, buat data dummy melalui seeder atau SQL insert ke tabel role, pengguna, lalu audit_trail. --}}
    <div class="card-soft p-4 mb-4">
        <form id="filterForm" method="GET" action="{{ route('audit.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-6 col-xl-3">
                    <label for="id_pengguna" class="form-label fw-semibold">Pengguna</label>
                    <select id="id_pengguna" name="id_pengguna" class="form-select">
                        <option value="">Semua Pengguna</option>
                        @foreach ($penggunaList as $pengguna)
                            <option value="{{ $pengguna->id_pengguna }}" @selected(request('id_pengguna') == $pengguna->id_pengguna)>
                                {{ $pengguna->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6 col-xl-2">
                    <label for="tanggal_mulai" class="form-label fw-semibold">Tanggal Mulai</label>
                    <input id="tanggal_mulai" type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="form-control">
                </div>

                <div class="col-12 col-md-6 col-xl-2">
                    <label for="tanggal_selesai" class="form-label fw-semibold">Tanggal Selesai</label>
                    <input id="tanggal_selesai" type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="form-control">
                </div>

                <div class="col-12 col-md-6 col-xl-2">
                    <label for="aksi" class="form-label fw-semibold">Aktivitas</label>
                    <select id="aksi" name="aksi" class="form-select">
                        <option value="">Semua Aktivitas</option>
                        @foreach ($aksiList as $aksi)
                            <option value="{{ $aksi }}" @selected(request('aksi') === $aksi)>{{ $aksi }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-xl-3 d-flex gap-2">
                    <button type="submit" class="btn btn-gold flex-fill">
                        <i class="fa-solid fa-filter me-1"></i>Filter
                    </button>
                    <button type="button" class="btn btn-outline-secondary flex-fill" onclick="resetFilter()">
                        <i class="fa-solid fa-rotate-left me-1"></i>Reset
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="card-soft p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center mb-3">
            <div>
                <h5 class="mb-1 fw-bold">Daftar Aktivitas</h5>
                <small class="text-muted">Riwayat perubahan data pada sistem</small>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-success" onclick="exportCsv()">
                    <i class="fa-solid fa-file-csv me-1"></i>Ekspor Excel/CSV
                </button>
                <button type="button" class="btn btn-danger" onclick="exportPdf()">
                    <i class="fa-solid fa-file-pdf me-1"></i>Ekspor PDF
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Waktu</th>
                        <th>Pengguna</th>
                        <th>Aktivitas</th>
                        <th>Modul</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
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
                            <td><span class="badge rounded-pill text-bg-success px-3 py-2">Berhasil</span></td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-info text-white" onclick="showDetail({{ $audit->id_audit }})">
                                    <i class="fa-solid fa-eye me-1"></i>Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data audit trail.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $audits->links() }}
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-brand-brown text-white">
                    <h5 class="modal-title" id="detailModalLabel">Detail Aktivitas</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <div class="text-center text-muted py-4">Memuat detail...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const filterForm = document.getElementById('filterForm');
        const defaultAction = filterForm.action;

        function resetFilter() {
            filterForm.querySelectorAll('input, select').forEach((element) => {
                element.value = '';
            });
            filterForm.action = defaultAction;
            filterForm.submit();
        }

        function submitExport(action) {
            filterForm.action = action;
            filterForm.submit();
            filterForm.action = defaultAction;
        }

        function exportCsv() {
            submitExport('{{ route('audit.export.csv') }}');
        }

        function exportPdf() {
            submitExport('{{ route('audit.export.pdf') }}');
        }

        function formatData(data) {
            if (!data || Object.keys(data).length === 0) {
                return '-';
            }

            return Object.entries(data)
                .map(([key, value]) => `${key}=${typeof value === 'object' ? JSON.stringify(value) : value}`)
                .join(', ');
        }

        function escapeHtml(value) {
            return String(value ?? '-')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        async function showDetail(id) {
            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = '<div class="text-center text-muted py-4">Memuat detail...</div>';

            const response = await fetch(`/audit/detail/${id}`);
            const data = await response.json();

            modalBody.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6"><strong>ID Aktivitas:</strong><br>AT-${escapeHtml(data.id)}</div>
                    <div class="col-md-6"><strong>Pengguna:</strong><br>${escapeHtml(data.pengguna)}</div>
                    <div class="col-md-6"><strong>Modul:</strong><br>${escapeHtml(data.modul)}</div>
                    <div class="col-md-6"><strong>Aktivitas:</strong><br>${escapeHtml(data.aktivitas)}</div>
                    <div class="col-12"><strong>Data Sebelum:</strong><br>${escapeHtml(formatData(data.data_lama))}</div>
                    <div class="col-12"><strong>Data Sesudah:</strong><br>${escapeHtml(formatData(data.data_baru))}</div>
                    <div class="col-md-6"><strong>Waktu:</strong><br>${escapeHtml(data.waktu)}</div>
                    <div class="col-md-6"><strong>IP Address:</strong><br>${escapeHtml(data.ip)}</div>
                    <div class="col-12"><strong>Status:</strong><br><span class="badge rounded-pill text-bg-success px-3 py-2">${escapeHtml(data.status)}</span></div>
                </div>
            `;

            new bootstrap.Modal(document.getElementById('detailModal')).show();
        }
    </script>
@endpush
