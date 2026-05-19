<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStokMasukRequest;
use App\Models\Pengguna;
use App\Models\Produk;
use App\Models\StokMasuk;
use App\Models\Supplier;
use App\Services\StokMasukService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StokMasukController extends Controller
{
    /**
     * Buat controller stok masuk baru.
     */
    public function __construct(private readonly StokMasukService $stokMasukService) {}

    /**
     * Tampilkan riwayat transaksi stok masuk dengan filter.
     */
    public function index(Request $request): View
    {
        $stokMasuk = StokMasuk::query()
            ->with(['supplier', 'pengguna'])
            ->bySupplier($request->input('id_supplier'))
            ->byDateRange($request->input('tanggal_mulai'), $request->input('tanggal_selesai'))
            ->search($request->input('q'))
            ->latest('tanggal_masuk')
            ->latest('id_stok_masuk')
            ->paginate(15)
            ->withQueryString();

        return view('stok-masuk.index', [
            'stokMasuk' => $stokMasuk,
            'suppliers' => Supplier::query()->orderBy('nama_supplier')->get(),
            'filters' => $request->only(['id_supplier', 'tanggal_mulai', 'tanggal_selesai', 'q']),
        ]);
    }

    /**
     * Tampilkan form transaksi stok masuk baru.
     */
    public function create(): View
    {
        return view('stok-masuk.create', [
            'suppliers' => Supplier::query()->orderBy('nama_supplier')->get(),
            'products' => Produk::query()->with('satuan')->orderBy('nama_produk')->get(),
        ]);
    }

    /**
     * Simpan transaksi stok masuk baru.
     */
    public function store(StoreStokMasukRequest $request): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $stokMasuk = $this->stokMasukService->store(
            [...$request->validated(), 'ip_address' => $request->ip()],
            $pengguna
        );

        return redirect()
            ->route('stok-masuk.show', $stokMasuk)
            ->with('success', 'Transaksi stok masuk berhasil disimpan.');
    }

    /**
     * Tampilkan detail transaksi stok masuk.
     */
    public function show(StokMasuk $stokMasuk): View
    {
        $stokMasuk->load(['supplier', 'pengguna', 'detailStokMasuk.produk.satuan', 'detailStokMasuk.batch']);

        return view('stok-masuk.show', [
            'stokMasuk' => $stokMasuk,
        ]);
    }

    /**
     * Arsipkan transaksi stok masuk.
     */
    public function destroy(Request $request, StokMasuk $stokMasuk): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        abort_unless($pengguna->isAdministrator(), 403, 'Hanya Administrator yang dapat menghapus transaksi.');

        $this->stokMasukService->destroy($stokMasuk, $pengguna, $request->ip());

        return redirect()
            ->route('stok-masuk.index')
            ->with('success', 'Transaksi stok masuk berhasil diarsipkan.');
    }
}
