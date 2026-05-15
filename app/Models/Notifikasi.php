<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_produk', 'id_pengguna', 'jenis', 'pesan', 'status'])]
class Notifikasi extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $table = 'notifikasi';

    protected $primaryKey = 'id_notifikasi';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_notifikasi' => 'integer',
            'id_produk' => 'integer',
            'id_pengguna' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}
