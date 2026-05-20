<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $suppliers = Supplier::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nama_supplier', 'like', '%'.$search.'%')
                    ->orWhere('kode_supplier', 'like', '%'.$search.'%')
                    ->orWhere('alamat', 'like', '%'.$search.'%')
                    ->orWhere('telepon', 'like', '%'.$search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.supplier.index', compact('search', 'suppliers'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'kode_supplier' => 'required|string|max:20|unique:supplier',
            'nama_supplier' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'kontak_person' => 'nullable|string|max:100',
        ]);

        $validated['id_supplier'] = (Supplier::max('id_supplier') ?? 0) + 1;

        Supplier::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Supplier berhasil ditambahkan']);
        }

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan');
    }

    public function show(int $id): JsonResponse|RedirectResponse
    {
        $supplier = Supplier::findOrFail($id);

        // Ambil produk yang pernah dibeli dari supplier ini melalui stok_masuk
        $produkList = DB::table('produk')
            ->join('detail_stok_masuk', 'produk.id_produk', '=', 'detail_stok_masuk.id_produk')
            ->join('stok_masuk', 'detail_stok_masuk.id_stok_masuk', '=', 'stok_masuk.id_stok_masuk')
            ->where('stok_masuk.id_supplier', $id)
            ->select('produk.nama_produk', 'produk.kode_produk')
            ->distinct()
            ->get();

        if (request()->ajax()) {
            return response()->json([
                'id_supplier' => $supplier->id_supplier,
                'kode_supplier' => $supplier->kode_supplier,
                'nama_supplier' => $supplier->nama_supplier,
                'alamat' => $supplier->alamat,
                'telepon' => $supplier->telepon,
                'email' => $supplier->email,
                'kontak_person' => $supplier->kontak_person,
                'produk' => $produkList,
            ]);
        }

        return redirect()->route('supplier.index');
    }

    public function update(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'kode_supplier' => 'required|string|max:20|unique:supplier,kode_supplier,'.$id.',id_supplier',
            'nama_supplier' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'kontak_person' => 'nullable|string|max:100',
        ]);

        $supplier->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Supplier berhasil diupdate']);
        }

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diupdate');
    }

    public function destroy(int $id): JsonResponse|RedirectResponse
    {
        $supplier = Supplier::findOrFail($id);

        // Cek apakah supplier memiliki relasi stok_masuk
        if ($supplier->stokMasuk()->count() > 0) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Supplier tidak dapat dihapus karena memiliki transaksi stok masuk'], 400);
            }

            return back()->with('error', 'Supplier tidak dapat dihapus karena memiliki transaksi stok masuk');
        }

        $supplier->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Supplier berhasil dihapus']);
        }

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus');
    }

    public function getAllProduk(): JsonResponse
    {
        // Ambil semua produk yang terhubung dengan supplier melalui stok_masuk
        $suppliersWithProduk = DB::table('supplier')
            ->leftJoin('stok_masuk', 'supplier.id_supplier', '=', 'stok_masuk.id_supplier')
            ->leftJoin('detail_stok_masuk', 'stok_masuk.id_stok_masuk', '=', 'detail_stok_masuk.id_stok_masuk')
            ->leftJoin('produk', 'detail_stok_masuk.id_produk', '=', 'produk.id_produk')
            ->select(
                'supplier.id_supplier',
                'supplier.nama_supplier',
                'produk.id_produk',
                'produk.nama_produk',
                'produk.kode_produk'
            )
            ->get()
            ->groupBy('id_supplier')
            ->map(function ($items, $key) {
                return [
                    'id_supplier' => $key,
                    'nama_supplier' => $items->first()->nama_supplier,
                    'produk' => $items->filter(function ($item) {
                        return ! is_null($item->id_produk);
                    })->map(function ($item) {
                        return $item->nama_produk;
                    })->values()->toArray(),
                ];
            })
            ->values();

        return response()->json($suppliersWithProduk);
    }

    // Untuk menampilkan form tambah supplier
    public function create(): RedirectResponse
    {
        return redirect()->route('supplier.index');
    }

    // Untuk menampilkan form edit supplier
    public function edit(int $id): RedirectResponse
    {
        Supplier::findOrFail($id);

        return redirect()->route('supplier.index');
    }
}
