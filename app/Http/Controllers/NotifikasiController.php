<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Pengguna;
use App\Services\NotifikasiService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Buat controller notifikasi baru.
     */
    public function __construct(private readonly NotifikasiService $notifikasiService) {}

    /**
     * Tampilkan daftar notifikasi milik pengguna.
     */
    public function index(Request $request): View
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $notifikasi = Notifikasi::query()
            ->with('produk')
            ->where('id_pengguna', $pengguna->getKey())
            ->byJenis($request->input('jenis'))
            ->byStatus($request->input('status'))
            ->byDateRange($request->input('tanggal_mulai'), $request->input('tanggal_selesai'))
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('notifikasi.index', [
            'notifikasi' => $notifikasi,
            'groups' => $notifikasi->getCollection()->groupBy(fn (Notifikasi $item): string => $this->groupLabel($item)),
            'filters' => $request->only(['jenis', 'status', 'tanggal_mulai', 'tanggal_selesai']),
        ]);
    }

    /**
     * Tandai notifikasi sebagai dibaca dari halaman daftar.
     */
    public function markAsRead(Request $request, Notifikasi $notifikasi): RedirectResponse|JsonResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $this->notifikasiService->markAsRead($notifikasi, $pengguna);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil ditandai sudah dibaca.',
                'data' => null,
            ]);
        }

        return back()->with('success', 'Notifikasi berhasil ditandai sudah dibaca.');
    }

    /**
     * Tandai semua notifikasi sebagai dibaca dari halaman daftar.
     */
    public function markAllAsRead(Request $request): RedirectResponse|JsonResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $total = $this->notifikasiService->markAllAsRead($pengguna);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi berhasil ditandai sudah dibaca.',
                'data' => ['updated' => $total],
            ]);
        }

        return back()->with('success', "{$total} notifikasi berhasil ditandai sudah dibaca.");
    }

    /**
     * Jalankan aksi massal notifikasi.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:notifikasi,id_notifikasi'],
            'action' => ['required', 'in:baca,hapus'],
        ], [
            'ids.required' => 'Pilih minimal satu notifikasi.',
            'action.in' => 'Aksi massal tidak valid.',
        ]);

        $total = $validated['action'] === 'hapus'
            ? $this->notifikasiService->deleteSelected($validated['ids'], $pengguna)
            : $this->notifikasiService->markSelectedAsRead($validated['ids'], $pengguna);

        return back()->with('success', "{$total} notifikasi berhasil diproses.");
    }

    /**
     * Hapus notifikasi, khusus Administrator.
     */
    public function destroy(Request $request, Notifikasi $notifikasi): RedirectResponse|JsonResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $this->notifikasiService->delete($notifikasi, $pengguna);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil dihapus.',
                'data' => null,
            ]);
        }

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Endpoint jumlah notifikasi belum dibaca.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Jumlah notifikasi belum dibaca berhasil diambil.',
            'data' => [
                'count' => $this->notifikasiService->unreadCount($pengguna),
            ],
        ]);
    }

    /**
     * Endpoint preview notifikasi belum dibaca.
     */
    public function preview(Request $request): JsonResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Preview notifikasi berhasil diambil.',
            'data' => [
                'count' => $this->notifikasiService->unreadCount($pengguna),
                'items' => $this->notifikasiService->unreadPreview($pengguna)->map(fn (Notifikasi $notifikasi): array => [
                    'id_notifikasi' => $notifikasi->id_notifikasi,
                    'jenis' => $notifikasi->jenis,
                    'pesan' => $notifikasi->pesan,
                    'produk' => $notifikasi->produk?->nama_produk,
                    'waktu' => $notifikasi->waktu_relatif,
                ]),
            ],
        ]);
    }

    /**
     * Kelompokkan notifikasi berdasarkan waktu.
     */
    private function groupLabel(Notifikasi $notifikasi): string
    {
        if ($notifikasi->created_at?->isToday()) {
            return 'Hari ini';
        }

        if ($notifikasi->created_at?->isYesterday()) {
            return 'Kemarin';
        }

        if ($notifikasi->created_at?->greaterThanOrEqualTo(now()->startOfWeek())) {
            return 'Minggu ini';
        }

        return 'Lebih lama';
    }
}
