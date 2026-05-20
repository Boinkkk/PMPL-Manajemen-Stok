<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Satuan;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $kategori1 = Kategori::first() ?? Kategori::factory()->create(['nama_kategori' => 'Umum']);
        $satuan1 = Satuan::first() ?? Satuan::factory()->create(['nama_satuan' => 'PCS', 'singkatan' => 'pcs']);

        $samples = [
            ['nama' => 'Pegel Linu', 'kategori' => $kategori1->id_kategori, 'satuan' => $satuan1->id_satuan, 'harga' => 25000, 'stok' => 20, 'min' => 5],
            ['nama' => 'Batuk', 'kategori' => $kategori1->id_kategori, 'satuan' => $satuan1->id_satuan, 'harga' => 15000, 'stok' => 20, 'min' => 5],
            ['nama' => 'Sakit Kepala', 'kategori' => $kategori1->id_kategori, 'satuan' => $satuan1->id_satuan, 'harga' => 35000, 'stok' => 20, 'min' => 5],
        ];

        foreach ($samples as $s) {
            Produk::create([
                'kode_produk' => Produk::generateKode(),
                'nama_produk' => $s['nama'],
                'id_kategori' => $s['kategori'],
                'id_satuan' => $s['satuan'],
                'harga_satuan' => $s['harga'],
                'stok_terkini' => $s['stok'],
                'stok_minimum' => $s['min'],
                'deskripsi' => null,
            ]);
        }
    }
}
