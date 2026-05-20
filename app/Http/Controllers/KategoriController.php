<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    /**
     * @param  array<string, mixed>|null  $dataLama
     * @param  array<string, mixed>|null  $dataBaru
     */
    private function recordAudit(Request $request, string $aksi, ?array $dataLama = null, ?array $dataBaru = null): void
    {
        AuditTrail::create([
            'aksi' => $aksi,
            'modul' => 'kategori',
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'ip_address' => $request->ip(),
            'waktu_aksi' => now(),
        ]);
    }

    public function index(Request $request)
    {
        $search = $request->query('search');

        $kategoris = Kategori::when($search, function ($query, $search) {
            return $query->where('nama_kategori', 'like', "%{$search}%");
        })
            ->orderBy('created_at', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('kategori.index', compact('kategoris', 'search'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $validated): void {
            $kategori = Kategori::create($validated);

            $this->recordAudit($request, 'create', null, $kategori->only([
                'id_kategori',
                'nama_kategori',
                'deskripsi',
            ]));
        });

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        DB::transaction(function () use ($kategori, $request, $validated): void {
            $dataLama = $kategori->only([
                'id_kategori',
                'nama_kategori',
                'deskripsi',
            ]);

            $kategori->update($validated);

            $this->recordAudit($request, 'update', $dataLama, $kategori->only([
                'id_kategori',
                'nama_kategori',
                'deskripsi',
            ]));
        });

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, Kategori $kategori)
    {
        DB::transaction(function () use ($kategori, $request): void {
            $dataLama = $kategori->only([
                'id_kategori',
                'nama_kategori',
                'deskripsi',
            ]);

            $kategori->delete();

            $this->recordAudit($request, 'delete', $dataLama);
        });

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
