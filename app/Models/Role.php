<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_role', 'nama_role', 'deskripsi'])]
class Role extends Model
{
    use HasFactory;

    protected $table = 'role';

    protected $primaryKey = 'id_role';

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
            'id_role' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function pengguna(): HasMany
    {
        return $this->hasMany(Pengguna::class, 'id_role', 'id_role');
    }
}
