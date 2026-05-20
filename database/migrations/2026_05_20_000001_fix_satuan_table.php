<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jika tabel sudah ada, drop terlebih dahulu
        if (Schema::hasTable('satuan')) {
            Schema::dropIfExists('satuan');
        }

        Schema::create('satuan', function (Blueprint $table) {
            $table->id('id_satuan');
            $table->string('nama_satuan', 255)->unique();
            $table->string('singkatan', 50)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('satuan');
    }
};
