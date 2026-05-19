<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['id_pengguna', 'id_role', 'nama_lengkap', 'username', 'password', 'email', 'status'])]
#[Hidden(['password'])]
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

    /**
     * Format tanggal dibuat untuk tampilan Bahasa Indonesia.
     *
     * @return Attribute<string|null, never>
     */
    protected function dibuatPadaFormatted(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->created_at?->locale('id')->translatedFormat('d F Y H:i'),
        );
    }

    /**
     * Filter pengguna berdasarkan role.
     */
    #[Scope]
    protected function byRole(Builder $query, int|string|null $idRole): void
    {
        $query->when($idRole, fn (Builder $query): Builder => $query->where('id_role', $idRole));
    }

    /**
     * Filter pengguna berdasarkan status akun.
     */
    #[Scope]
    protected function byStatus(Builder $query, ?string $status): void
    {
        $query->when($status, fn (Builder $query): Builder => $query->where('status', $status));
    }

    /**
     * Cari pengguna berdasarkan nama, username, atau email.
     */
    #[Scope]
    protected function search(Builder $query, ?string $keyword): void
    {
        $query->when($keyword, function (Builder $query, string $keyword): void {
            $query->where(function (Builder $query) use ($keyword): void {
                $query
                    ->where('nama_lengkap', 'like', "%{$keyword}%")
                    ->orWhere('username', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        });
    }

    /**
     * Periksa apakah pengguna memiliki salah satu role.
     *
     * @param  array<int, string>  $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        $roleName = $this->role?->nama_role;

        if ($roleName === null) {
            return false;
        }

        $normalizedRoleName = $this->normalizeRoleName($roleName);

        foreach ($roles as $role) {
            if ($normalizedRoleName === $this->normalizeRoleName($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Periksa apakah pengguna adalah administrator.
     */
    public function isAdministrator(): bool
    {
        return $this->hasAnyRole(['Administrator']);
    }

    /**
     * Periksa apakah pengguna boleh mengelola stok.
     */
    public function canManageStock(): bool
    {
        return $this->hasAnyRole(['Administrator', 'Staf Gudang']);
    }

    /**
     * Normalisasi variasi nama role dari database dan route middleware.
     */
    private function normalizeRoleName(string $roleName): string
    {
        $roleName = Str::of($roleName)->lower()->squish()->toString();

        return match ($roleName) {
            'admin', 'administrator' => 'administrator',
            'staff', 'staf', 'staff gudang', 'staf gudang' => 'staf gudang',
            'manager', 'manajer' => 'manajer',
            default => $roleName,
        };
    }

    /**
     * Role yang dimiliki pengguna.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    /**
     * Order distribusi yang dibuat pengguna.
     */
    public function orderDistribusi(): HasMany
    {
        return $this->hasMany(OrderDistribusi::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Periksa apakah pengguna boleh mengekspor order distribusi.
     */
    public function canExportOrders(): bool
    {
        return $this->hasAnyRole(['Administrator', 'Manajer']);
    }

    /**
     * Riwayat stok masuk yang dibuat pengguna.
     */
    public function stokMasuk(): HasMany
    {
        return $this->hasMany(StokMasuk::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Riwayat stok keluar yang dibuat pengguna.
     */
    public function stokKeluar(): HasMany
    {
        return $this->hasMany(StokKeluar::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Notifikasi milik pengguna.
     */
    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Audit trail yang dilakukan pengguna.
     */
    public function auditTrails(): HasMany
    {
        return $this->hasMany(AuditTrail::class, 'id_pengguna', 'id_pengguna');
    }
}
