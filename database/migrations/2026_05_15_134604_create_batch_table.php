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
        Schema::create('batch', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigInteger('id_batch')->primary();
            $table->bigInteger('id_produk')->nullable();
            $table->string('nomor_batch', 50);
            $table->date('tanggal_produksi')->nullable();
            $table->date('tanggal_expired');
            $table->string('keterangan', 255)->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->unique(['id_produk', 'nomor_batch'], 'uq_batch_produk');
            $table->index('id_produk', 'idx_batch_produk');
            $table->index('tanggal_expired', 'idx_batch_expired');
            $table->foreign('id_produk', 'fk_batch_produk')->references('id_produk')->on('produk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch');
    }
};
