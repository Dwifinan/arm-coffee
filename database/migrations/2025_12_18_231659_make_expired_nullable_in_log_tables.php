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
        Schema::table('bahan_masuk', function (Blueprint $table) {
            $table->dateTime('expired')->nullable()->change();
        });
        
        Schema::table('bahan_keluar', function (Blueprint $table) {
            $table->dateTime('expired')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahan_masuk', function (Blueprint $table) {
            $table->dateTime('expired')->nullable(false)->change();
        });

        Schema::table('bahan_keluar', function (Blueprint $table) {
            $table->dateTime('expired')->nullable(false)->change();
        });
    }
};
