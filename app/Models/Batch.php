<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $table = 'batch';

    protected $primaryKey = 'id_batch';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = ['id_batch', 'id_produk', 'nomor_batch', 'tanggal_produksi', 'tanggal_expired', 'keterangan'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_batch' => 'integer',
            'id_produk' => 'integer',
            'tanggal_produksi' => 'date',
            'tanggal_expired' => 'date',
            'created_at' => 'datetime',
        ];
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function detailStokMasuk(): HasMany
    {
        return $this->hasMany(DetailStokMasuk::class, 'id_batch', 'id_batch');
    }

    public function detailStokKeluar(): HasMany
    {
        return $this->hasMany(DetailStokKeluar::class, 'id_batch', 'id_batch');
    }
}
