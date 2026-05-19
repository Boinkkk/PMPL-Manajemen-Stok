<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE produk DROP FOREIGN KEY fk_produk_kategori');
        DB::statement('ALTER TABLE kategori MODIFY id_kategori BIGINT NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE produk ADD CONSTRAINT fk_produk_kategori FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE produk DROP FOREIGN KEY fk_produk_kategori');
        DB::statement('ALTER TABLE kategori MODIFY id_kategori BIGINT NOT NULL');
        DB::statement('ALTER TABLE produk ADD CONSTRAINT fk_produk_kategori FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)');
    }
};
