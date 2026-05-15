<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_stok_masuk', 'id_supplier', 'id_pengguna', 'nomor_transaksi', 'tanggal_masuk', 'catatan'])]
class StokMasuk extends Model
{
    use HasFactory;

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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function detailStokMasuk(): HasMany
    {
        return $this->hasMany(DetailStokMasuk::class, 'id_stok_masuk', 'id_stok_masuk');
    }
}
