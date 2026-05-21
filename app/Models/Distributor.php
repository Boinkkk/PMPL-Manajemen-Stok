<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_distributor', 'kode_distributor', 'nama_distributor', 'alamat', 'telepon', 'email', 'kontak_person'])]
class Distributor extends Model
{
    use HasFactory;

    protected $table = 'distributor';

    protected $primaryKey = 'id_distributor';

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

    /**
     * Scope distributor aktif.
     *
     * Tabel distributor tidak memiliki kolom status pada migration, jadi semua
     * distributor dianggap aktif untuk form order distribusi.
     */
    #[Scope]
    protected function aktif(Builder $query): void
    {
        //
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
     * Cari distributor berdasarkan identitas utama.
     */
    #[Scope]
    protected function search(Builder $query, ?string $keyword): void
    {
        $query->when($keyword, function (Builder $query, string $keyword): void {
            $query->where(function (Builder $query) use ($keyword): void {
                $query
                    ->where('nama_distributor', 'like', "%{$keyword}%")
                    ->orWhere('kode_distributor', 'like', "%{$keyword}%")
                    ->orWhere('kontak_person', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('telepon', 'like', "%{$keyword}%");
            });
        });
    }
}
