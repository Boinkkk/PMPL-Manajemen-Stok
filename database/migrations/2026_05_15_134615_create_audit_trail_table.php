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
        Schema::create('audit_trail', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigInteger('id_audit', true);
            $table->bigInteger('id_pengguna')->nullable();
            $table->string('aksi', 50);
            $table->string('modul', 50);
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->dateTime('waktu_aksi')->useCurrent();

            $table->index('id_pengguna', 'idx_audit_pengguna');
            $table->index('modul', 'idx_audit_modul');
            $table->index('waktu_aksi', 'idx_audit_waktu');
            $table->foreign('id_pengguna', 'fk_audit_pengguna')->references('id_pengguna')->on('pengguna');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trail');
    }
};
