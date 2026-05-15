<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_stok_keluar', 'id_pengguna', 'id_distributor', 'id_order', 'nomor_transaksi', 'tanggal_keluar', 'catatan'])]
class StokKeluar extends Model
{
    use HasFactory;

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

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class, 'id_distributor', 'id_distributor');
    }

    public function orderDistribusi(): BelongsTo
    {
        return $this->belongsTo(OrderDistribusi::class, 'id_order', 'id_order');
    }

    public function detailStokKeluar(): HasMany
    {
        return $this->hasMany(DetailStokKeluar::class, 'id_stok_keluar', 'id_stok_keluar');
    }
}
