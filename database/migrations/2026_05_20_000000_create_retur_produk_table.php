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
        Schema::create('retur_produk', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id('id_retur')->autoIncrement();
            $table->foreignId('id_distributor');
            $table->foreignId('id_produk');
            $table->bigInteger('id_pengguna')->nullable();
            $table->dateTime('tanggal_lapor')->useCurrent();
            $table->integer('jumlah_retur');
            $table->text('alasan')->nullable();
            $table->string('foto_bukti')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'selesai'])->default('pending');
            $table->bigInteger('id_admin_verifikator')->nullable();
            $table->dateTime('tanggal_verifikasi')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->foreignId('id_supplier')->nullable();
            $table->boolean('supplier_diberitahu')->default(false);
            $table->dateTime('tanggal_selesai')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_distributor', 'idx_retur_distributor');
            $table->index('id_produk', 'idx_retur_produk');
            $table->index('id_pengguna', 'idx_retur_pengguna');
            $table->index('status', 'idx_retur_status');
            $table->foreign('id_distributor', 'fk_retur_distributor')->references('id_distributor')->on('distributor');
            $table->foreign('id_produk', 'fk_retur_produk')->references('id_produk')->on('produk');
            $table->foreign('id_pengguna', 'fk_retur_pengguna')->references('id_pengguna')->on('pengguna');
            $table->foreign('id_admin_verifikator', 'fk_retur_admin_verifikator')->references('id_pengguna')->on('pengguna');
            $table->foreign('id_supplier', 'fk_retur_supplier')->references('id_supplier')->on('supplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur_produk');
    }
};
