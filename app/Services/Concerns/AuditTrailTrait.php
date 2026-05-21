<?php

namespace App\Services\Concerns;

use App\Models\AuditTrail;
use App\Models\Pengguna;

trait AuditTrailTrait
{
    /**
     * Simpan catatan audit trail untuk aksi sensitif pengguna.
     *
     * @param  array<string, mixed>|null  $dataLama
     * @param  array<string, mixed>|null  $dataBaru
     */
    public function simpanAuditTrail(
        string $aksi,
        string $modul,
        ?array $dataLama,
        ?array $dataBaru,
        ?string $ipAddress,
        ?Pengguna $pengguna = null
    ): AuditTrail {
        return AuditTrail::query()->create([
            'id_pengguna' => $pengguna?->getKey(),
            'aksi' => $aksi,
            'modul' => $modul,
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'ip_address' => $ipAddress,
        ]);
    }
}
