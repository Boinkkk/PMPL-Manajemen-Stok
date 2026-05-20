<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_supplier', 'kode_supplier', 'nama_supplier', 'alamat', 'telepon', 'email', 'kontak_person'])]
class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $primaryKey = 'id_supplier';

    protected $keyType = 'int';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_supplier' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function stokMasuk(): HasMany
    {
        return $this->hasMany(StokMasuk::class, 'id_supplier', 'id_supplier');
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
     * Cari supplier berdasarkan identitas utama.
     */
    #[Scope]
    protected function search(Builder $query, ?string $keyword): void
    {
        $query->when($keyword, function (Builder $query, string $keyword): void {
            $query->where(function (Builder $query) use ($keyword): void {
                $query
                    ->where('nama_supplier', 'like', "%{$keyword}%")
                    ->orWhere('kode_supplier', 'like', "%{$keyword}%")
                    ->orWhere('kontak_person', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('telepon', 'like', "%{$keyword}%");
            });
        });
    }
}
