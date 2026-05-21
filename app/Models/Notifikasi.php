<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_produk', 'id_pengguna', 'jenis', 'pesan', 'status'])]
class Notifikasi extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $table = 'notifikasi';

    protected $primaryKey = 'id_notifikasi';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_notifikasi' => 'integer',
            'id_produk' => 'integer',
            'id_pengguna' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Format waktu relatif untuk tampilan.
     *
     * @return Attribute<string|null, never>
     */
    protected function waktuRelatif(): Attribute
    {
        return Attribute::get(function (): ?string {
            if ($this->created_at === null) {
                return null;
            }

            if ($this->created_at->isToday()) {
                return $this->created_at->locale('id')->diffForHumans();
            }

            if ($this->created_at->isYesterday()) {
                return 'kemarin';
            }

            return $this->created_at->locale('id')->translatedFormat('d M Y');
        });
    }

    /**
     * Filter notifikasi berdasarkan jenis.
     */
    #[Scope]
    protected function byJenis(Builder $query, ?string $jenis): void
    {
        $query->when($jenis, fn (Builder $query): Builder => $query->where('jenis', $jenis));
    }

    /**
     * Filter notifikasi berdasarkan status baca.
     */
    #[Scope]
    protected function byStatus(Builder $query, ?string $status): void
    {
        $query->when($status, fn (Builder $query): Builder => $query->where('status', $status));
    }

    /**
     * Filter notifikasi berdasarkan rentang tanggal.
     */
    #[Scope]
    protected function byDateRange(Builder $query, ?string $tanggalMulai, ?string $tanggalSelesai): void
    {
        $query
            ->when($tanggalMulai, fn (Builder $query): Builder => $query->whereDate('created_at', '>=', $tanggalMulai))
            ->when($tanggalSelesai, fn (Builder $query): Builder => $query->whereDate('created_at', '<=', $tanggalSelesai));
    }

    /**
     * Cek status belum dibaca.
     */
    public function isUnread(): bool
    {
        return $this->status === 'belum_dibaca';
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}
