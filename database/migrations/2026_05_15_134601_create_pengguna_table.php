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
        Schema::create('pengguna', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigInteger('id_pengguna')->primary();
            $table->bigInteger('id_role')->nullable();
            $table->string('nama_lengkap', 100);
            $table->string('username', 50);
            $table->string('password', 255);
            $table->string('email', 100);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('username', 'uq_username');
            $table->unique('email', 'uq_email');
            $table->index('id_role', 'fk_pengguna_role');
            $table->foreign('id_role', 'fk_pengguna_role')->references('id_role')->on('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
