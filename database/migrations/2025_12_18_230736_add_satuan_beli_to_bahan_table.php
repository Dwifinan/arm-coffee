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
        Schema::table('bahan', function (Blueprint $table) {
            $table->string('satuan_beli', 50)->nullable()->after('satuan'); // e.g. Galon, Pack, Kg
            // jumlah_satuan is already present as conversion factor
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahan', function (Blueprint $table) {
            $table->dropColumn('satuan_beli');
        });
    }
};
