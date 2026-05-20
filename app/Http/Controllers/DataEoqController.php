<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDataEoqRequest;
use App\Models\DataEoq;
use App\Models\Pengguna;
use App\Services\EoqService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DataEoqController extends Controller
{
    /**
     * Buat controller data EOQ.
     */
    public function __construct(private readonly EoqService $eoqService) {}

    /**
     * Tampilkan halaman manajemen data EOQ.
     */
    public function index(Request $request): View
    {
        return view('monitoring.eoq.index', [
            'dataEoq' => $this->eoqService->paginate($request->input('q')),
            'summary' => $this->eoqService->summary(),
            'filters' => $request->only('q'),
        ]);
    }

    /**
     * Sinkronkan data EOQ dari produk dan transaksi stok keluar.
     */
    public function sync(Request $request): RedirectResponse
    {
        $pengguna = $request->user();

        abort_unless($pengguna instanceof Pengguna && $pengguna->canManageStock(), 403, 'Anda tidak memiliki hak akses untuk sinkronisasi EOQ.');

        $result = $this->eoqService->sync($pengguna, $request->ip());

        return redirect()
            ->route('monitoring.eoq.index')
            ->with('success', "Sinkronisasi EOQ selesai. {$result['dibuat']} data dibuat, {$result['diperbarui']} data diperbarui.");
    }

    /**
     * Perbarui data EOQ per produk.
     */
    public function update(UpdateDataEoqRequest $request, DataEoq $dataEoq): RedirectResponse
    {
        $pengguna = $request->user();

        abort_unless($pengguna instanceof Pengguna, 403, 'Sesi pengguna tidak valid.');

        $this->eoqService->update($dataEoq, $request->validated(), $pengguna, $request->ip());

        return redirect()
            ->route('monitoring.eoq.index', $request->only('q'))
            ->with('success', 'Data EOQ berhasil diperbarui.');
    }

    public function updateStokMinimum(Request $request, EoqService $eoqService)
    {
        $result = $eoqService->updateStokMinimumByEoq(
            $request->user(),
            $request->ip()
        );

        return redirect()
            ->back()
            ->with('success', "Stok minimum berhasil diperbarui untuk {$result['diperbarui']} produk.");
    }
}
