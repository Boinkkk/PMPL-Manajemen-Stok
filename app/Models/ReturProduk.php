<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_distributor',
    'id_produk',
    'id_pengguna',
    'tanggal_lapor',
    'jumlah_retur',
    'alasan',
    'foto_bukti',
    'status',
    'id_admin_verifikator',
    'tanggal_verifikasi',
    'alasan_penolakan',
    'id_supplier',
    'supplier_diberitahu',
    'tanggal_selesai',
])]
class ReturProduk extends Model
{
    use HasFactory;

    protected $table = 'retur_produk';

    protected $primaryKey = 'id_retur';

    protected $keyType = 'int';

    public $incrementing = true;

    protected function casts(): array
    {
        return [
            'id_retur' => 'integer',
            'id_distributor' => 'integer',
            'id_produk' => 'integer',
            'id_pengguna' => 'integer',
            'jumlah_retur' => 'integer',
            'status' => 'string',
            'id_admin_verifikator' => 'integer',
            'id_supplier' => 'integer',
            'supplier_diberitahu' => 'boolean',
            'tanggal_lapor' => 'datetime',
            'tanggal_verifikasi' => 'datetime',
            'tanggal_selesai' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class, 'id_distributor', 'id_distributor');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function adminVerifikator(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_admin_verifikator', 'id_pengguna');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }
}
