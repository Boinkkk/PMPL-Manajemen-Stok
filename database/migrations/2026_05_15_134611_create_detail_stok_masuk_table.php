<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_stok_masuk', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigInteger('id_detail_masuk', true);
            $table->bigInteger('id_stok_masuk')->nullable();
            $table->bigInteger('id_produk')->nullable();
            $table->bigInteger('id_batch')->nullable();
            $table->integer('jumlah');
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);

            $table->index('id_produk', 'idx_detail_masuk_produk');
            $table->index('id_batch', 'fk_detail_masuk_batch');
            $table->index('id_stok_masuk', 'fk_detail_masuk_header');
            $table->foreign('id_batch', 'fk_detail_masuk_batch')->references('id_batch')->on('batch');
            $table->foreign('id_stok_masuk', 'fk_detail_masuk_header')->references('id_stok_masuk')->on('stok_masuk');
            $table->foreign('id_produk', 'fk_detail_masuk_produk')->references('id_produk')->on('produk');
        });

        DB::statement('ALTER TABLE detail_stok_masuk ADD CONSTRAINT chk_detail_masuk_harga CHECK (harga_beli >= 0)');
        DB::statement('ALTER TABLE detail_stok_masuk ADD CONSTRAINT chk_detail_masuk_jumlah CHECK (jumlah > 0)');

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_subtotal_masuk BEFORE INSERT ON detail_stok_masuk FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.jumlah * NEW.harga_beli;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_update_stok_masuk AFTER INSERT ON detail_stok_masuk FOR EACH ROW
BEGIN
    UPDATE produk
    SET stok_terkini = stok_terkini + NEW.jumlah
    WHERE id_produk = NEW.id_produk;
END
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_update_stok_masuk');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_subtotal_masuk');

        Schema::dropIfExists('detail_stok_masuk');
    }
};
