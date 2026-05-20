<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_produk', 'permintaan_tahunan', 'biaya_pemesanan', 'biaya_penyimpanan', 'eoq'])]
class DataEoq extends Model
{
    public $timestamps = false;

    protected $table = 'data_eoq';

    protected $primaryKey = 'id_eoq';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_eoq' => 'integer',
            'id_produk' => 'integer',
            'permintaan_tahunan' => 'integer',
            'biaya_pemesanan' => 'integer',
            'biaya_penyimpanan' => 'integer',
            'eoq' => 'integer',
        ];
    }

    /**
     * Produk yang dihitung nilai EOQ-nya.
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
