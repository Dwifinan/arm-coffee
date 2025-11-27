<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 30)->unique();
            $table->text('password');
            $table->enum('role', ['owner', 'staff']);
            $table->timestamps();
        });

        // Tabel bahan
        Schema::create('bahan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('nama', 50)->nullable();
            $table->string('satuan', 50)->nullable();
            $table->integer('harga_persatuan')->nullable();
            $table->integer('jumlah_satuan')->nullable();
            $table->decimal('harga_satuan', 10, 2)->storedAs('harga_persatuan / NULLIF(jumlah_satuan, 0)');
            $table->integer('stok_minimal')->nullable();
            $table->integer('stok')->default(0);
            $table->timestamps();
        });

        // Tabel menu
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->integer('harga');
            $table->boolean('tersedia')->default(1);
            $table->timestamps();
        });

        // Tabel komposisi
        Schema::create('komposisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menu')->onDelete('cascade');
            $table->foreignId('bahan_id')->constrained('bahan')->onDelete('cascade');
            $table->integer('jumlah_bahan');
            $table->string('satuan');
            $table->timestamps();
        });

        // Tabel permintaan_belanja
        Schema::create('permintaan_belanja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_id')->constrained('bahan')->onDelete('cascade');
            $table->integer('jumlah');
            $table->timestamps();
        });

        // Tabel bahan_masuk
        Schema::create('bahan_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_id')->constrained('bahan')->onDelete('cascade');
            $table->integer('jumlah');
            $table->integer('harga');
            $table->integer('harga_satuan')->nullable();
            $table->integer('total_harga');
            $table->dateTime('expired');
            $table->timestamps();
        });

        // Tabel penjualan
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menu')->onDelete('cascade');
            $table->integer('jumlah');
            $table->integer('total_harga');
            $table->timestamps();
        });

        // Tabel bahan_keluar
        Schema::create('bahan_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_id')->constrained('bahan')->onDelete('cascade');
            $table->integer('jumlah');
            $table->integer('harga');
            $table->integer('harga_satuan')->nullable();
            $table->integer('total_harga');
            $table->dateTime('expired');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_keluar');
        Schema::dropIfExists('penjualan');
        Schema::dropIfExists('bahan_masuk');
        Schema::dropIfExists('permintaan_belanja');
        Schema::dropIfExists('komposisi');
        Schema::dropIfExists('menu');
        Schema::dropIfExists('bahan');
        Schema::dropIfExists('users');
    }
};
