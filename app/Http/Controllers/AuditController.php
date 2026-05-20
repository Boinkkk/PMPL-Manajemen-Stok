<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\Pengguna;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $audits = $this->filteredAuditQuery($request)
            ->orderByDesc('waktu_aksi')
            ->paginate(10)
            ->appends($request->all());

        $penggunaList = Pengguna::query()
            ->when(
                Schema::hasColumn((new Pengguna)->getTable(), 'status'),
                fn (Builder $query): Builder => $query->where('status', 'aktif')
            )
            ->orderBy('nama_lengkap')
            ->get();

        $aksiList = AuditTrail::query()
            ->select('aksi')
            ->distinct()
            ->orderBy('aksi')
            ->pluck('aksi');

        return view('audit.index', compact('audits', 'penggunaList', 'aksiList'));
    }

    public function detail(int $id): JsonResponse
    {
        $audit = AuditTrail::with('pengguna')->findOrFail($id);

        return response()->json([
            'id' => $audit->id_audit,
            'pengguna' => $audit->pengguna?->nama_lengkap ?? '-',
            'modul' => $audit->modul,
            'aktivitas' => $audit->aksi,
            'data_lama' => $audit->data_lama ?? [],
            'data_baru' => $audit->data_baru ?? [],
            'waktu' => $audit->waktu_aksi?->format('d-m-Y H:i:s'),
            'ip' => $audit->ip_address,
            'status' => 'Berhasil',
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $fileName = 'audit_trail_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->streamDownload(function () use ($request): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['ID', 'Waktu', 'Pengguna', 'Aktivitas', 'Modul', 'IP Address', 'Data Lama', 'Data Baru']);

            $this->filteredAuditQuery($request)
                ->orderByDesc('waktu_aksi')
                ->get()
                ->each(function (AuditTrail $audit) use ($handle): void {
                    fputcsv($handle, [
                        $audit->id_audit,
                        $audit->waktu_aksi?->format('d-m-Y H:i:s'),
                        $audit->pengguna?->nama_lengkap ?? '-',
                        $audit->aksi,
                        $audit->modul,
                        $audit->ip_address,
                        json_encode($audit->data_lama ?? [], JSON_UNESCAPED_UNICODE),
                        json_encode($audit->data_baru ?? [], JSON_UNESCAPED_UNICODE),
                    ]);
                });

            fclose($handle);
        }, $fileName, $headers);
    }

    public function exportPdf(Request $request): Response
    {
        $audits = $this->filteredAuditQuery($request)
            ->orderByDesc('waktu_aksi')
            ->get();

        // composer require barryvdh/laravel-dompdf
        $pdf = Pdf::loadView('audit.pdf', compact('audits'))->setPaper('a4', 'landscape');

        return $pdf->download('audit_trail_'.now()->format('Ymd_His').'.pdf');
    }

    /**
     * @return Builder<AuditTrail>
     */
    private function filteredAuditQuery(Request $request): Builder
    {
        return AuditTrail::with('pengguna')
            ->when($request->filled('id_pengguna'), function (Builder $query) use ($request): Builder {
                return $query->where('id_pengguna', $request->integer('id_pengguna'));
            })
            ->when(
                $request->filled('tanggal_mulai') && $request->filled('tanggal_selesai'),
                function (Builder $query) use ($request): Builder {
                    return $query->whereBetween('waktu_aksi', [
                        $request->date('tanggal_mulai')->startOfDay(),
                        $request->date('tanggal_selesai')->endOfDay(),
                    ]);
                }
            )
            ->when($request->filled('aksi'), function (Builder $query) use ($request): Builder {
                return $query->where('aksi', $request->string('aksi')->toString());
            });
    }
}
