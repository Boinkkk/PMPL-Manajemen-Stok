<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return array<int, array{table: string, constraint: string, column: string}>
     */
    private function productForeignKeys(): array
    {
        return [
            ['table' => 'batch', 'constraint' => 'fk_batch_produk', 'column' => 'id_produk'],
            ['table' => 'detail_order', 'constraint' => 'fk_detail_order_produk', 'column' => 'id_produk'],
            ['table' => 'detail_stok_masuk', 'constraint' => 'fk_detail_masuk_produk', 'column' => 'id_produk'],
            ['table' => 'detail_stok_keluar', 'constraint' => 'fk_detail_keluar_produk', 'column' => 'id_produk'],
            ['table' => 'notifikasi', 'constraint' => 'fk_notifikasi_produk', 'column' => 'id_produk'],
        ];
    }

    private function hasForeignKey(string $table, string $constraint): bool
    {
        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->where('REFERENCED_TABLE_NAME', 'produk')
            ->where('REFERENCED_COLUMN_NAME', 'id_produk')
            ->exists();
    }

    private function dropProductForeignKeys(): void
    {
        foreach ($this->productForeignKeys() as $foreignKey) {
            if ($this->hasForeignKey($foreignKey['table'], $foreignKey['constraint'])) {
                DB::statement("ALTER TABLE {$foreignKey['table']} DROP FOREIGN KEY {$foreignKey['constraint']}");
            }
        }
    }

    private function restoreProductForeignKeys(): void
    {
        foreach ($this->productForeignKeys() as $foreignKey) {
            if (! Schema::hasTable($foreignKey['table']) || $this->hasForeignKey($foreignKey['table'], $foreignKey['constraint'])) {
                continue;
            }

            DB::statement(
                "ALTER TABLE {$foreignKey['table']} ADD CONSTRAINT {$foreignKey['constraint']} FOREIGN KEY ({$foreignKey['column']}) REFERENCES produk(id_produk)"
            );
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('produk')) {
            return;
        }

        $this->dropProductForeignKeys();
        DB::statement('ALTER TABLE produk MODIFY id_produk BIGINT NOT NULL AUTO_INCREMENT');
        $this->restoreProductForeignKeys();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('produk')) {
            return;
        }

        $this->dropProductForeignKeys();
        DB::statement('ALTER TABLE produk MODIFY id_produk BIGINT NOT NULL');
        $this->restoreProductForeignKeys();
    }
};
