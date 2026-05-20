<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierTableSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::create([
            'id_supplier' => 1,
            'kode_supplier' => 'SPL001',
            'nama_supplier' => 'PT Herbal Nusantara',
            'alamat' => 'Jl. Raya Sukobilo No. 45, Subarjo, Surabaya, Jawa Timur 69111',
            'telepon' => '0812-3456-7890',
            'email' => 'info@herbalnusantara.com',
            'kontak_person' => 'Budi Santoso',
        ]);

        Supplier::create([
            'id_supplier' => 2,
            'kode_supplier' => 'SPL002',
            'nama_supplier' => 'CV Jamu Sehat Madura',
            'alamat' => 'Jl. KH. Hasnun Asyari No. 12, Sungai Kram, Madura, Jawa Timur 69116',
            'telepon' => '0821-5678-9012',
            'email' => 'cs@jamusehat.com',
            'kontak_person' => 'Siti Aminah',
        ]);

        Supplier::create([
            'id_supplier' => 3,
            'kode_supplier' => 'SPL003',
            'nama_supplier' => 'UD Rempah Alami',
            'alamat' => 'Jl. Diponegoro No. 68, Sampang, Jawa Timur 69212',
            'telepon' => '0852-2345-6789',
            'email' => 'rempahalami@gmail.com',
            'kontak_person' => 'Ahmad Zaini',
        ]);
    }
}
