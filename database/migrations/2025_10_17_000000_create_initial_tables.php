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
            $table->id(); // auto increment
            $table->string('username', 30)->unique();
            $table->text('password');
            $table->enum('role', ['owner', 'staff']);
            $table->timestamps();
        });

        // Tabel bahan
        Schema::create('bahan', function (Blueprint $table) {
            $table->id(); // Gunakan id() atau bigIncrements('id')
            $table->string('kode', 50)->unique();
            $table->string('nama', 50)->nullable();
            $table->string('satuan', 50)->nullable();
            $table->integer('harga_persatuan')->nullable();
            $table->integer('jumlah_satuan')->nullable();
            $table->integer('harga_satuan')->nullable();
            $table->integer('stok_minimal')->nullable();
            $table->integer('stok')->nullable()->default(0);
            $table->timestamps();
        });
        
        // Tabel menu
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->integer('harga');
            $table->timestamps();
        });

        // Tabel produksi
        Schema::create('produksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menu')->onDelete('cascade');
            $table->integer('jumlah');
            $table->integer('total_harga');
            $table->timestamps();
        });

        // Tabel menu_detail
        Schema::create('menu_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menu')->onDelete('cascade');
            // FIX: Menggunakan sintaks eksplisit untuk kunci asing
            $table->unsignedBigInteger('bahan_id');
            $table->foreign('bahan_id')->references('id')->on('bahan')->onDelete('cascade');
            $table->integer('jumlah');
            $table->timestamps();
        });

        // Tabel bahan_masuk
        Schema::create('bahan_masuk', function (Blueprint $table) {
            $table->id();
            // FIX: Menggunakan sintaks eksplisit untuk kunci asing
            $table->unsignedBigInteger('bahan_id');
            $table->foreign('bahan_id')->references('id')->on('bahan')->onDelete('cascade');
            $table->integer('jumlah');
            $table->integer('harga');
            $table->integer('harga_satuan')->nullable();
            $table->integer('total_harga');
            $table->dateTime('expired');
            $table->timestamps();
        });
        
        // Tabel belanja_requests (Tabel yang dibutuhkan untuk request aktif)
        Schema::create('belanja_requests', function (Blueprint $table) {
            $table->id();
            // FIX: Menggunakan sintaks eksplisit untuk kunci asing, 
            // menghilangkan sumber duplikasi kolom 'bahan_id'
            $table->unsignedBigInteger('bahan_id');
            $table->foreign('bahan_id')->references('id')->on('bahan')->onDelete('cascade');
            $table->integer('jumlah');
            $table->timestamps(); 
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('belanja_requests');
        Schema::dropIfExists('bahan_masuk');
        Schema::dropIfExists('menu_detail');
        Schema::dropIfExists('produksi');
        Schema::dropIfExists('menu');
        Schema::dropIfExists('bahan');
        Schema::dropIfExists('users');
    }
};