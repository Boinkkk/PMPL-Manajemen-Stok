<?php

namespace App\Policies;

use App\Models\Pengguna; // atau User model Anda
use App\Models\ReturProduk;

class ReturProdukPolicy
{
    private function normalizeRole($user): string
    {
        if (isset($user->role) && $user->role?->nama_role) {
            return strtolower(str_replace([' ', '_'], '', $user->role->nama_role));
        }

        if (isset($user->id_role)) {
            return match ($user->id_role) {
                1 => 'admin',
                2 => 'stafgudang',
                3 => 'manajer',
                4 => 'distributor',
                default => 'unknown',
            };
        }

        return 'unknown';
    }

    public function viewAny($user)
    {
        $role = $this->normalizeRole($user);

        return in_array($role, ['admin', 'manajer', 'stafgudang', 'distributor'], true);
    }

    public function view($user, ReturProduk $retur)
    {
        $role = $this->normalizeRole($user);

        if (in_array($role, ['admin', 'manajer'], true)) {
            return true;
        }

        return $user->id_pengguna === $retur->id_pengguna;
    }

    public function create($user)
    {
        $role = $this->normalizeRole($user);

        return in_array($role, ['distributor', 'stafgudang'], true);
    }

    public function update($user, ReturProduk $retur)
    {
        $role = $this->normalizeRole($user);

        return $role === 'admin' && $retur->status === 'pending';
    }

    /**
     * Determine whether the user can delete the retur.
     */
    public function delete($user, ReturProduk $retur)
    {
        return $this->update($user, $retur);
    }

    public function setujui($user, ReturProduk $retur)
    {
        return $this->update($user, $retur);
    }

    public function tolak($user, ReturProduk $retur)
    {
        return $this->update($user, $retur);
    }
}