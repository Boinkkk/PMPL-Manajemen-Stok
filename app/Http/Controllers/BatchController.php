<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\Batch;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BatchController extends Controller
{
    /**
     * @param  array<string, mixed>|null  $dataLama
     * @param  array<string, mixed>|null  $dataBaru
     */
    private function recordAudit(Request $request, string $aksi, ?array $dataLama = null, ?array $dataBaru = null): void
    {
        AuditTrail::create([
            'aksi' => $aksi,
            'modul' => 'batch',
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'ip_address' => $request->ip(),
            'waktu_aksi' => now(),
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function auditedColumns(): array
    {
        return [
            'id_batch',
            'id_produk',
            'nomor_batch',
            'tanggal_produksi',
            'tanggal_expired',
            'keterangan',
        ];
    }

    private function nextBatchId(): int
    {
        return ((int) Batch::max('id_batch')) + 1;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $batches = Batch::with('produk')
            ->when($search, function ($query, $search) {
                $query->where('nomor_batch', 'like', "%{$search}%")
                    ->orWhereHas('produk', function ($produkQuery) use ($search) {
                        $produkQuery->where('nama_produk', 'like', "%{$search}%")
                            ->orWhere('kode_produk', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('batch.index', compact('batches', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produks = Produk::orderBy('nama_produk')->get();

        return view('batch.create', compact('produks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_produk' => ['required', 'exists:produk,id_produk'],
            'nomor_batch' => [
                'required',
                'string',
                'max:50',
                Rule::unique('batch', 'nomor_batch')->where(fn ($query) => $query->where('id_produk', $request->input('id_produk'))),
            ],
            'tanggal_produksi' => ['nullable', 'date'],
            'tanggal_expired' => [
                'required',
                'date',
                Rule::when($request->filled('tanggal_produksi'), ['after_or_equal:tanggal_produksi']),
            ],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'id_produk.required' => 'Produk wajib dipilih',
            'id_produk.exists' => 'Produk yang dipilih tidak valid',
            'nomor_batch.required' => 'Nomor batch wajib diisi',
            'nomor_batch.unique' => 'Nomor batch sudah ada untuk produk ini',
            'tanggal_expired.required' => 'Tanggal expired wajib diisi',
            'tanggal_expired.after_or_equal' => 'Tanggal expired tidak boleh sebelum tanggal produksi',
        ]);

        DB::transaction(function () use ($request, $validated): void {
            $batch = Batch::create([
                ...$validated,
                'id_batch' => $this->nextBatchId(),
            ]);

            $this->recordAudit($request, 'create', null, $batch->only($this->auditedColumns()));
        });

        return redirect()->route('batch.index')->with('success', 'Batch berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Batch $batch)
    {
        $produks = Produk::orderBy('nama_produk')->get();

        return view('batch.edit', compact('batch', 'produks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'id_produk' => ['required', 'exists:produk,id_produk'],
            'nomor_batch' => [
                'required',
                'string',
                'max:50',
                Rule::unique('batch', 'nomor_batch')
                    ->where(fn ($query) => $query->where('id_produk', $request->input('id_produk')))
                    ->ignore($batch->id_batch, 'id_batch'),
            ],
            'tanggal_produksi' => ['nullable', 'date'],
            'tanggal_expired' => [
                'required',
                'date',
                Rule::when($request->filled('tanggal_produksi'), ['after_or_equal:tanggal_produksi']),
            ],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'id_produk.required' => 'Produk wajib dipilih',
            'id_produk.exists' => 'Produk yang dipilih tidak valid',
            'nomor_batch.required' => 'Nomor batch wajib diisi',
            'nomor_batch.unique' => 'Nomor batch sudah ada untuk produk ini',
            'tanggal_expired.required' => 'Tanggal expired wajib diisi',
            'tanggal_expired.after_or_equal' => 'Tanggal expired tidak boleh sebelum tanggal produksi',
        ]);

        DB::transaction(function () use ($batch, $request, $validated): void {
            $dataLama = $batch->only($this->auditedColumns());

            $batch->update($validated);

            $this->recordAudit($request, 'update', $dataLama, $batch->only($this->auditedColumns()));
        });

        return redirect()->route('batch.index')->with('success', 'Batch berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Batch $batch)
    {
        DB::transaction(function () use ($batch, $request): void {
            $dataLama = $batch->only($this->auditedColumns());

            $batch->delete();

            $this->recordAudit($request, 'delete', $dataLama);
        });

        return redirect()->route('batch.index')->with('success', 'Batch berhasil dihapus.');
    }
}
