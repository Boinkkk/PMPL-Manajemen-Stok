<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePenggunaRequest;
use App\Http\Requests\UpdatePenggunaRequest;
use App\Models\Pengguna;
use App\Models\Role;
use App\Services\PenggunaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    /**
     * Buat controller pengguna baru.
     */
    public function __construct(private readonly PenggunaService $penggunaService) {}

    /**
     * Tampilkan daftar pengguna dengan filter.
     */
    public function index(Request $request): View
    {
        $pengguna = Pengguna::query()
            ->with('role')
            ->byRole($request->input('id_role'))
            ->byStatus($request->input('status'))
            ->search($request->input('q'))
            ->orderBy('nama_lengkap')
            ->paginate(15)
            ->withQueryString();

        return view('pengguna.index', [
            'pengguna' => $pengguna,
            'roles' => Role::query()->orderBy('nama_role')->get(),
            'filters' => $request->only(['id_role', 'status', 'q']),
        ]);
    }

    /**
     * Tampilkan form tambah pengguna.
     */
    public function create(): View
    {
        return view('pengguna.create', [
            'roles' => Role::query()->orderBy('nama_role')->get(),
        ]);
    }

    /**
     * Simpan pengguna baru.
     */
    public function store(StorePenggunaRequest $request): RedirectResponse
    {
        $this->penggunaService->store($request);

        return redirect()
            ->route('pengguna.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit pengguna.
     */
    public function edit(Pengguna $pengguna): View
    {
        return view('pengguna.edit', [
            'pengguna' => $pengguna->load('role'),
            'roles' => Role::query()->orderBy('nama_role')->get(),
            'isSelf' => (int) auth()->id() === (int) $pengguna->getKey(),
        ]);
    }

    /**
     * Perbarui data pengguna.
     */
    public function update(UpdatePenggunaRequest $request, Pengguna $pengguna): RedirectResponse
    {
        $this->penggunaService->update($request, $pengguna);

        return redirect()
            ->route('pengguna.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Nonaktifkan akun pengguna.
     */
    public function nonaktifkan(Request $request, Pengguna $pengguna): RedirectResponse
    {
        /** @var Pengguna $admin */
        $admin = $request->user();

        $this->penggunaService->nonaktifkan($pengguna, $admin, $request);

        return redirect()
            ->route('pengguna.index')
            ->with('success', 'Pengguna berhasil dinonaktifkan.');
    }
}
