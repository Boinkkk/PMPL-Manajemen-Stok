<?php

namespace App\Models;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Satuan extends Model
{
    use HasFactory;

    protected $table = 'satuan';

    protected $primaryKey = 'id_satuan';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = ['nama_satuan', 'singkatan'];

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
