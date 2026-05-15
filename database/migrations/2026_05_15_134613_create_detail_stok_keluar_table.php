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
        Schema::create('detail_stok_keluar', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigInteger('id_detail_keluar', true);
            $table->bigInteger('id_stok_keluar')->nullable();
            $table->bigInteger('id_produk')->nullable();
            $table->bigInteger('id_batch')->nullable();
            $table->integer('jumlah');
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);

            $table->index('id_produk', 'idx_detail_keluar_prd');
            $table->index('id_batch', 'fk_detail_keluar_batch');
            $table->index('id_stok_keluar', 'fk_detail_keluar_header');
            $table->foreign('id_batch', 'fk_detail_keluar_batch')->references('id_batch')->on('batch');
            $table->foreign('id_stok_keluar', 'fk_detail_keluar_header')->references('id_stok_keluar')->on('stok_keluar');
            $table->foreign('id_produk', 'fk_detail_keluar_produk')->references('id_produk')->on('produk');
        });

        DB::statement('ALTER TABLE detail_stok_keluar ADD CONSTRAINT chk_detail_keluar_harga CHECK (harga_jual >= 0)');
        DB::statement('ALTER TABLE detail_stok_keluar ADD CONSTRAINT chk_detail_keluar_jumlah CHECK (jumlah > 0)');

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_subtotal_keluar BEFORE INSERT ON detail_stok_keluar FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.jumlah * NEW.harga_jual;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_update_stok_keluar AFTER INSERT ON detail_stok_keluar FOR EACH ROW
BEGIN
    DECLARE stok_sekarang INT;

    SELECT stok_terkini INTO stok_sekarang
    FROM produk
    WHERE id_produk = NEW.id_produk;

    IF stok_sekarang < NEW.jumlah THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stok tidak mencukupi untuk transaksi ini';
    END IF;

    UPDATE produk
    SET stok_terkini = stok_terkini - NEW.jumlah
    WHERE id_produk = NEW.id_produk;
END
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_update_stok_keluar');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_subtotal_keluar');

        Schema::dropIfExists('detail_stok_keluar');
    }
};
