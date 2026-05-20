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
        Schema::create('data_eoq', function (Blueprint $table) {
            $table->id('id_eoq');
            $table->foreignId('id_produk')->nullable();
            $table->bigInteger('permintaan_tahunan')->nullable();
            $table->bigInteger('biaya_pemesanan')->nullable();
            $table->bigInteger('biaya_penyimpanan')->nullable();
            $table->bigInteger('eoq')->nullable();

            $table->foreign('id_produk', 'fk_produk_eoq')->references('id_produk')->on('produk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_eoq');
    }
};
