<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $primaryKey = 'id_produk';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = ['id_kategori', 'id_satuan', 'kode_produk', 'nama_produk', 'harga_satuan', 'stok_terkini', 'stok_minimum', 'deskripsi'];

    protected $casts = [
        'id_produk' => 'integer',
        'id_kategori' => 'integer',
        'id_satuan' => 'integer',
        'harga_satuan' => 'decimal:2',
        'stok_terkini' => 'integer',
        'stok_minimum' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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

    // Generate next kode produk PRD-XXX
    public static function generateKode(): string
    {
        $last = self::selectRaw('MAX(CAST(SUBSTRING(kode_produk, 5) AS UNSIGNED)) as max_number')->first();
        $num = ($last && $last->max_number) ? intval($last->max_number) + 1 : 1;

        return sprintf('PRD-%03d', $num);
    }

    public function getFormattedHargaAttribute(): string
    {
        return 'Rp '.number_format($this->harga_satuan, 0, ',', '.');
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stok_terkini == 0) {
            return 'out';
        }
        if ($this->stok_terkini <= $this->stok_minimum) {
            return 'low';
        }

        return 'available';
    }
}
