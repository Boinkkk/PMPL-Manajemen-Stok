<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_produk', 'id_kategori', 'id_satuan', 'kode_produk', 'nama_produk', 'harga_satuan', 'stok_terkini', 'stok_minimum', 'deskripsi'])]
class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $primaryKey = 'id_produk';

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
            'id_produk' => 'integer',
            'id_kategori' => 'integer',
            'id_satuan' => 'integer',
            'harga_satuan' => 'decimal:2',
            'stok_terkini' => 'integer',
            'stok_minimum' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class, 'id_produk', 'id_produk');
    }

    public function detailOrders(): HasMany
    {
        return $this->hasMany(DetailOrder::class, 'id_produk', 'id_produk');
    }

    public function detailStokMasuk(): HasMany
    {
        return $this->hasMany(DetailStokMasuk::class, 'id_produk', 'id_produk');
    }

    public function detailStokKeluar(): HasMany
    {
        return $this->hasMany(DetailStokKeluar::class, 'id_produk', 'id_produk');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'id_produk', 'id_produk');
    }
}
