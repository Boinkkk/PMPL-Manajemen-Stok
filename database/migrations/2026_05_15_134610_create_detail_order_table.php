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
        Schema::create('detail_order', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigInteger('id_detail_order', true);
            $table->bigInteger('id_order')->nullable();
            $table->bigInteger('id_produk')->nullable();
            $table->integer('jumlah_diminta');
            $table->integer('jumlah_disetujui')->nullable()->default(0);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->string('catatan', 255)->nullable();

            $table->index('id_order', 'fk_detail_order_header');
            $table->index('id_produk', 'fk_detail_order_produk');
            $table->foreign('id_order', 'fk_detail_order_header')->references('id_order')->on('order_distribusi');
            $table->foreign('id_produk', 'fk_detail_order_produk')->references('id_produk')->on('produk');
        });

        DB::statement('ALTER TABLE detail_order ADD CONSTRAINT chk_detail_order_diminta CHECK (jumlah_diminta > 0)');
        DB::statement('ALTER TABLE detail_order ADD CONSTRAINT chk_detail_order_disetujui CHECK (jumlah_disetujui >= 0)');

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_subtotal_order BEFORE INSERT ON detail_order FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.jumlah_diminta * NEW.harga_satuan;
END
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_subtotal_order');

        Schema::dropIfExists('detail_order');
    }
};
