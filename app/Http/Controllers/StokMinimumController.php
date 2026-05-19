<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateStokMinimumRequest;
use App\Models\Pengguna;
use App\Models\Produk;
use App\Services\Concerns\AuditTrailTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class StokMinimumController extends Controller
{
    use AuditTrailTrait;

    /**
     * Tampilkan halaman pengaturan stok minimum.
     */
    public function index(): View
    {
        return view('monitoring.stok-minimum', [
            'products' => Produk::query()->with(['kategori', 'satuan'])->orderBy('nama_produk')->get(),
        ]);
    }

    /**
     * Simpan perubahan stok minimum secara bulk.
     */
    public function update(UpdateStokMinimumRequest $request): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $validated = $request->validated();

        $changes = [];

        DB::transaction(function () use ($validated, $pengguna, $request, &$changes): void {
            $products = Produk::query()
                ->whereIn('id_produk', array_keys($validated['stok_minimum']))
                ->lockForUpdate()
                ->get();

            foreach ($products as $product) {
                $newMinimum = (int) $validated['stok_minimum'][$product->id_produk];

                if ((int) $product->stok_minimum === $newMinimum) {
                    continue;
                }

                $oldData = $product->only(['id_produk', 'nama_produk', 'stok_minimum']);
                $product->update(['stok_minimum' => $newMinimum]);
                $newData = $product->fresh()?->only(['id_produk', 'nama_produk', 'stok_minimum']) ?? [];

                $changes[] = [
                    'data_lama' => $oldData,
                    'data_baru' => $newData,
                ];

                $this->simpanAuditTrail(
                    'UBAH_STOK_MINIMUM',
                    'Monitoring Stok',
                    $oldData,
                    $newData,
                    $request->ip(),
                    $pengguna
                );
            }
        });

        return redirect()
            ->route('monitoring.stok-minimum.index')
            ->with('success', count($changes).' perubahan stok minimum berhasil disimpan.');
    }
}
