<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_order', 'id_distributor', 'id_pengguna', 'nomor_order', 'tanggal_order', 'tanggal_diproses', 'status', 'catatan'])]
class OrderDistribusi extends Model
{
    use HasFactory;

    protected $table = 'order_distribusi';

    protected $primaryKey = 'id_order';

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
            'id_order' => 'integer',
            'id_distributor' => 'integer',
            'id_pengguna' => 'integer',
            'tanggal_order' => 'date',
            'tanggal_diproses' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class, 'id_distributor', 'id_distributor');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function detailOrders(): HasMany
    {
        return $this->hasMany(DetailOrder::class, 'id_order', 'id_order');
    }

    public function stokKeluar(): HasMany
    {
        return $this->hasMany(StokKeluar::class, 'id_order', 'id_order');
    }
}
