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
        Schema::create('stok_masuk', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id('id_stok_masuk')->primary();
            $table->foreignId('id_supplier')->nullable();
            $table->bigInteger('id_pengguna')->nullable();
            $table->string('nomor_transaksi', 30);
            $table->date('tanggal_masuk');
            $table->text('catatan')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->unique('nomor_transaksi', 'uq_stok_masuk_nomor');
            $table->index('id_pengguna', 'fk_stok_masuk_pengguna');
            $table->index('id_supplier', 'idx_stokmasuk_supplier');
            $table->index('tanggal_masuk', 'idx_stokmasuk_tanggal');
            $table->foreign('id_supplier', 'fk_stok_masuk_supplier')->references('id_supplier')->on('supplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_masuk');
    }
};
