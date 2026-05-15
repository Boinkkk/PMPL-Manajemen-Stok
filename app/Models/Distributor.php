<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_distributor', 'kode_distributor', 'nama_distributor', 'alamat', 'telepon', 'email', 'kontak_person'])]
class Distributor extends Model
{
    use HasFactory;

    protected $table = 'distributor';

    protected $primaryKey = 'id_distributor';

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
            'id_distributor' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function orderDistribusi(): HasMany
    {
        return $this->hasMany(OrderDistribusi::class, 'id_distributor', 'id_distributor');
    }

    public function stokKeluar(): HasMany
    {
        return $this->hasMany(StokKeluar::class, 'id_distributor', 'id_distributor');
    }
}
