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
        // Drop old table
        Schema::dropIfExists('permintaan_belanja');

        // Header Table
        Schema::create('belanja', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // e.g., REQ-20231210-001
            $table->enum('status', ['pending', 'selesai'])->default('pending');
            $table->bigInteger('total_estimasi')->default(0);
            $table->bigInteger('total_akhir')->nullable(); // Realisasi
            $table->string('foto_bukti')->nullable();
            
            // Tracking Users
            $table->foreignId('user_id_request')->constrained('users')->onDelete('cascade'); // Owner
            $table->foreignId('user_id_selesai')->nullable()->constrained('users')->onDelete('set null'); // Staff
            
            $table->timestamps();
        });

        // Detail Table
        Schema::create('belanja_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('belanja_id')->constrained('belanja')->onDelete('cascade');
            $table->foreignId('bahan_id')->constrained('bahan')->onDelete('cascade');
            
            // Estimasi (Owner)
            $table->integer('jumlah_estimasi');
            $table->decimal('harga_satuan_estimasi', 12, 2);
            $table->decimal('subtotal_estimasi', 12, 2);
            
            // Realisasi (Staff)
            $table->integer('jumlah_akhir')->nullable();
            $table->decimal('harga_satuan_akhir', 12, 2)->nullable();
            $table->decimal('subtotal_akhir', 12, 2)->nullable();
            $table->date('expired')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('belanja_detail');
        Schema::dropIfExists('belanja');
        
        // Restore old table (structure only, data lost)
        Schema::create('permintaan_belanja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_id')->constrained('bahan')->onDelete('cascade');
            $table->integer('jumlah');
            $table->integer('total_harga')->nullable();
            $table->timestamps();
        });
    }
};
