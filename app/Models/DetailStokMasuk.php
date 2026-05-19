<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_stok_masuk', 'id_produk', 'id_batch', 'jumlah', 'harga_beli', 'subtotal'])]
class DetailStokMasuk extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'detail_stok_masuk';

    protected $primaryKey = 'id_detail_masuk';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_detail_masuk' => 'integer',
            'id_stok_masuk' => 'integer',
            'id_produk' => 'integer',
            'id_batch' => 'integer',
            'jumlah' => 'integer',
            'harga_beli' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Header transaksi stok masuk.
     */
    public function stokMasuk(): BelongsTo
    {
        return $this->belongsTo(StokMasuk::class, 'id_stok_masuk', 'id_stok_masuk');
    }

    /**
     * Produk yang masuk.
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    /**
     * Batch produk yang masuk.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'id_batch', 'id_batch');
    }
}
