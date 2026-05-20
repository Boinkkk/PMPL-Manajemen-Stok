<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SatuanController extends Controller
{
    /**
     * @param  array<string, mixed>|null  $dataLama
     * @param  array<string, mixed>|null  $dataBaru
     */
    private function recordAudit(Request $request, string $aksi, ?array $dataLama = null, ?array $dataBaru = null): void
    {
        AuditTrail::create([
            'aksi' => $aksi,
            'modul' => 'satuan',
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'ip_address' => $request->ip(),
            'waktu_aksi' => now(),
        ]);
    }

    public function index(Request $request)
    {
        $search = $request->query('search');

        $satuans = Satuan::when($search, function ($query, $search) {
            return $query->where('nama_satuan', 'like', "%{$search}%")
                ->orWhere('singkatan', 'like', "%{$search}%");
        })
            ->orderBy('created_at', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('satuan.index', compact('satuans', 'search'));
    }

    public function create()
    {
        return view('satuan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:255|unique:satuan,nama_satuan',
            'singkatan' => 'required|string|max:50|unique:satuan,singkatan',
        ], [
            'nama_satuan.required' => 'Nama satuan wajib diisi',
            'nama_satuan.unique' => 'Nama satuan sudah ada, gunakan nama lain',
            'singkatan.required' => 'Singkatan wajib diisi',
            'singkatan.unique' => 'Singkatan sudah ada, gunakan singkatan lain',
        ]);

        DB::transaction(function () use ($request, $validated): void {
            $satuan = Satuan::create($validated);

            $this->recordAudit($request, 'create', null, $satuan->only([
                'id_satuan',
                'nama_satuan',
                'singkatan',
            ]));
        });

        return redirect()->route('satuan.index')
            ->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit($id_satuan)
    {
        $satuan = Satuan::findOrFail($id_satuan);

        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, $id_satuan)
    {
        $satuan = Satuan::findOrFail($id_satuan);

        $validated = $request->validate([
            'nama_satuan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('satuan', 'nama_satuan')->ignore($satuan->id_satuan, 'id_satuan'),
            ],
            'singkatan' => [
                'required',
                'string',
                'max:50',
                Rule::unique('satuan', 'singkatan')->ignore($satuan->id_satuan, 'id_satuan'),
            ],
        ], [
            'nama_satuan.required' => 'Nama satuan wajib diisi',
            'nama_satuan.unique' => 'Nama satuan sudah ada, gunakan nama lain',
            'singkatan.required' => 'Singkatan wajib diisi',
            'singkatan.unique' => 'Singkatan sudah ada, gunakan singkatan lain',
        ]);

        DB::transaction(function () use ($satuan, $request, $validated): void {
            $dataLama = $satuan->only([
                'id_satuan',
                'nama_satuan',
                'singkatan',
            ]);

            $satuan->update($validated);

            $this->recordAudit($request, 'update', $dataLama, $satuan->only([
                'id_satuan',
                'nama_satuan',
                'singkatan',
            ]));
        });

        return redirect()->route('satuan.index')
            ->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Request $request, $id_satuan)
    {
        $satuan = Satuan::findOrFail($id_satuan);
        DB::transaction(function () use ($satuan, $request): void {
            $dataLama = $satuan->only([
                'id_satuan',
                'nama_satuan',
                'singkatan',
            ]);

            $satuan->delete();

            $this->recordAudit($request, 'delete', $dataLama);
        });

        return redirect()->route('satuan.index')
            ->with('success', 'Satuan berhasil dihapus.');
    }
}
