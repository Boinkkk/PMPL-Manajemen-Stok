<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_pengguna', 'id_role', 'nama_lengkap', 'username', 'password', 'email', 'status'])]
#[Hidden(['password', 'remember_token'])]
class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';

    protected $primaryKey = 'id_pengguna';

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
            'id_pengguna' => 'integer',
            'id_role' => 'integer',
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function orderDistribusi(): HasMany
    {
        return $this->hasMany(OrderDistribusi::class, 'id_pengguna', 'id_pengguna');
    }

    public function stokMasuk(): HasMany
    {
        return $this->hasMany(StokMasuk::class, 'id_pengguna', 'id_pengguna');
    }

    public function stokKeluar(): HasMany
    {
        return $this->hasMany(StokKeluar::class, 'id_pengguna', 'id_pengguna');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'id_pengguna', 'id_pengguna');
    }

    public function auditTrails(): HasMany
    {
        return $this->hasMany(AuditTrail::class, 'id_pengguna', 'id_pengguna');
    }

    public function isAdmin(): bool
    {
        return strcasecmp($this->role?->nama_role ?? '', 'Admin') === 0;
    }

    public function isManajer(): bool
    {
        return strcasecmp($this->role?->nama_role ?? '', 'Manajer') === 0;
    }

    public function isDistributorOrStafGudang(): bool
    {
        $role = strtolower($this->role?->nama_role ?? '');

        return in_array($role, ['distributor', 'staf gudang', 'gudang'], true);
    }
}
