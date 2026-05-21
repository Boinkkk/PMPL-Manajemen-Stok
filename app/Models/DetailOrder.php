<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_order', 'id_produk', 'jumlah_diminta', 'jumlah_disetujui', 'harga_satuan', 'subtotal', 'catatan'])]
class DetailOrder extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'detail_order';

    protected $primaryKey = 'id_detail_order';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_detail_order' => 'integer',
            'id_order' => 'integer',
            'id_produk' => 'integer',
            'jumlah_diminta' => 'integer',
            'jumlah_disetujui' => 'integer',
            'harga_satuan' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Hitung subtotal detail dari jumlah diminta dan harga satuan.
     */
    public function hitungSubtotal(): float
    {
        return (float) $this->jumlah_diminta * (float) $this->harga_satuan;
    }

    public function orderDistribusi(): BelongsTo
    {
        return $this->belongsTo(OrderDistribusi::class, 'id_order', 'id_order');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
