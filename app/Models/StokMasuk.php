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

#[Fillable(['id_stok_masuk', 'id_supplier', 'id_pengguna', 'nomor_transaksi', 'tanggal_masuk', 'catatan'])]
class StokMasuk extends Model
{
    use HasFactory, SoftDeletes;

    public const UPDATED_AT = null;

    protected $table = 'stok_masuk';

    protected $primaryKey = 'id_stok_masuk';

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
            'id_stok_masuk' => 'integer',
            'id_supplier' => 'integer',
            'id_pengguna' => 'integer',
            'tanggal_masuk' => 'date',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Format tanggal masuk untuk tampilan Bahasa Indonesia.
     *
     * @return Attribute<string|null, never>
     */
    protected function tanggalMasukFormatted(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->tanggal_masuk?->locale('id')->translatedFormat('d F Y'),
        );
    }

    /**
     * Filter transaksi berdasarkan supplier.
     */
    #[Scope]
    protected function bySupplier(Builder $query, int|string|null $idSupplier): void
    {
        $query->when($idSupplier, fn (Builder $query): Builder => $query->where('id_supplier', $idSupplier));
    }

    /**
     * Filter transaksi berdasarkan rentang tanggal.
     */
    #[Scope]
    protected function byDateRange(Builder $query, ?string $tanggalMulai, ?string $tanggalSelesai): void
    {
        $query
            ->when($tanggalMulai, fn (Builder $query): Builder => $query->whereDate('tanggal_masuk', '>=', $tanggalMulai))
            ->when($tanggalSelesai, fn (Builder $query): Builder => $query->whereDate('tanggal_masuk', '<=', $tanggalSelesai));
    }

    /**
     * Cari transaksi berdasarkan nomor, supplier, pengguna, atau catatan.
     */
    #[Scope]
    protected function search(Builder $query, ?string $keyword): void
    {
        $query->when($keyword, function (Builder $query, string $keyword): void {
            $query->where(function (Builder $query) use ($keyword): void {
                $query
                    ->where('nomor_transaksi', 'like', "%{$keyword}%")
                    ->orWhere('catatan', 'like', "%{$keyword}%")
                    ->orWhereHas('supplier', fn (Builder $query): Builder => $query->where('nama_supplier', 'like', "%{$keyword}%"))
                    ->orWhereHas('pengguna', fn (Builder $query): Builder => $query->where('nama_lengkap', 'like', "%{$keyword}%"));
            });
        });
    }

    /**
     * Supplier asal stok masuk.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    /**
     * Pengguna pencatat stok masuk.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Detail produk stok masuk.
     */
    public function detailStokMasuk(): HasMany
    {
        return $this->hasMany(DetailStokMasuk::class, 'id_stok_masuk', 'id_stok_masuk');
    }
}
