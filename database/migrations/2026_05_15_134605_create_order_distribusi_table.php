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
        Schema::create('order_distribusi', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id('id_order')->primary();
            $table->foreignId('id_distributor')->nullable();
            $table->bigInteger('id_pengguna')->nullable();
            $table->string('nomor_order', 30);
            $table->date('tanggal_order');
            $table->date('tanggal_diproses')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'selesai'])->default('pending');
            $table->text('catatan')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('nomor_order', 'uq_order_nomor');
            $table->index('id_distributor', 'idx_order_distributor');
            $table->index('status', 'idx_order_status');
            $table->index('tanggal_order', 'idx_order_tanggal');
            $table->index('id_pengguna', 'fk_order_pengguna');
            $table->foreign('id_distributor', 'fk_order_distributor')->references('id_distributor')->on('distributor');
            $table->foreign('id_pengguna', 'fk_order_pengguna')->references('id_pengguna')->on('pengguna');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_distribusi');
    }
};
