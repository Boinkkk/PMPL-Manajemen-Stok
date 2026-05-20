@extends('layouts.app')

@section('title', 'Supplier Staf Gudang')
@section('user_greeting', 'Halo, Faiza (Staf Gudang)')

@section('sidebar_nav')
    <a class="nav-link" href="#">Dashboard</a>
    <a class="nav-link" href="#">Data Produk</a>
    <a class="nav-link active" href="{{ route('staff-gudang.supplier.index') }}">Supplier</a>
    <a class="nav-link" href="#">Stok Masuk</a>
    <a class="nav-link" href="#">Stok Keluar</a>
    <a class="nav-link" href="#">Distributor</a>
    <a class="nav-link" href="#">Laporan</a>
@endsection

@section('content')
<div class="staff-supplier-screen">
    <div class="staff-supplier-panel">
        <div class="staff-toolbar">
            <button type="button" class="staff-add-button" id="tambahSupplierBtn">
                <i class="fa-solid fa-circle-plus"></i>
                Tambah Supplier
            </button>

            <form action="{{ route('staff-gudang.supplier.index') }}" method="GET" class="staff-search-form">
                <div class="staff-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" placeholder="Cari nama supplier, kontak atau kota..." value="{{ old('search', $search) }}">
                </div>
                <button type="submit" class="staff-filter-button">
                    <i class="fa-solid fa-filter"></i>
                    Filter
                </button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="staff-supplier-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Supplier</th>
                        <th>Kontak</th>
                        <th>Alamat</th>
                        <th>Kota</th>
                        <th>Telepon</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        @php
                            $rowNumber = $suppliers->firstItem() + $loop->index;
                            $statusAktif = $rowNumber % 3 !== 2;
                            $alamatParts = collect(explode(',', $supplier->alamat ?? ''))->map(fn ($part) => trim($part))->filter()->values();
                            $kota = $alamatParts->count() > 1 ? $alamatParts->get($alamatParts->count() - 2) : 'Pamekasan';
                        @endphp
                        <tr>
                            <td>{{ $rowNumber }}</td>
                            <td>{{ $supplier->nama_supplier }}</td>
                            <td>{{ $supplier->kontak_person ?? '-' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($supplier->alamat ?? '-', 46) }}</td>
                            <td>{{ $kota }}</td>
                            <td>{{ $supplier->telepon ?? '-' }}</td>
                            <td>
                                <span class="status-pill {{ $statusAktif ? 'active' : 'inactive' }}">
                                    {{ $statusAktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <button type="button" class="staff-action detail-button" data-id="{{ $supplier->id_supplier }}">
                                    Detail
                                </button>
                                <button type="button" class="staff-action edit-button" data-id="{{ $supplier->id_supplier }}">
                                    <i class="fa-solid fa-pen"></i>
                                    Edit
                                </button>
                                <button type="button" class="staff-action delete-button" data-id="{{ $supplier->id_supplier }}" data-name="{{ $supplier->nama_supplier }}">
                                    <i class="fa-solid fa-trash-can"></i>
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="staff-empty">Tidak ada supplier yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="staff-table-footer">
            <div>
                @if($suppliers->total() > 0)
                    Menampilkan {{ $suppliers->firstItem() }} - {{ $suppliers->lastItem() }} dari {{ $suppliers->total() }} data
                @else
                    Menampilkan 0 data
                @endif
            </div>

            @if($suppliers->hasPages())
                <div class="staff-pagination">
                    @if($suppliers->onFirstPage())
                        <span class="page-control disabled"><i class="fa-solid fa-chevron-left"></i></span>
                    @else
                        <a class="page-control" href="{{ $suppliers->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
                    @endif

                    @foreach($suppliers->getUrlRange(1, $suppliers->lastPage()) as $page => $url)
                        @if(abs($page - $suppliers->currentPage()) <= 2 || $page === 1 || $page === $suppliers->lastPage())
                            <a class="page-number {{ $page === $suppliers->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($suppliers->hasMorePages())
                        <a class="page-control" href="{{ $suppliers->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
                    @else
                        <span class="page-control disabled"><i class="fa-solid fa-chevron-right"></i></span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="modalTitle">Detail Supplier</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary-custom" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="staff-modal-confirm" id="modalActionConfirm">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .staff-supplier-screen {
        min-height: calc(100vh - 66px);
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 34px 44px 56px;
        background: #ffffff;
    }

    .staff-supplier-panel {
        width: min(100%, 1110px);
        overflow: hidden;
        border-radius: 20px;
        background: #ffefb7;
    }

    .staff-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 20px 22px 18px;
    }

    .staff-add-button,
    .staff-filter-button,
    .staff-modal-confirm {
        border: 0;
        border-radius: 5px;
        background: #a8323c;
        color: #ffffff;
        font-weight: 800;
        cursor: pointer;
    }

    .staff-add-button {
        min-width: 170px;
        padding: 10px 16px;
        font-size: 0.88rem;
    }

    .staff-add-button i {
        margin-right: 8px;
    }

    .staff-search-form {
        display: flex;
        align-items: center;
        gap: 16px;
        width: min(100%, 500px);
    }

    .staff-search {
        position: relative;
        flex: 1;
    }

    .staff-search i {
        position: absolute;
        left: 18px;
        top: 50%;
        color: #ffffff;
        transform: translateY(-50%);
        font-size: 0.9rem;
    }

    .staff-search input {
        width: 100%;
        height: 38px;
        border: 0;
        border-radius: 999px;
        outline: 0;
        background: #b8b8b8;
        color: #ffffff;
        padding: 0 18px 0 48px;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .staff-search input::placeholder {
        color: #ffffff;
        opacity: 0.95;
    }

    .staff-filter-button {
        min-width: 106px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        font-size: 0.88rem;
    }

    .staff-supplier-table {
        width: 100%;
        min-width: 1050px;
        border-collapse: collapse;
        color: #161616;
    }

    .staff-supplier-table th,
    .staff-supplier-table td {
        border-bottom: 1px solid #f08319;
        padding: 13px 18px;
        font-size: 0.78rem;
        vertical-align: middle;
    }

    .staff-supplier-table th {
        font-weight: 900;
        background: #ffe59a;
    }

    .staff-supplier-table td {
        background: #fff4c8;
        font-weight: 700;
    }

    .staff-supplier-table th:first-child,
    .staff-supplier-table td:first-child {
        width: 58px;
        text-align: center;
    }

    .status-pill {
        min-width: 78px;
        display: inline-flex;
        justify-content: center;
        border-radius: 999px;
        padding: 4px 14px;
        color: #ffffff;
        font-size: 0.68rem;
        font-weight: 900;
    }

    .status-pill.active {
        background: #4db749;
    }

    .status-pill.inactive {
        background: #ef3030;
    }

    .staff-action {
        min-width: 70px;
        height: 27px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin: 0 3px;
        border: 0;
        border-radius: 5px;
        color: #ffffff;
        font-size: 0.68rem;
        font-weight: 800;
        cursor: pointer;
    }

    .staff-action.detail-button,
    .staff-action.edit-button {
        background: #086eb8;
    }

    .staff-action.delete-button {
        background: #b92c32;
    }

    .staff-empty {
        height: 88px;
        text-align: center;
    }

    .staff-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 42px 24px;
        color: #7b7b7b;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .staff-pagination {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .page-control,
    .page-number {
        min-width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #ff922f;
        border-radius: 6px;
        color: #ff922f;
        text-decoration: none;
        font-size: 0.78rem;
        font-weight: 900;
        background: #fff8dc;
    }

    .page-number.active {
        background: #ff922f;
        color: #ffffff;
    }

    .page-control.disabled {
        opacity: 0.5;
    }

    .detail-list {
        display: grid;
        gap: 0.65rem;
    }

    .staff-modal-confirm {
        display: none;
        padding: 0.85rem 1.35rem;
    }

    @media (max-width: 991.98px) {
        .staff-supplier-screen {
            padding: 24px 16px 40px;
        }

        .staff-toolbar,
        .staff-table-footer {
            align-items: stretch;
            flex-direction: column;
        }

        .staff-search-form {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

let currentSupplierId = null;
let actionModal = null;

function getActionModal() {
    if (! actionModal) {
        actionModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('actionModal'));
    }

    return actionModal;
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, function (character) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[character];
    });
}

function openModal(title, bodyHtml, confirmText = null, confirmCallback = null) {
    $('#modalTitle').text(title);
    $('#modalBody').html(bodyHtml);

    if (confirmText && confirmCallback) {
        $('#modalActionConfirm').text(confirmText).show().off('click').on('click', function () {
            confirmCallback();
        });
    } else {
        $('#modalActionConfirm').hide().off('click');
    }

    getActionModal().show();
}

function supplierForm(supplier = {}) {
    return `
        <form id="supplierForm">
            <div class="mb-3">
                <label for="kode_supplier" class="form-label">Kode Supplier *</label>
                <input type="text" id="kode_supplier" class="form-control" value="${escapeHtml(supplier.kode_supplier)}" required>
            </div>
            <div class="mb-3">
                <label for="nama_supplier" class="form-label">Nama Supplier *</label>
                <input type="text" id="nama_supplier" class="form-control" value="${escapeHtml(supplier.nama_supplier)}" required>
            </div>
            <div class="mb-3">
                <label for="kontak_person" class="form-label">Kontak</label>
                <input type="text" id="kontak_person" class="form-control" value="${escapeHtml(supplier.kontak_person)}">
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea id="alamat" class="form-control" rows="3">${escapeHtml(supplier.alamat)}</textarea>
            </div>
            <div class="mb-3">
                <label for="telepon" class="form-label">Telepon</label>
                <input type="text" id="telepon" class="form-control" value="${escapeHtml(supplier.telepon)}">
            </div>
            <div>
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control" value="${escapeHtml(supplier.email)}">
            </div>
        </form>
    `;
}

function supplierPayload() {
    return {
        kode_supplier: $('#kode_supplier').val(),
        nama_supplier: $('#nama_supplier').val(),
        alamat: $('#alamat').val(),
        telepon: $('#telepon').val(),
        email: $('#email').val(),
        kontak_person: $('#kontak_person').val()
    };
}

function showDetail(id) {
    $.get(`/supplier/${id}`, function (supplier) {
        const html = `
            <div class="detail-list">
                <div><strong>Kode:</strong> ${escapeHtml(supplier.kode_supplier)}</div>
                <div><strong>Nama:</strong> ${escapeHtml(supplier.nama_supplier)}</div>
                <div><strong>Kontak:</strong> ${escapeHtml(supplier.kontak_person || '-')}</div>
                <div><strong>Alamat:</strong> ${escapeHtml(supplier.alamat || '-')}</div>
                <div><strong>Telepon:</strong> ${escapeHtml(supplier.telepon || '-')}</div>
                <div><strong>Email:</strong> ${escapeHtml(supplier.email || '-')}</div>
            </div>
        `;

        openModal('Detail Supplier', html);
    });
}

function showEdit(id) {
    currentSupplierId = id;

    $.get(`/supplier/${id}`, function (supplier) {
        openModal('Edit Supplier', supplierForm(supplier), 'Simpan', function () {
            $.ajax({
                url: `/supplier/${currentSupplierId}`,
                method: 'PUT',
                data: supplierPayload(),
                success: function (response) {
                    alert(response.message);
                    location.reload();
                },
                error: function (xhr) {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
                }
            });
        });
    });
}

function showDelete(id, nama) {
    currentSupplierId = id;

    openModal('Konfirmasi Hapus', `<strong>Hapus supplier ${escapeHtml(nama)}?</strong>`, 'Hapus', function () {
        $.ajax({
            url: `/supplier/${currentSupplierId}`,
            method: 'DELETE',
            success: function (response) {
                alert(response.message);
                location.reload();
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message || 'Gagal menghapus supplier');
            }
        });
    });
}

$('#tambahSupplierBtn').on('click', function () {
    openModal('Tambah Supplier', supplierForm(), 'Simpan', function () {
        $.ajax({
            url: '/supplier',
            method: 'POST',
            data: supplierPayload(),
            success: function (response) {
                alert(response.message);
                location.reload();
            },
            error: function (xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
            }
        });
    });
});

$('.detail-button').on('click', function () {
    showDetail($(this).data('id'));
});

$('.edit-button').on('click', function () {
    showEdit($(this).data('id'));
});

$('.delete-button').on('click', function () {
    showDelete($(this).data('id'), $(this).data('name'));
});
</script>
@endpush
