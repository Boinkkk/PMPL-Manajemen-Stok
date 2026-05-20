<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\Pengguna;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $audits = $this->auditQuery($request)
            ->paginate(10)
            ->appends($request->all());

        $penggunaList = Pengguna::query()
            ->when(Schema::hasColumn('pengguna', 'status'), function (Builder $query): void {
                $query->where('status', 'aktif');
            })
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
        $audits = $this->auditQuery($request)->get();
        $fileName = 'audit_trail_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($audits): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['ID', 'Waktu', 'Pengguna', 'Aktivitas', 'Modul', 'IP Address', 'Data Lama', 'Data Baru']);

            foreach ($audits as $audit) {
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
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        // composer require barryvdh/laravel-dompdf
        if (! class_exists(Pdf::class)) {
            abort(500, 'Paket barryvdh/laravel-dompdf belum terpasang.');
        }

        $audits = $this->auditQuery($request)->get();
        $fileName = 'audit_trail_'.now()->format('Ymd_His').'.pdf';

        return Pdf::loadView('audit.pdf', compact('audits'))
            ->download($fileName);
    }

    private function auditQuery(Request $request): Builder
    {
        return AuditTrail::with('pengguna')
            ->when($request->filled('id_pengguna'), function (Builder $query) use ($request): void {
                $query->where('id_pengguna', $request->input('id_pengguna'));
            })
            ->when(
                $request->filled('tanggal_mulai') && $request->filled('tanggal_selesai'),
                function (Builder $query) use ($request): void {
                    $query->whereBetween('waktu_aksi', [
                        $request->input('tanggal_mulai').' 00:00:00',
                        $request->input('tanggal_selesai').' 23:59:59',
                    ]);
                }
            )
            ->when($request->filled('aksi'), function (Builder $query) use ($request): void {
                $query->where('aksi', $request->input('aksi'));
            })
            ->orderByDesc('waktu_aksi');
    }
}
