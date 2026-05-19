<?php

namespace App\Services;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StorePenggunaRequest;
use App\Http\Requests\UpdatePenggunaRequest;
use App\Models\Pengguna;
use App\Services\Concerns\AuditTrailTrait;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PenggunaService
{
    use AuditTrailTrait;

    /**
     * Buat service manajemen pengguna baru.
     */
    public function __construct(
        private readonly DatabaseManager $database,
        private readonly Hasher $hasher,
    ) {}

    /**
     * Simpan pengguna baru dan catat audit trail.
     */
    public function store(StorePenggunaRequest $request): Pengguna
    {
        /** @var Pengguna $admin */
        $admin = $request->user();

        return $this->database->transaction(function () use ($request, $admin): Pengguna {
            $data = $request->validated();
            $data['id_pengguna'] = $this->nextPrimaryKey();
            $data['password'] = $this->hasher->make($data['password']);
            unset($data['password_confirmation']);

            $pengguna = Pengguna::query()->create($data);
            $pengguna->load('role');

            $this->simpanAuditTrail(
                'TAMBAH_USER',
                'pengguna',
                null,
                $this->auditDataPengguna($pengguna),
                $request->ip(),
                $admin
            );

            return $pengguna;
        });
    }

    /**
     * Simpan pengguna dari form register publik khusus testing.
     */
    public function register(RegisterRequest $request): Pengguna
    {
        return $this->database->transaction(function () use ($request): Pengguna {
            $data = $request->validated();
            $data['id_pengguna'] = $this->nextPrimaryKey();
            $data['password'] = $this->hasher->make($data['password']);
            $data['status'] = 'aktif';
            unset($data['password_confirmation']);

            $pengguna = Pengguna::query()->create($data);
            $pengguna->load('role');

            $this->simpanAuditTrail(
                'TAMBAH_USER',
                'pengguna',
                null,
                $this->auditDataPengguna($pengguna),
                $request->ip(),
                null
            );

            return $pengguna;
        });
    }

    /**
     * Perbarui pengguna dan catat audit trail.
     */
    public function update(UpdatePenggunaRequest $request, Pengguna $pengguna): Pengguna
    {
        /** @var Pengguna $admin */
        $admin = $request->user();

        return $this->database->transaction(function () use ($request, $pengguna, $admin): Pengguna {
            $pengguna->load('role');
            $dataLama = $this->auditDataPengguna($pengguna);
            $data = $request->validated();

            if ((int) $admin->getKey() === (int) $pengguna->getKey()) {
                $data['id_role'] = $pengguna->id_role;
                $data['status'] = $pengguna->status;
            }

            if (blank($data['password'] ?? null)) {
                unset($data['password']);
            } else {
                $data['password'] = $this->hasher->make($data['password']);
            }

            unset($data['password_confirmation']);

            $pengguna->fill($data);
            $pengguna->save();
            $pengguna->refresh()->load('role');

            $this->simpanAuditTrail(
                'UBAH_USER',
                'pengguna',
                $dataLama,
                $this->auditDataPengguna($pengguna),
                $request->ip(),
                $admin
            );

            return $pengguna;
        });
    }

    /**
     * Nonaktifkan pengguna tanpa menghapus data permanen.
     */
    public function nonaktifkan(Pengguna $pengguna, Pengguna $admin, Request $request): void
    {
        if ((int) $pengguna->getKey() === (int) $admin->getKey()) {
            throw ValidationException::withMessages([
                'pengguna' => 'Administrator tidak dapat menonaktifkan akunnya sendiri.',
            ]);
        }

        $this->database->transaction(function () use ($pengguna, $admin, $request): void {
            $pengguna->load('role');
            $dataLama = $this->auditDataPengguna($pengguna);

            $pengguna->forceFill(['status' => 'nonaktif'])->save();
            $pengguna->refresh()->load('role');

            $this->simpanAuditTrail(
                'NONAKTIFKAN_USER',
                'pengguna',
                $dataLama,
                $this->auditDataPengguna($pengguna),
                $request->ip(),
                $admin
            );
        });
    }

    /**
     * Buat primary key manual untuk tabel pengguna.
     */
    private function nextPrimaryKey(): int
    {
        return ((int) Pengguna::query()->lockForUpdate()->max('id_pengguna')) + 1;
    }

    /**
     * Ambil data aman pengguna untuk audit trail.
     *
     * @return array<string, mixed>
     */
    private function auditDataPengguna(Pengguna $pengguna): array
    {
        return [
            'id_pengguna' => $pengguna->id_pengguna,
            'id_role' => $pengguna->id_role,
            'nama_role' => $pengguna->role?->nama_role,
            'nama_lengkap' => $pengguna->nama_lengkap,
            'username' => $pengguna->username,
            'email' => $pengguna->email,
            'status' => $pengguna->status,
        ];
    }
}
