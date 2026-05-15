<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stok_keluar', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigInteger('id_stok_keluar')->primary();
            $table->bigInteger('id_pengguna')->nullable();
            $table->bigInteger('id_distributor')->nullable();
            $table->bigInteger('id_order')->nullable();
            $table->string('nomor_transaksi', 30);
            $table->date('tanggal_keluar');
            $table->text('catatan')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->unique('nomor_transaksi', 'uq_stok_keluar_nomor');
            $table->index('id_distributor', 'idx_stokkeluar_dist');
            $table->index('id_order', 'idx_stokkeluar_order');
            $table->index('tanggal_keluar', 'idx_stokkeluar_tgl');
            $table->index('id_pengguna', 'fk_stok_keluar_pengguna');
            $table->foreign('id_distributor', 'fk_stok_keluar_distributor')->references('id_distributor')->on('distributor');
            $table->foreign('id_order', 'fk_stok_keluar_order')->references('id_order')->on('order_distribusi');
            $table->foreign('id_pengguna', 'fk_stok_keluar_pengguna')->references('id_pengguna')->on('pengguna');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_keluar');
    }
};
