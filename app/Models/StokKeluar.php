<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['id_stok_keluar', 'id_pengguna', 'id_distributor', 'id_order', 'nomor_transaksi', 'tanggal_keluar', 'catatan'])]
class StokKeluar extends Model
{
    use HasFactory, SoftDeletes;

    public const UPDATED_AT = null;

    protected $table = 'stok_keluar';

    protected $primaryKey = 'id_stok_keluar';

    public $incrementing = false;

    protected $keyType = 'int';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_stok_keluar' => 'integer',
            'id_pengguna' => 'integer',
            'id_distributor' => 'integer',
            'id_order' => 'integer',
            'tanggal_keluar' => 'date',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Format tanggal keluar untuk tampilan Bahasa Indonesia.
     *
     * @return Attribute<string|null, never>
     */
    protected function tanggalKeluarFormatted(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->tanggal_keluar?->locale('id')->translatedFormat('d F Y'),
        );
    }

    /**
     * Filter transaksi berdasarkan distributor.
     */
    #[Scope]
    protected function byDistributor(Builder $query, int|string|null $idDistributor): void
    {
        $query->when($idDistributor, fn (Builder $query): Builder => $query->where('id_distributor', $idDistributor));
    }

    /**
     * Filter transaksi berdasarkan rentang tanggal.
     */
    #[Scope]
    protected function byDateRange(Builder $query, ?string $tanggalMulai, ?string $tanggalSelesai): void
    {
        $query
            ->when($tanggalMulai, fn (Builder $query): Builder => $query->whereDate('tanggal_keluar', '>=', $tanggalMulai))
            ->when($tanggalSelesai, fn (Builder $query): Builder => $query->whereDate('tanggal_keluar', '<=', $tanggalSelesai));
    }

    /**
     * Cari transaksi berdasarkan nomor, distributor, pengguna, order, atau catatan.
     */
    #[Scope]
    protected function search(Builder $query, ?string $keyword): void
    {
        $query->when($keyword, function (Builder $query, string $keyword): void {
            $query->where(function (Builder $query) use ($keyword): void {
                $query
                    ->where('nomor_transaksi', 'like', "%{$keyword}%")
                    ->orWhere('catatan', 'like', "%{$keyword}%")
                    ->orWhereHas('distributor', fn (Builder $query): Builder => $query->where('nama_distributor', 'like', "%{$keyword}%"))
                    ->orWhereHas('pengguna', fn (Builder $query): Builder => $query->where('nama_lengkap', 'like', "%{$keyword}%"))
                    ->orWhereHas('orderDistribusi', fn (Builder $query): Builder => $query->where('nomor_order', 'like', "%{$keyword}%"));
            });
        });
    }

    /**
     * Pengguna pencatat stok keluar.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Distributor tujuan stok keluar.
     */
    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class, 'id_distributor', 'id_distributor');
    }

    /**
     * Order distribusi terkait jika ada.
     */
    public function orderDistribusi(): BelongsTo
    {
        return $this->belongsTo(OrderDistribusi::class, 'id_order', 'id_order');
    }

    /**
     * Detail produk stok keluar.
     */
    public function detailStokKeluar(): HasMany
    {
        return $this->hasMany(DetailStokKeluar::class, 'id_stok_keluar', 'id_stok_keluar');
    }
}
