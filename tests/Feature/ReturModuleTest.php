<?php

namespace Tests\Feature;

use App\Models\Distributor;
use App\Models\Produk;
use App\Models\Role;
use App\Models\ReturProduk;
use App\Models\Supplier;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReturModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_distributor_can_create_pending_retur_with_photo_and_supplier_notification(): void
    {
        Storage::fake('public');
        Http::fake([
            '*' => Http::response(['success' => true], 200),
        ]);

        $role = Role::create([
            'id_role' => 1,
            'nama_role' => 'Distributor',
            'deskripsi' => 'Role distributor',
        ]);

        $pengguna = Pengguna::create([
            'id_pengguna' => 1000,
            'id_role' => $role->id_role,
            'nama_lengkap' => 'Distributor User',
            'username' => 'distributor1',
            'password' => Hash::make('password'),
            'email' => 'dist@example.com',
            'status' => 'active',
        ]);

        $distributor = Distributor::create([
            'id_distributor' => 500,
            'kode_distributor' => 'D500',
            'nama_distributor' => 'Distributor A',
            'alamat' => 'Jl. Contoh',
            'telepon' => '081234567890',
            'email' => 'distributor@example.com',
            'kontak_person' => 'Budi',
        ]);

        $supplier = Supplier::create([
            'id_supplier' => 700,
            'kode_supplier' => 'S700',
            'nama_supplier' => 'Supplier A',
            'alamat' => 'Jl. Supplier',
            'telepon' => '081298765432',
            'email' => 'supplier@example.com',
            'kontak_person' => 'Siti',
        ]);

        $produk = Produk::create([
            'id_produk' => 900,
            'id_kategori' => null,
            'id_satuan' => null,
            'kode_produk' => 'P900',
            'nama_produk' => 'Jamu Herbal',
            'harga_satuan' => 12000,
            'stok_terkini' => 10,
            'stok_minimum' => 1,
            'deskripsi' => 'Produk jamu',
        ]);

        Auth::login($pengguna);

        $response = $this->post(route('retur-produk.store'), [
            'id_distributor' => $distributor->id_distributor,
            'id_produk' => $produk->id_produk,
            'jumlah_retur' => 2,
            'alasan' => 'Produk rusak saat penerimaan',
            'foto_bukti' => UploadedFile::fake()->image('bukti.jpg'),
            'id_supplier' => $supplier->id_supplier,
        ]);

        $response->assertRedirect(route('retur-produk.index'));
        $this->assertDatabaseHas('retur_produk', [
            'id_distributor' => $distributor->id_distributor,
            'id_produk' => $produk->id_produk,
            'id_pengguna' => $pengguna->id_pengguna,
            'jumlah_retur' => 2,
            'status' => 'pending',
            'id_supplier' => $supplier->id_supplier,
            'supplier_diberitahu' => true,
        ]);

        $retur = ReturProduk::first();
        Storage::disk('public')->assertExists($retur->foto_bukti);
    }

    public function test_admin_can_approve_retur_and_reduce_stock(): void
    {
        Http::fake([
            '*' => Http::response(['success' => true], 200),
        ]);

        $roleAdmin = Role::create([
            'id_role' => 2,
            'nama_role' => 'Admin',
            'deskripsi' => 'Role administrator',
        ]);

        $admin = Pengguna::create([
            'id_pengguna' => 2000,
            'id_role' => $roleAdmin->id_role,
            'nama_lengkap' => 'Admin User',
            'username' => 'admin1',
            'password' => Hash::make('password'),
            'email' => 'admin@example.com',
            'status' => 'active',
        ]);

        $supplier = Supplier::create([
            'id_supplier' => 800,
            'kode_supplier' => 'S800',
            'nama_supplier' => 'Supplier B',
            'alamat' => 'Jl. Supplier B',
            'telepon' => '081298765433',
            'email' => 'supplierb@example.com',
            'kontak_person' => 'Ani',
        ]);

        $distributor = Distributor::create([
            'id_distributor' => 501,
            'kode_distributor' => 'D501',
            'nama_distributor' => 'Distributor B',
            'alamat' => 'Jl. Contoh 2',
            'telepon' => '081234560000',
            'email' => 'distributorb@example.com',
            'kontak_person' => 'Andi',
        ]);

        $produk = Produk::create([
            'id_produk' => 901,
            'id_kategori' => null,
            'id_satuan' => null,
            'kode_produk' => 'P901',
            'nama_produk' => 'Jamu Tradisional',
            'harga_satuan' => 15000,
            'stok_terkini' => 5,
            'stok_minimum' => 1,
            'deskripsi' => 'Produk jamu tradisional',
        ]);

        $retur = ReturProduk::create([
            'id_distributor' => $distributor->id_distributor,
            'id_produk' => $produk->id_produk,
            'id_pengguna' => $admin->id_pengguna,
            'jumlah_retur' => 2,
            'alasan' => 'Kualitas tidak sesuai',
            'status' => 'pending',
            'id_supplier' => $supplier->id_supplier,
        ]);

        Auth::login($admin);

        $response = $this->post(route('retur-produk.setujui', $retur));

        $response->assertRedirect(route('retur-produk.show', $retur));
        $this->assertDatabaseHas('retur_produk', [
            'id_retur' => $retur->id_retur,
            'status' => 'disetujui',
            'supplier_diberitahu' => true,
        ]);

        $this->assertDatabaseHas('produk', [
            'id_produk' => $produk->id_produk,
            'stok_terkini' => 3,
        ]);
    }
}
