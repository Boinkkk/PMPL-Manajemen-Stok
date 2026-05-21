<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_stok_keluar', 'id_produk', 'id_batch', 'jumlah', 'harga_jual', 'subtotal'])]
class DetailStokKeluar extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'detail_stok_keluar';

    protected $primaryKey = 'id_detail_keluar';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_detail_keluar' => 'integer',
            'id_stok_keluar' => 'integer',
            'id_produk' => 'integer',
            'id_batch' => 'integer',
            'jumlah' => 'integer',
            'harga_jual' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Header transaksi stok keluar.
     */
    public function stokKeluar(): BelongsTo
    {
        return $this->belongsTo(StokKeluar::class, 'id_stok_keluar', 'id_stok_keluar');
    }

    /**
     * Produk yang keluar.
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    /**
     * Batch produk yang keluar.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'id_batch', 'id_batch');
    }
}
