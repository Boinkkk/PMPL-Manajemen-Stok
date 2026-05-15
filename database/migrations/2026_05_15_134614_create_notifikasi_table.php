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
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigInteger('id_notifikasi', true);
            $table->bigInteger('id_produk')->nullable();
            $table->bigInteger('id_pengguna')->nullable();
            $table->enum('jenis', ['stok_minimum', 'stok_habis', 'kedaluwarsa']);
            $table->string('pesan', 255);
            $table->enum('status', ['belum_dibaca', 'dibaca'])->default('belum_dibaca');
            $table->dateTime('created_at')->useCurrent();

            $table->index('id_produk', 'idx_notif_produk');
            $table->index('status', 'idx_notif_status');
            $table->index('id_pengguna', 'fk_notifikasi_pengguna');
            $table->foreign('id_pengguna', 'fk_notifikasi_pengguna')->references('id_pengguna')->on('pengguna');
            $table->foreign('id_produk', 'fk_notifikasi_produk')->references('id_produk')->on('produk');
        });

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_notifikasi_stok AFTER UPDATE ON produk FOR EACH ROW
BEGIN
    IF NEW.stok_terkini = 0 AND OLD.stok_terkini > 0 THEN
        INSERT INTO notifikasi (id_produk, id_pengguna, jenis, pesan)
        SELECT NEW.id_produk,
               p.id_pengguna,
               'stok_habis',
               CONCAT('Stok produk "', NEW.nama_produk, '" telah habis')
        FROM pengguna p
        WHERE p.status = 'aktif';

    ELSEIF NEW.stok_terkini <= NEW.stok_minimum
        AND OLD.stok_terkini > NEW.stok_minimum THEN
        INSERT INTO notifikasi (id_produk, id_pengguna, jenis, pesan)
        SELECT NEW.id_produk,
               p.id_pengguna,
               'stok_minimum',
               CONCAT('Stok produk "', NEW.nama_produk,
                      '" mendekati batas minimum (', NEW.stok_terkini, ')')
        FROM pengguna p
        WHERE p.status = 'aktif';
    END IF;
END
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_notifikasi_stok');

        Schema::dropIfExists('notifikasi');
    }
};
