<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $primaryKey = 'id_supplier';

    protected $fillable = [
        'id_supplier',
        'kode_supplier',
        'nama_supplier',
        'alamat',
        'telepon',
        'email',
        'kontak_person',
    ];

    // Relasi ke stok_masuk
    public function stokMasuk()
    {
        return $this->hasMany(StokMasuk::class, 'id_supplier');
    }

    // Aksesor untuk kompatibilitas dengan view
    public function getNamaAttribute()
    {
        return $this->nama_supplier;
    }

    public function getKontakAttribute()
    {
        return $this->telepon;
    }
}
