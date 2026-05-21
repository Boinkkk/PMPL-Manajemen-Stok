@pushOnce('legacy-layout-styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
    <style>
        .content-card,
        .card-soft {
            background: #fffdf8;
            border: 1px solid #e5d8c5;
            border-radius: 0.5rem;
            box-shadow: 0 12px 28px rgba(63, 36, 18, 0.06);
        }

        .content-header {
            border-left: 4px solid #d99a22;
            background: #fff8ec;
            border-radius: 0.5rem;
            padding: 1rem;
        }

        .product-page-header {
            display: grid;
            grid-template-columns: minmax(240px, 1fr) auto;
            align-items: center;
            gap: 1rem;
        }

        .module-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        .btn-gold {
            background: #d99a22;
            border-color: #d99a22;
            color: #2b1a10;
            font-weight: 700;
        }

        .btn-gold:hover,
        .btn-gold:focus {
            background: #f6d78b;
            border-color: #f6d78b;
            color: #2b1a10;
        }

        .btn-back,
        .btn-manage {
            background: #6b3f1d;
            border-color: #6b3f1d;
            color: #ffffff;
            font-weight: 700;
        }

        .btn-back:hover,
        .btn-manage:hover {
            background: #3f2412;
            border-color: #3f2412;
            color: #ffffff;
        }

        .table-modern {
            min-width: 980px;
        }

        .table-modern.table-product {
            min-width: 1320px;
        }

        .table-modern.table-batch {
            min-width: 1080px;
        }

        .table-modern thead th {
            color: #7a6a5c;
            font-size: 0.76rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .table-modern tbody tr:hover,
        .table-hover tbody tr:hover {
            background: #fff8ec;
        }

        .table-actions,
        .module-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-actions {
            justify-content: center;
            white-space: nowrap;
        }

        .input-icon {
            position: relative;
        }

        .input-icon .fa-search,
        .input-icon .fa-magnifying-glass {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #7a6a5c;
        }

        .input-icon input {
            padding-left: 2.75rem;
        }

        .badge-total {
            background: #f6d78b;
            color: #2b1a10;
        }

        .toast-stack {
            position: fixed;
            top: 5rem;
            right: 1rem;
            z-index: 1080;
            display: grid;
            gap: 0.75rem;
            width: min(420px, calc(100vw - 2rem));
        }

        @media (max-width: 767.98px) {
            .product-page-header {
                grid-template-columns: 1fr;
            }

            .module-actions {
                justify-content: stretch;
            }

            .module-actions .btn,
            .content-header .btn {
                width: 100%;
            }
        }
    </style>
@endPushOnce

@pushOnce('legacy-layout-scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let pendingDeleteForm = null;
            const deleteConfirmModal = document.getElementById('deleteConfirmModal');
            const deleteConfirmMessage = document.getElementById('deleteConfirmMessage');
            const deleteConfirmButton = document.getElementById('deleteConfirmButton');

            document.querySelectorAll('.js-loading-form').forEach(function (form) {
                form.addEventListener('submit', function () {
                    const button = form.querySelector('button[type="submit"]');

                    if (button) {
                        button.disabled = true;
                        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memuat...';
                    }
                });
            });

            document.querySelectorAll('.toast-stack .alert').forEach(function (alert) {
                setTimeout(function () {
                    bootstrap.Alert.getOrCreateInstance(alert).close();
                }, 4200);
            });

            if (deleteConfirmModal && deleteConfirmMessage && deleteConfirmButton) {
                const deleteModal = new bootstrap.Modal(deleteConfirmModal);

                document.querySelectorAll('.js-delete-form').forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        event.preventDefault();
                        pendingDeleteForm = form;
                        deleteConfirmMessage.textContent = form.dataset.deleteMessage || 'Apakah Anda yakin ingin menghapus data ini?';
                        deleteModal.show();
                    });
                });

                deleteConfirmButton.addEventListener('click', function () {
                    if (! pendingDeleteForm) {
                        return;
                    }

                    deleteConfirmButton.disabled = true;
                    deleteConfirmButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menghapus...';
                    pendingDeleteForm.submit();
                });

                deleteConfirmModal.addEventListener('hidden.bs.modal', function () {
                    pendingDeleteForm = null;
                    deleteConfirmButton.disabled = false;
                    deleteConfirmButton.innerHTML = 'Hapus';
                });
            }
        });
    </script>
@endPushOnce

<x-layouts.app :title="trim($__env->yieldContent('title', 'Manajemen Stok Jamu Madura'))">
    @if(session('success') || session('error'))
        <div class="toast-stack">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif
        </div>
    @endif

    @yield('content')

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="mb-3 text-danger">
                        <i class="fa-solid fa-trash fa-2x"></i>
                    </div>
                    <h5 class="mb-2 fw-bold" id="deleteConfirmModalLabel">Hapus Data?</h5>
                    <p class="mb-0 text-muted" id="deleteConfirmMessage">Apakah Anda yakin ingin menghapus data ini?</p>
                </div>
                <div class="modal-footer justify-content-center gap-2 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="deleteConfirmButton">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
