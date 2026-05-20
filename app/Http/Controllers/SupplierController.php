<?php

namespace App\Http\Controllers;

use App\Exports\SupplierExport;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Pengguna;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SupplierController extends Controller
{
    /**
     * Buat controller supplier.
     */
    public function __construct(
        private readonly SupplierService $supplierService,
    ) {}

    /**
     * Tampilkan daftar supplier.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'sort']);

        return view('supplier.index', [
            'suppliers' => $this->supplierService->paginate($filters),
            'summary' => $this->supplierService->summary(),
            'filters' => $filters,
            'service' => $this->supplierService,
        ]);
    }

    /**
     * Tampilkan form tambah supplier.
     */
    public function create(): View
    {
        return view('supplier.create', [
            'kodeSupplier' => $this->supplierService->generateKodeSupplier(),
        ]);
    }

    /**
     * Simpan supplier baru.
     */
    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $supplier = $this->supplierService->store($request->validated(), $pengguna, $request->ip());

        return redirect()
            ->route('supplier.show', $supplier)
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail supplier.
     */
    public function show(Request $request, Supplier $supplier): View
    {
        return view('supplier.show', [
            'supplier' => $supplier,
            'stats' => $this->supplierService->supplierStats($supplier),
            'transactions' => $this->supplierService->transactionHistory($supplier, $request->only(['tanggal_mulai', 'tanggal_selesai', 'q_transaksi'])),
            'products' => $this->supplierService->suppliedProducts($supplier),
            'creatorName' => $this->supplierService->creatorName($supplier),
            'hasTransactions' => $this->supplierService->hasTransactions($supplier),
            'filters' => $request->only(['tanggal_mulai', 'tanggal_selesai', 'q_transaksi']),
            'service' => $this->supplierService,
        ]);
    }

    /**
     * Tampilkan form edit supplier.
     */
    public function edit(Supplier $supplier): View
    {
        return view('supplier.edit', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Perbarui data supplier.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $this->supplierService->update($supplier, $request->validated(), $pengguna, $request->ip());

        return redirect()
            ->route('supplier.show', $supplier)
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    /**
     * Hapus supplier jika belum pernah digunakan.
     */
    public function destroy(Request $request, Supplier $supplier): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        if (! $pengguna->isAdministrator()) {
            abort(403);
        }

        if (! $this->supplierService->destroy($supplier, $pengguna, $request->ip())) {
            return back()->with('error', 'Supplier tidak dapat dihapus karena memiliki riwayat transaksi.');
        }

        return redirect()
            ->route('supplier.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }

    /**
     * Cek duplikasi supplier untuk validasi AJAX.
     */
    public function checkDuplicate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_supplier' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'string', 'max:100'],
            'ignore_id' => ['nullable', 'integer'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status duplikasi berhasil diperiksa.',
            'data' => $this->supplierService->duplicateStatus(
                $data['nama_supplier'] ?? null,
                $data['email'] ?? null,
                isset($data['ignore_id']) ? (int) $data['ignore_id'] : null,
            ),
        ]);
    }

    /**
     * Export daftar supplier ke Excel.
     */
    public function export(Request $request): BinaryFileResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        if (! $pengguna->isAdministrator()) {
            abort(403);
        }

        $filters = $request->only(['q', 'sort']);
        $rows = $this->supplierService->exportRows($filters);
        $this->supplierService->auditExport($filters, $rows->count(), $pengguna, $request->ip());

        return Excel::download(
            new SupplierExport($rows, $this->supplierService->summary(), $filters),
            'data-supplier-'.now('Asia/Jakarta')->format('Ymd').'.xlsx',
        );
    }
}
