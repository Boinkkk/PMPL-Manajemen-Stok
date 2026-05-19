<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Collection;

class NotifikasiService
{
    /**
     * Hitung notifikasi belum dibaca milik pengguna.
     */
    public function unreadCount(Pengguna $pengguna): int
    {
        return Notifikasi::query()
            ->where('id_pengguna', $pengguna->getKey())
            ->where('status', 'belum_dibaca')
            ->count();
    }

    /**
     * Ambil lima notifikasi terbaru yang belum dibaca.
     *
     * @return Collection<int, Notifikasi>
     */
    public function unreadPreview(Pengguna $pengguna): Collection
    {
        return Notifikasi::query()
            ->with('produk')
            ->where('id_pengguna', $pengguna->getKey())
            ->where('status', 'belum_dibaca')
            ->latest('created_at')
            ->limit(5)
            ->get();
    }

    /**
     * Tandai satu notifikasi sebagai dibaca.
     */
    public function markAsRead(Notifikasi $notifikasi, Pengguna $pengguna): void
    {
        abort_unless((int) $notifikasi->id_pengguna === (int) $pengguna->getKey(), 403, 'Notifikasi tidak dapat diakses.');

        $notifikasi->update(['status' => 'dibaca']);
    }

    /**
     * Tandai semua notifikasi pengguna sebagai dibaca.
     */
    public function markAllAsRead(Pengguna $pengguna): int
    {
        return Notifikasi::query()
            ->where('id_pengguna', $pengguna->getKey())
            ->where('status', 'belum_dibaca')
            ->update(['status' => 'dibaca']);
    }

    /**
     * Hapus notifikasi milik pengguna tertentu.
     */
    public function delete(Notifikasi $notifikasi, Pengguna $pengguna): void
    {
        abort_unless($pengguna->isAdministrator(), 403, 'Hanya Administrator yang dapat menghapus notifikasi.');

        $notifikasi->delete();
    }

    /**
     * Tandai pilihan notifikasi sebagai dibaca.
     *
     * @param  array<int, int>  $ids
     */
    public function markSelectedAsRead(array $ids, Pengguna $pengguna): int
    {
        return Notifikasi::query()
            ->where('id_pengguna', $pengguna->getKey())
            ->whereIn('id_notifikasi', $ids)
            ->update(['status' => 'dibaca']);
    }

    /**
     * Hapus pilihan notifikasi, hanya Administrator.
     *
     * @param  array<int, int>  $ids
     */
    public function deleteSelected(array $ids, Pengguna $pengguna): int
    {
        abort_unless($pengguna->isAdministrator(), 403, 'Hanya Administrator yang dapat menghapus notifikasi.');

        return Notifikasi::query()
            ->whereIn('id_notifikasi', $ids)
            ->delete();
    }
}
