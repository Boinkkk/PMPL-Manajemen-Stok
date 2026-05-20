<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProdukController extends Controller
{
    /**
     * @param  array<string, mixed>|null  $dataLama
     * @param  array<string, mixed>|null  $dataBaru
     */
    private function recordAudit(Request $request, string $aksi, ?array $dataLama = null, ?array $dataBaru = null): void
    {
        AuditTrail::create([
            'aksi' => $aksi,
            'modul' => 'produk',
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
            'id_produk',
            'kode_produk',
            'nama_produk',
            'id_kategori',
            'id_satuan',
            'harga_satuan',
            'stok_terkini',
            'stok_minimum',
            'deskripsi',
        ];
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $kategoriFilter = $request->query('kategori');

        $query = Produk::with([
            'kategori',
            'satuan',
            'batches' => fn ($query) => $query->orderBy('tanggal_expired', 'asc'),
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_produk', 'like', "%{$search}%");
            });
        }

        if ($kategoriFilter) {
            $query->where('id_kategori', $kategoriFilter);
        }

        $produks = $query->orderBy('created_at', 'asc')
            ->paginate(10)
            ->withQueryString();

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        // statistics
        $totalProduk = Produk::count();
        $totalStok = Produk::sum('stok_terkini');
        $produkMenipis = Produk::whereColumn('stok_terkini', '<=', 'stok_minimum')->count();
        $produkHabis = Produk::where('stok_terkini', 0)->count();

        return view('produk.index', compact('produks', 'kategoris', 'search', 'kategoriFilter', 'totalProduk', 'totalStok', 'produkMenipis', 'produkHabis'));
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $satuans = Satuan::orderBy('nama_satuan')->get();
        $generatedKode = Produk::generateKode();

        return view('produk.create', compact('kategoris', 'satuans', 'generatedKode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_produk' => 'required|string|max:100|unique:produk,kode_produk',
            'nama_produk' => 'required|string|max:255',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'id_satuan' => 'required|exists:satuan,id_satuan',
            'harga_satuan' => 'required|numeric|min:0',
            'stok_terkini' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ], [
            'kode_produk.required' => 'Kode produk wajib diisi',
            'kode_produk.unique' => 'Kode produk sudah ada, gunakan kode lain',
            'nama_produk.required' => 'Nama produk wajib diisi',
            'id_kategori.required' => 'Kategori wajib dipilih',
            'id_kategori.exists' => 'Kategori yang dipilih tidak valid',
            'id_satuan.required' => 'Satuan wajib dipilih',
            'id_satuan.exists' => 'Satuan yang dipilih tidak valid',
            'harga_satuan.required' => 'Harga satuan wajib diisi',
            'harga_satuan.numeric' => 'Harga satuan harus berupa angka',
            'harga_satuan.min' => 'Harga satuan minimal 0',
            'stok_terkini.required' => 'Stok terkini wajib diisi',
            'stok_terkini.integer' => 'Stok terkini harus berupa angka',
            'stok_terkini.min' => 'Stok terkini minimal 0',
            'stok_minimum.required' => 'Stok minimum wajib diisi',
            'stok_minimum.integer' => 'Stok minimum harus berupa angka',
            'stok_minimum.min' => 'Stok minimum minimal 0',
        ]);

        DB::transaction(function () use ($request, $validated): void {
            $produk = Produk::create($validated);

            $this->recordAudit($request, 'create', null, $produk->only($this->auditedColumns()));
        });

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show($id_produk)
    {
        $produk = Produk::with(['kategori', 'satuan'])->findOrFail($id_produk);

        return view('produk.show', compact('produk'));
    }

    public function edit($id_produk)
    {
        $produk = Produk::findOrFail($id_produk);
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $satuans = Satuan::orderBy('nama_satuan')->get();

        return view('produk.edit', compact('produk', 'kategoris', 'satuans'));
    }

    public function update(Request $request, $id_produk)
    {
        $produk = Produk::findOrFail($id_produk);

        $validated = $request->validate([
            'kode_produk' => [
                'required', 'string', 'max:100',
                Rule::unique('produk', 'kode_produk')->ignore($produk->id_produk, 'id_produk'),
            ],
            'nama_produk' => 'required|string|max:255',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'id_satuan' => 'required|exists:satuan,id_satuan',
            'harga_satuan' => 'required|numeric|min:0',
            'stok_terkini' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ], [
            'kode_produk.required' => 'Kode produk wajib diisi',
            'kode_produk.unique' => 'Kode produk sudah ada, gunakan kode lain',
            'nama_produk.required' => 'Nama produk wajib diisi',
            'id_kategori.required' => 'Kategori wajib dipilih',
            'id_kategori.exists' => 'Kategori yang dipilih tidak valid',
            'id_satuan.required' => 'Satuan wajib dipilih',
            'id_satuan.exists' => 'Satuan yang dipilih tidak valid',
            'harga_satuan.required' => 'Harga satuan wajib diisi',
            'harga_satuan.numeric' => 'Harga satuan harus berupa angka',
            'harga_satuan.min' => 'Harga satuan minimal 0',
            'stok_terkini.required' => 'Stok terkini wajib diisi',
            'stok_terkini.integer' => 'Stok terkini harus berupa angka',
            'stok_terkini.min' => 'Stok terkini minimal 0',
            'stok_minimum.required' => 'Stok minimum wajib diisi',
            'stok_minimum.integer' => 'Stok minimum harus berupa angka',
            'stok_minimum.min' => 'Stok minimum minimal 0',
        ]);

        DB::transaction(function () use ($produk, $request, $validated): void {
            $dataLama = $produk->only($this->auditedColumns());

            $produk->update($validated);

            $this->recordAudit($request, 'update', $dataLama, $produk->only($this->auditedColumns()));
        });

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Request $request, $id_produk)
    {
        $produk = Produk::findOrFail($id_produk);
        DB::transaction(function () use ($produk, $request): void {
            $dataLama = $produk->only($this->auditedColumns());

            $produk->delete();

            $this->recordAudit($request, 'delete', $dataLama);
        });

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}
