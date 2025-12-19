<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            // Fix previous missing column if necessary
            if (!Schema::hasColumn('penjualan', 'kode_transaksi')) {
                $table->string('kode_transaksi', 20)->nullable()->after('id');
            }

            // New column for Batch grouping
            if (!Schema::hasColumn('penjualan', 'batch_id')) {
                $table->string('batch_id', 40)->nullable()->after('kode_transaksi')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            if (Schema::hasColumn('penjualan', 'batch_id')) {
                $table->dropColumn('batch_id');
            }
            if (Schema::hasColumn('penjualan', 'kode_transaksi')) {
                $table->dropColumn('kode_transaksi');
            }
        });
    }
};
