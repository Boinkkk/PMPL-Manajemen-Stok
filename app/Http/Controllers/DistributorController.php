<?php

namespace App\Http\Controllers;

use App\Exports\DistributorExport;
use App\Http\Requests\StoreDistributorRequest;
use App\Http\Requests\UpdateDistributorRequest;
use App\Models\Distributor;
use App\Models\Pengguna;
use App\Services\DistributorService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DistributorController extends Controller
{
    /**
     * Buat controller distributor.
     */
    public function __construct(
        private readonly DistributorService $distributorService,
    ) {}

    /**
     * Tampilkan daftar distributor.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'sort']);

        return view('distributor.index', [
            'distributors' => $this->distributorService->paginate($filters),
            'summary' => $this->distributorService->summary(),
            'filters' => $filters,
            'service' => $this->distributorService,
        ]);
    }

    /**
     * Tampilkan form tambah distributor.
     */
    public function create(): View
    {
        return view('distributor.create', [
            'kodeDistributor' => $this->distributorService->generateKodeDistributor(),
        ]);
    }

    /**
     * Simpan distributor baru.
     */
    public function store(StoreDistributorRequest $request): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $distributor = $this->distributorService->store($request->validated(), $pengguna, $request->ip());

        return redirect()
            ->route('distributor.show', $distributor)
            ->with('success', 'Distributor berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail distributor.
     */
    public function show(Request $request, Distributor $distributor): View
    {
        return view('distributor.show', [
            'distributor' => $distributor,
            'stats' => $this->distributorService->distributorStats($distributor),
            'transactions' => $this->distributorService->transactionHistory($distributor, $request->only(['tanggal_mulai', 'tanggal_selesai', 'q_transaksi'])),
            'products' => $this->distributorService->distributedProducts($distributor),
            'creatorName' => $this->distributorService->creatorName($distributor),
            'hasTransactions' => $this->distributorService->hasTransactions($distributor),
            'filters' => $request->only(['tanggal_mulai', 'tanggal_selesai', 'q_transaksi']),
            'service' => $this->distributorService,
        ]);
    }

    /**
     * Tampilkan form edit distributor.
     */
    public function edit(Distributor $distributor): View
    {
        return view('distributor.edit', [
            'distributor' => $distributor,
        ]);
    }

    /**
     * Perbarui data distributor.
     */
    public function update(UpdateDistributorRequest $request, Distributor $distributor): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $this->distributorService->update($distributor, $request->validated(), $pengguna, $request->ip());

        return redirect()
            ->route('distributor.show', $distributor)
            ->with('success', 'Distributor berhasil diperbarui.');
    }

    /**
     * Hapus distributor jika belum pernah digunakan.
     */
    public function destroy(Request $request, Distributor $distributor): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        if (! $pengguna->isAdministrator()) {
            abort(403);
        }

        if (! $this->distributorService->destroy($distributor, $pengguna, $request->ip())) {
            return back()->with('error', 'Distributor tidak dapat dihapus karena memiliki riwayat transaksi.');
        }

        return redirect()
            ->route('distributor.index')
            ->with('success', 'Distributor berhasil dihapus.');
    }

    /**
     * Cek duplikasi distributor untuk validasi AJAX.
     */
    public function checkDuplicate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_distributor' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'string', 'max:100'],
            'ignore_id' => ['nullable', 'integer'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status duplikasi berhasil diperiksa.',
            'data' => $this->distributorService->duplicateStatus(
                $data['nama_distributor'] ?? null,
                $data['email'] ?? null,
                isset($data['ignore_id']) ? (int) $data['ignore_id'] : null,
            ),
        ]);
    }

    /**
     * Export daftar distributor ke Excel.
     */
    public function export(Request $request): BinaryFileResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        if (! $pengguna->isAdministrator()) {
            abort(403);
        }

        $filters = $request->only(['q', 'sort']);
        $rows = $this->distributorService->exportRows($filters);
        $this->distributorService->auditExport($filters, $rows->count(), $pengguna, $request->ip());

        return Excel::download(
            new DistributorExport($rows, $this->distributorService->summary(), $filters),
            'data-distributor-'.now('Asia/Jakarta')->format('Ymd').'.xlsx',
        );
    }
}
