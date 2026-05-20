<?php

namespace App\Http\Controllers;

use App\Exports\GenericReportExport;
use App\Models\Pengguna;
use App\Services\LaporanService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class LaporanController extends Controller
{
    /**
     * Buat controller laporan baru.
     */
    public function __construct(private readonly LaporanService $laporanService) {}

    /**
     * Tampilkan indeks laporan.
     */
    public function index(Request $request): View
    {
        $this->authorizeReports($request);

        return view('laporan.index', [
            'groups' => collect($this->laporanService->definitions())->groupBy('group'),
        ]);
    }

    /**
     * Tampilkan laporan.
     */
    public function show(Request $request, string $type): View
    {
        $this->authorizeReportType($request, $type);

        $perPage = min(100, max(10, (int) $request->input('per_page', 25)));
        $filters = $request->query();
        $report = $this->laporanService->report($type, $filters, $perPage);

        /** @var Pengguna|null $pengguna */
        $pengguna = $request->user();
        $this->laporanService->audit('BUKA_LAPORAN', $type, $filters, $pengguna, $request->ip());

        return view('laporan.show', [
            ...$report,
            ...$this->laporanService->filterOptions(),
            'type' => $type,
        ]);
    }

    /**
     * Ekspor laporan ke Excel.
     */
    public function excel(Request $request, string $type): Response|RedirectResponse
    {
        $this->authorizeExport($request);
        $this->authorizeReportType($request, $type);

        $filters = $request->query();
        $rows = $this->laporanService->exportRows($type, $filters);

        if ($rows->count() > 1000) {
            return back()->with('warning', 'Data laporan lebih dari 1000 baris. Skema database belum menyediakan tabel unduhan laporan, sehingga ekspor antrean belum diaktifkan.');
        }

        $definition = $this->laporanService->definition($type);
        $report = $this->laporanService->report($type, $filters, 25);

        /** @var Pengguna|null $pengguna */
        $pengguna = $request->user();
        $this->laporanService->audit('EKSPOR_EXCEL', $type, $filters, $pengguna, $request->ip(), [
            'jumlah_data' => $rows->count(),
        ]);

        return Excel::download(
            new GenericReportExport($this->meta($definition['title'], $filters, $pengguna), $report['columns'], $rows, $report['summary']),
            str($type)->append('-', now('Asia/Jakarta')->format('Ymd'), '.xlsx')->toString()
        );
    }

    /**
     * Ekspor laporan ke PDF.
     */
    public function pdf(Request $request, string $type): Response|RedirectResponse
    {
        $this->authorizeExport($request);
        $this->authorizeReportType($request, $type);

        $filters = $request->query();
        $rows = $this->laporanService->exportRows($type, $filters);

        if ($rows->count() > 1000) {
            return back()->with('warning', 'Data laporan lebih dari 1000 baris. Skema database belum menyediakan tabel unduhan laporan, sehingga ekspor antrean belum diaktifkan.');
        }

        $definition = $this->laporanService->definition($type);
        $report = $this->laporanService->report($type, $filters, 25);

        /** @var Pengguna|null $pengguna */
        $pengguna = $request->user();
        $this->laporanService->audit('EKSPOR_PDF', $type, $filters, $pengguna, $request->ip(), [
            'jumlah_data' => $rows->count(),
        ]);

        return app('dompdf.wrapper')
            ->loadView('laporan.pdf', [
                'title' => $definition['title'],
                'columns' => $report['columns'],
                'rows' => $rows,
                'summary' => $report['summary'],
                'meta' => $this->meta($definition['title'], $filters, $pengguna),
            ])
            ->setPaper('a4', 'landscape')
            ->download(str($type)->append('-', now('Asia/Jakarta')->format('Ymd'), '.pdf')->toString());
    }

    /**
     * Catat audit cetak laporan langsung.
     */
    public function print(Request $request, string $type): RedirectResponse
    {
        $this->authorizeExport($request);
        $this->authorizeReportType($request, $type);

        /** @var Pengguna|null $pengguna */
        $pengguna = $request->user();
        $this->laporanService->audit('CETAK_LAPORAN', $type, $request->query(), $pengguna, $request->ip());

        return redirect()->to(route('laporan.show', $type).'?'.http_build_query($request->query()))
            ->with('success', 'Gunakan tombol Cetak atau Ctrl+P pada halaman laporan.');
    }

    /**
     * Metadata export.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function meta(string $title, array $filters, ?Pengguna $pengguna): array
    {
        return [
            'Judul Laporan' => $title,
            'Filter' => $filters,
            'Waktu Cetak' => now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y H:i'),
            'Dicetak Oleh' => $pengguna?->nama_lengkap ?? '-',
        ];
    }

    /**
     * Otorisasi akses halaman laporan umum.
     */
    private function authorizeReports(Request $request): void
    {
        abort_unless($request->user()?->hasAnyRole(['Administrator', 'Manajer']), 403, 'Staf Gudang tidak dapat mengakses halaman laporan.');
    }

    /**
     * Otorisasi tipe laporan.
     */
    private function authorizeReportType(Request $request, string $type): void
    {
        if ($type === 'stok-kedaluwarsa') {
            abort_unless($request->user()?->hasAnyRole(['Administrator', 'Staf Gudang', 'Manajer']), 403);

            return;
        }

        $this->authorizeReports($request);
    }

    /**
     * Otorisasi export/cetak.
     */
    private function authorizeExport(Request $request): void
    {
        abort_unless($request->user()?->canExportOrders(), 403, 'Ekspor laporan hanya tersedia untuk Administrator dan Manajer.');
    }
}
