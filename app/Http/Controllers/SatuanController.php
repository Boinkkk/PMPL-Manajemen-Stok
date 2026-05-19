<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SatuanController extends Controller
{
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

        Satuan::create($validated);

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

        $satuan->update($validated);

        return redirect()->route('satuan.index')
            ->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy($id_satuan)
    {
        $satuan = Satuan::findOrFail($id_satuan);
        $satuan->delete();

        return redirect()->route('satuan.index')
            ->with('success', 'Satuan berhasil dihapus.');
    }
}
