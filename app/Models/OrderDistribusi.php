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
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['id_order', 'id_distributor', 'id_pengguna', 'nomor_order', 'tanggal_order', 'tanggal_diproses', 'status', 'catatan'])]
class OrderDistribusi extends Model
{
    use HasFactory;

    protected $table = 'order_distribusi';

    protected $primaryKey = 'id_order';

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
            'id_order' => 'integer',
            'id_distributor' => 'integer',
            'id_pengguna' => 'integer',
            'tanggal_order' => 'date',
            'tanggal_diproses' => 'date',
            'status' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Format tanggal order untuk tampilan Bahasa Indonesia.
     *
     * @return Attribute<string|null, never>
     */
    protected function tanggalOrderFormatted(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->tanggal_order?->locale('id')->translatedFormat('d F Y'),
        );
    }

    /**
     * Format tanggal proses untuk tampilan Bahasa Indonesia.
     *
     * @return Attribute<string|null, never>
     */
    protected function tanggalDiprosesFormatted(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->tanggal_diproses?->locale('id')->translatedFormat('d F Y'),
        );
    }

    /**
     * Format waktu dibuat untuk tampilan Bahasa Indonesia.
     *
     * @return Attribute<string|null, never>
     */
    protected function dibuatPadaFormatted(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->created_at?->locale('id')->translatedFormat('d F Y H:i'),
        );
    }

    /**
     * Filter order berdasarkan status.
     */
    #[Scope]
    protected function byStatus(Builder $query, ?string $status): void
    {
        $query->when($status, fn (Builder $query): Builder => $query->where('status', $status));
    }

    /**
     * Filter order berdasarkan distributor.
     */
    #[Scope]
    protected function byDistributor(Builder $query, int|string|null $idDistributor): void
    {
        $query->when($idDistributor, fn (Builder $query): Builder => $query->where('id_distributor', $idDistributor));
    }

    /**
     * Filter order berdasarkan rentang tanggal order.
     */
    #[Scope]
    protected function byDateRange(Builder $query, ?string $tanggalMulai, ?string $tanggalSelesai): void
    {
        $query
            ->when($tanggalMulai, fn (Builder $query): Builder => $query->whereDate('tanggal_order', '>=', $tanggalMulai))
            ->when($tanggalSelesai, fn (Builder $query): Builder => $query->whereDate('tanggal_order', '<=', $tanggalSelesai));
    }

    /**
     * Cari order berdasarkan nomor order atau distributor.
     */
    #[Scope]
    protected function search(Builder $query, ?string $keyword): void
    {
        $query->when($keyword, function (Builder $query, string $keyword): void {
            $query->where(function (Builder $query) use ($keyword): void {
                $query
                    ->where('nomor_order', 'like', "%{$keyword}%")
                    ->orWhereHas('distributor', fn (Builder $query): Builder => $query->where('nama_distributor', 'like', "%{$keyword}%"));
            });
        });
    }

    /**
     * Cek apakah order masih bisa diproses sebagai pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Total nilai order berdasarkan detail.
     */
    public function totalNilai(): float
    {
        return (float) $this->detailOrders->sum(fn (DetailOrder $detail): float => (float) $detail->subtotal);
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class, 'id_distributor', 'id_distributor');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function detailOrders(): HasMany
    {
        return $this->hasMany(DetailOrder::class, 'id_order', 'id_order');
    }

    public function stokKeluar(): HasMany
    {
        return $this->hasMany(StokKeluar::class, 'id_order', 'id_order');
    }

    /**
     * Transaksi stok keluar resmi yang dibuat dari order.
     */
    public function stokKeluarUtama(): HasOne
    {
        return $this->hasOne(StokKeluar::class, 'id_order', 'id_order');
    }
}
