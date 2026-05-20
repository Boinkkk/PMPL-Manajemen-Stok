<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id('id_produk')->primary();
            $table->foreignId('id_kategori')->nullable();
            $table->foreignId('id_satuan')->nullable();
            $table->string('kode_produk', 30);
            $table->string('nama_produk', 150);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->integer('stok_terkini')->default(0);
            $table->integer('stok_minimum')->default(0);
            $table->text('deskripsi')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('kode_produk', 'uq_kode_produk');
            $table->unique('nama_produk', 'uq_nama_produk');
            $table->index('id_kategori', 'idx_produk_kategori');
            $table->index('id_satuan', 'idx_produk_satuan');
            $table->index('stok_terkini', 'idx_produk_stok');
            $table->foreign('id_kategori', 'fk_produk_kategori')->references('id_kategori')->on('kategori');
            $table->foreign('id_satuan', 'fk_produk_satuan')->references('id_satuan')->on('satuan');
        });

        DB::statement('ALTER TABLE produk ADD CONSTRAINT chk_harga_satuan CHECK (harga_satuan >= 0)');
        DB::statement('ALTER TABLE produk ADD CONSTRAINT chk_stok_minimum CHECK (stok_minimum >= 0)');
        DB::statement('ALTER TABLE produk ADD CONSTRAINT chk_stok_terkini CHECK (stok_terkini >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
