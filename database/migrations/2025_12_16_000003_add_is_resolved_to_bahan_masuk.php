<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_masuk', function (Blueprint $table) {
            if (!Schema::hasColumn('bahan_masuk', 'is_resolved')) {
                $table->boolean('is_resolved')->default(false)->after('expired');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bahan_masuk', function (Blueprint $table) {
            if (Schema::hasColumn('bahan_masuk', 'is_resolved')) {
                $table->dropColumn('is_resolved');
            }
        });
    }
};
