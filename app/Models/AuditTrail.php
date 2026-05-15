<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_pengguna', 'aksi', 'modul', 'data_lama', 'data_baru', 'ip_address', 'waktu_aksi'])]
class AuditTrail extends Model
{
    use HasFactory;

    public const CREATED_AT = 'waktu_aksi';

    public const UPDATED_AT = null;

    protected $table = 'audit_trail';

    protected $primaryKey = 'id_audit';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_audit' => 'integer',
            'id_pengguna' => 'integer',
            'data_lama' => 'array',
            'data_baru' => 'array',
            'waktu_aksi' => 'datetime',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}
