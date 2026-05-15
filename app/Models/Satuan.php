<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_satuan', 'nama_satuan', 'singkatan'])]
class Satuan extends Model
{
    use HasFactory;

    protected $table = 'satuan';

    protected $primaryKey = 'id_satuan';

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
            'id_satuan' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'id_satuan', 'id_satuan');
    }
}
