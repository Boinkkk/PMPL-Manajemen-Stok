<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('detail_stok_keluar')->truncate();
        DB::table('stok_keluar')->truncate();
        DB::table('detail_order')->truncate();
        DB::table('order_distribusi')->truncate();
        DB::table('detail_stok_masuk')->truncate();
        DB::table('stok_masuk')->truncate();
        DB::table('batch')->truncate();
        DB::table('produk')->truncate();
        DB::table('supplier')->truncate();
        DB::table('distributor')->truncate();
        DB::table('pengguna')->truncate();
        DB::table('kategori')->truncate();
        DB::table('satuan')->truncate();
        DB::table('role')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('role')->insert([
            ['id_role' => 1, 'nama_role' => 'Administrator', 'deskripsi' => 'Admin sistem'],
            ['id_role' => 2, 'nama_role' => 'Staf Gudang', 'deskripsi' => 'Pengelola stok'],
            ['id_role' => 3, 'nama_role' => 'Manajer', 'deskripsi' => 'Manajemen distribusi'],
        ]);

        DB::table('satuan')->insert([
            ['id_satuan' => 1, 'nama_satuan' => 'Botol', 'singkatan' => 'btl'],
            ['id_satuan' => 2, 'nama_satuan' => 'Kotak', 'singkatan' => 'ktk'],
            ['id_satuan' => 3, 'nama_satuan' => 'Pcs', 'singkatan' => 'pcs'],
        ]);

        DB::table('kategori')->insert([
            ['id_kategori' => 1, 'nama_kategori' => 'Jamu Kesehatan', 'deskripsi' => 'Produk kesehatan herbal'],
            ['id_kategori' => 2, 'nama_kategori' => 'Jamu Kecantikan', 'deskripsi' => 'Produk herbal kecantikan'],
            ['id_kategori' => 3, 'nama_kategori' => 'Jamu Vitalitas', 'deskripsi' => 'Produk peningkat stamina'],
        ]);

        DB::table('pengguna')->insert([
            [
                'id_pengguna' => 1,
                'id_role' => 1,
                'nama_lengkap' => 'Admin Utama',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'email' => 'admin@jamu.com',
                'status' => 'aktif',
            ],
            [
                'id_pengguna' => 2,
                'id_role' => 2,
                'nama_lengkap' => 'Staf Gudang',
                'username' => 'staf',
                'password' => Hash::make('password123'),
                'email' => 'staf@jamu.com',
                'status' => 'aktif',
            ],
            [
                'id_pengguna' => 3,
                'id_role' => 3,
                'nama_lengkap' => 'Manajer Operasional',
                'username' => 'manager',
                'password' => Hash::make('password123'),
                'email' => 'manager@jamu.com',
                'status' => 'aktif',
            ],
        ]);

        DB::table('supplier')->insert([
            [
                'id_supplier' => 1,
                'kode_supplier' => 'SUP001',
                'nama_supplier' => 'CV Herbal Nusantara',
                'alamat' => 'Surabaya',
                'telepon' => '081234567890',
                'email' => 'herbal@nusantara.com',
                'kontak_person' => 'Budi',
            ],
            [
                'id_supplier' => 2,
                'kode_supplier' => 'SUP002',
                'nama_supplier' => 'PT Rempah Madura',
                'alamat' => 'Madura',
                'telepon' => '081298765432',
                'email' => 'rempah@madura.com',
                'kontak_person' => 'Siti',
            ],
        ]);

        DB::table('distributor')->insert([
            [
                'id_distributor' => 1,
                'kode_distributor' => 'DST001',
                'nama_distributor' => 'Distributor Sehat Abadi',
                'alamat' => 'Jakarta',
                'telepon' => '081111111111',
                'email' => 'dist1@mail.com',
                'kontak_person' => 'Andi',
            ],
            [
                'id_distributor' => 2,
                'kode_distributor' => 'DST002',
                'nama_distributor' => 'Toko Herbal Makmur',
                'alamat' => 'Bandung',
                'telepon' => '082222222222',
                'email' => 'dist2@mail.com',
                'kontak_person' => 'Rina',
            ],
        ]);

        DB::table('produk')->insert([
            [
                'id_produk' => 1,
                'id_kategori' => 1,
                'id_satuan' => 1,
                'kode_produk' => 'PRD001',
                'nama_produk' => 'Jamu Kunyit Asam',
                'harga_satuan' => 15000,
                'stok_terkini' => 120,
                'stok_minimum' => 20,
                'deskripsi' => 'Jamu tradisional kunyit asam',
            ],
            [
                'id_produk' => 2,
                'id_kategori' => 1,
                'id_satuan' => 1,
                'kode_produk' => 'PRD002',
                'nama_produk' => 'Jamu Beras Kencur',
                'harga_satuan' => 12000,
                'stok_terkini' => 90,
                'stok_minimum' => 15,
                'deskripsi' => 'Jamu beras kencur segar',
            ],
            [
                'id_produk' => 3,
                'id_kategori' => 3,
                'id_satuan' => 2,
                'kode_produk' => 'PRD003',
                'nama_produk' => 'Jamu Vitalitas Pria',
                'harga_satuan' => 35000,
                'stok_terkini' => 40,
                'stok_minimum' => 10,
                'deskripsi' => 'Herbal stamina pria',
            ],
            [
                'id_produk' => 4,
                'id_kategori' => 2,
                'id_satuan' => 1,
                'kode_produk' => 'PRD004',
                'nama_produk' => 'Jamu Kecantikan Herbal',
                'harga_satuan' => 28000,
                'stok_terkini' => 75,
                'stok_minimum' => 10,
                'deskripsi' => 'Herbal kecantikan alami',
            ],
        ]);

        DB::table('batch')->insert([
            [
                'id_batch' => 1,
                'id_produk' => 1,
                'nomor_batch' => 'BATCH-KA-001',
                'tanggal_produksi' => '2026-01-01',
                'tanggal_expired' => '2027-01-01',
                'keterangan' => 'Produksi awal',
            ],
            [
                'id_batch' => 2,
                'id_produk' => 2,
                'nomor_batch' => 'BATCH-BK-001',
                'tanggal_produksi' => '2026-02-01',
                'tanggal_expired' => '2027-02-01',
                'keterangan' => 'Produksi reguler',
            ],
            [
                'id_batch' => 3,
                'id_produk' => 3,
                'nomor_batch' => 'BATCH-VP-001',
                'tanggal_produksi' => '2026-03-01',
                'tanggal_expired' => '2027-03-01',
                'keterangan' => 'Produk premium',
            ],
        ]);

        DB::table('stok_masuk')->insert([
            [
                'id_stok_masuk' => 1,
                'id_supplier' => 1,
                'id_pengguna' => 2,
                'nomor_transaksi' => 'SM-2026-001',
                'tanggal_masuk' => '2026-05-01',
                'catatan' => 'Pengadaan awal',
            ],
            [
                'id_stok_masuk' => 2,
                'id_supplier' => 2,
                'id_pengguna' => 2,
                'nomor_transaksi' => 'SM-2026-002',
                'tanggal_masuk' => '2026-05-03',
                'catatan' => 'Restock bulanan',
            ],
        ]);

        DB::table('detail_stok_masuk')->insert([
            [
                'id_stok_masuk' => 1,
                'id_produk' => 1,
                'id_batch' => 1,
                'jumlah' => 100,
                'harga_beli' => 10000,
            ],
            [
                'id_stok_masuk' => 1,
                'id_produk' => 2,
                'id_batch' => 2,
                'jumlah' => 80,
                'harga_beli' => 9000,
            ],
            [
                'id_stok_masuk' => 2,
                'id_produk' => 3,
                'id_batch' => 3,
                'jumlah' => 50,
                'harga_beli' => 25000,
            ],
        ]);

        DB::table('order_distribusi')->insert([
            [
                'id_order' => 1,
                'id_distributor' => 1,
                'id_pengguna' => 3,
                'nomor_order' => 'ORD-2026-001',
                'tanggal_order' => '2026-05-10',
                'status' => 'pending',
                'catatan' => 'Order pertama',
            ],
            [
                'id_order' => 2,
                'id_distributor' => 2,
                'id_pengguna' => 3,
                'nomor_order' => 'ORD-2026-002',
                'tanggal_order' => '2026-05-12',
                'status' => 'disetujui',
                'catatan' => 'Order kedua',
            ],
        ]);

        DB::table('detail_order')->insert([
            [
                'id_order' => 1,
                'id_produk' => 1,
                'jumlah_diminta' => 20,
                'jumlah_disetujui' => 0,
                'harga_satuan' => 15000,
                'catatan' => 'Menunggu persetujuan',
            ],
            [
                'id_order' => 2,
                'id_produk' => 2,
                'jumlah_diminta' => 15,
                'jumlah_disetujui' => 15,
                'harga_satuan' => 12000,
                'catatan' => 'Disetujui penuh',
            ],
        ]);

        DB::table('stok_keluar')->insert([
            [
                'id_stok_keluar' => 1,
                'id_pengguna' => 2,
                'id_distributor' => 2,
                'id_order' => 2,
                'nomor_transaksi' => 'SK-2026-001',
                'tanggal_keluar' => '2026-05-13',
                'catatan' => 'Pengiriman order',
            ],
        ]);

        DB::table('detail_stok_keluar')->insert([
            [
                'id_stok_keluar' => 1,
                'id_produk' => 2,
                'id_batch' => 2,
                'jumlah' => 15,
                'harga_jual' => 12000,
            ],
        ]);
    }
}
