<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStokKeluarRequest;
use App\Models\Distributor;
use App\Models\OrderDistribusi;
use App\Models\Pengguna;
use App\Models\Produk;
use App\Models\StokKeluar;
use App\Services\StokKeluarService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StokKeluarController extends Controller
{
    /**
     * Buat controller stok keluar baru.
     */
    public function __construct(private readonly StokKeluarService $stokKeluarService) {}

    /**
     * Tampilkan riwayat transaksi stok keluar dengan filter.
     */
    public function index(Request $request): View
    {
        $stokKeluar = StokKeluar::query()
            ->with(['distributor', 'pengguna', 'orderDistribusi'])
            ->byDistributor($request->input('id_distributor'))
            ->byDateRange($request->input('tanggal_mulai'), $request->input('tanggal_selesai'))
            ->search($request->input('q'))
            ->latest('tanggal_keluar')
            ->latest('id_stok_keluar')
            ->paginate(15)
            ->withQueryString();

        return view('stok-keluar.index', [
            'stokKeluar' => $stokKeluar,
            'distributors' => Distributor::query()->orderBy('nama_distributor')->get(),
            'filters' => $request->only(['id_distributor', 'tanggal_mulai', 'tanggal_selesai', 'q']),
        ]);
    }

    /**
     * Tampilkan form transaksi stok keluar baru.
     */
    public function create(): View
    {
        return view('stok-keluar.create', [
            'distributors' => Distributor::query()->orderBy('nama_distributor')->get(),
            'products' => Produk::query()->with('satuan')->orderBy('nama_produk')->get(),
            'orders' => OrderDistribusi::query()->latest('tanggal_order')->get(),
        ]);
    }

    /**
     * Simpan transaksi stok keluar baru.
     */
    public function store(StoreStokKeluarRequest $request): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $stokKeluar = $this->stokKeluarService->store(
            [...$request->validated(), 'ip_address' => $request->ip()],
            $pengguna
        );

        return redirect()
            ->route('stok-keluar.show', $stokKeluar)
            ->with('success', 'Transaksi stok keluar berhasil disimpan.');
    }

    /**
     * Tampilkan detail transaksi stok keluar.
     */
    public function show(StokKeluar $stokKeluar): View
    {
        $stokKeluar->load(['distributor', 'pengguna', 'orderDistribusi', 'detailStokKeluar.produk.satuan', 'detailStokKeluar.batch']);

        return view('stok-keluar.show', [
            'stokKeluar' => $stokKeluar,
        ]);
    }

    /**
     * Arsipkan transaksi stok keluar.
     */
    public function destroy(Request $request, StokKeluar $stokKeluar): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        abort_unless($pengguna->isAdministrator(), 403, 'Hanya Administrator yang dapat menghapus transaksi.');

        $this->stokKeluarService->destroy($stokKeluar, $pengguna, $request->ip());

        return redirect()
            ->route('stok-keluar.index')
            ->with('success', 'Transaksi stok keluar berhasil diarsipkan.');
    }
}
