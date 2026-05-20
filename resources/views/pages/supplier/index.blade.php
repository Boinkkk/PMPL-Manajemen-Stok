@extends('layouts.app')

@section('title', 'Supplier')

@section('content')
<div class="supplier-screen px-4 pb-5">
    <div class="content-card">
        <div class="card-body">
            <div class="content-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <h1 class="supplier-title mb-1">Supplier</h1>
                    <span class="badge badge-total rounded-pill px-3 py-2">{{ $suppliers->count() }} supplier</span>
                </div>

                <form action="{{ route('supplier.index') }}" method="GET" class="supplier-search-form">
                    <div class="input-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="form-control search-input" placeholder="Cari Supplier...." value="{{ old('search', $search) }}">
                    </div>
                </form>
            </div>

            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-gold" id="tambahSupplierBtn">
                    <i class="fa-solid fa-circle-plus me-2"></i>
                    Tambah Supplier
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Alamat</th>
                            <th scope="col">Kontak</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $index => $supplier)
                            <tr>
                                <td class="fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold">{{ $supplier->nama_supplier }}</div>
                                    <small class="text-muted">{{ $supplier->kode_supplier }}</small>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($supplier->alamat ?? '-', 70) }}</td>
                                <td>{{ $supplier->telepon ?? '-' }}</td>
                                <td class="text-center text-nowrap">
                                    <button type="button" class="btn btn-success btn-sm action-button detail-button me-2" data-id="{{ $supplier->id_supplier }}">
                                        <i class="fa-solid fa-circle-info me-1"></i>
                                        Detail
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm action-button edit-button me-2" data-id="{{ $supplier->id_supplier }}">
                                        <i class="fa-solid fa-pen me-1"></i>
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm action-button delete-button" data-id="{{ $supplier->id_supplier }}" data-name="{{ $supplier->nama_supplier }}">
                                        <i class="fa-solid fa-trash-can me-1"></i>
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Tidak ada supplier yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="modalTitle">Detail Supplier</h2>
                <button type="button" class="btn-close" id="closeModalBtn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary-custom" id="modalCloseFooterBtn" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-gold" id="modalActionConfirm">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .supplier-screen {
        background: #ffffff;
    }

    .supplier-title {
        color: #050505;
        font-size: 2.35rem;
        font-weight: 800;
        letter-spacing: 0;
    }

    .supplier-search-form {
        width: min(100%, 360px);
    }

    .action-button {
        min-width: 82px;
        border-radius: 0.55rem;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 0.45rem 0.75rem;
    }

    .detail-list {
        display: grid;
        gap: 0.65rem;
    }

    #modalActionConfirm {
        display: none;
    }

    @media (max-width: 991.98px) {
        .supplier-screen {
            padding-right: 1rem !important;
            padding-left: 1rem !important;
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

function closeModal() {
    $('#modalActionConfirm').hide().off('click');
    getActionModal().hide();
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
                <label for="alamat" class="form-label">Alamat</label>
                <textarea id="alamat" class="form-control" rows="3">${escapeHtml(supplier.alamat)}</textarea>
            </div>
            <div class="mb-3">
                <label for="telepon" class="form-label">Kontak</label>
                <input type="text" id="telepon" class="form-control" value="${escapeHtml(supplier.telepon)}">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control" value="${escapeHtml(supplier.email)}">
            </div>
            <div>
                <label for="kontak_person" class="form-label">Kontak Person</label>
                <input type="text" id="kontak_person" class="form-control" value="${escapeHtml(supplier.kontak_person)}">
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
        let produkHtml = '<div><strong>Produk disuplai:</strong> Belum ada produk.</div>';

        if (supplier.produk && supplier.produk.length > 0) {
            const produkItems = supplier.produk.map(function (produk) {
                return `<li>${escapeHtml(produk.nama_produk)} (${escapeHtml(produk.kode_produk)})</li>`;
            }).join('');

            produkHtml = `<div><strong>Produk disuplai:</strong><ul class="mb-0 mt-2">${produkItems}</ul></div>`;
        }

        const html = `
            <div class="detail-list">
                <div><strong>Kode:</strong> ${escapeHtml(supplier.kode_supplier)}</div>
                <div><strong>Nama:</strong> ${escapeHtml(supplier.nama_supplier)}</div>
                <div><strong>Alamat:</strong> ${escapeHtml(supplier.alamat || '-')}</div>
                <div><strong>Kontak:</strong> ${escapeHtml(supplier.telepon || '-')}</div>
                <div><strong>Email:</strong> ${escapeHtml(supplier.email || '-')}</div>
                <div><strong>Kontak Person:</strong> ${escapeHtml(supplier.kontak_person || '-')}</div>
                ${produkHtml}
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

    const html = `
        <div class="detail-list">
            <div><strong>Hapus supplier ${escapeHtml(nama)}?</strong></div>
            <div>Supplier tidak dapat dihapus jika memiliki transaksi stok masuk.</div>
        </div>
    `;

    openModal('Konfirmasi Hapus', html, 'Hapus', function () {
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

$('#modalCloseFooterBtn').on('click', closeModal);
</script>
@endpush
