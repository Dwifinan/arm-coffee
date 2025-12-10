<?php

use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\KomposisiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BelanjaController;
use App\Http\Controllers\PenjualanController; // WAJIB: Import PenjualanController
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return redirect('/login');
});

// =========================================================
// --- AUTH ---
// =========================================================
Route::get('login', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// =========================================================
// --- DASHBOARD ---
// =========================================================
Route::get('dashboard',[AppController::class, 'dashboardView'])
    ->name('dashboard')
    ->middleware('auth');

// =========================================================
// --- WEB ROUTES ---
// =========================================================
Route::middleware('auth')->group(function () {
    // --- USER MANAGEMENT ---
    Route::resource('users', UserController::class);

    // --- BELANJA (Purchasing) ---
    Route::prefix('belanja')->name('belanja.')->group(function () {
        Route::get('/', [BelanjaController::class, 'index'])->name('index');
        Route::get('/request', [BelanjaController::class, 'request'])->name('request');
        Route::post('/tambah', [BelanjaController::class, 'tambah'])->name('tambah');
        Route::delete('/hapus/{id}', [BelanjaController::class, 'hapus'])->name('hapus');
        Route::post('/selesai/{id}', [BelanjaController::class, 'selesai'])->name('selesai');
        Route::get('/create', [BelanjaController::class, 'create'])->name('create');
    });

    // --- BAHAN (Ingredients) - FULL CRUD ---
    Route::prefix('bahan')->name('bahan.')->group(function () {
        // READ - Tampilan utama DataTables
        Route::get('/', [BahanController::class, 'index'])->name('index');

        // CREATE
        Route::post('/', [BahanController::class, 'store'])->name('store');
        // READ - Edit Form
        Route::get('/{bahan}/edit', [BahanController::class, 'edit'])->name('edit');
        // UPDATE
        Route::put('/{bahan}', [BahanController::class, 'update'])->name('update');
        // DELETE
        Route::delete('/{bahan}', [BahanController::class, 'destroy'])->name('destroy');
    });

    // --- PRODUKSI (Production) ---
    Route::get('produksi', function () { return view('pages.produksi'); })->name('produksi');

    // --- MENU (Web Routes for Detail/Composition Views) ---
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::get('/', function() { return view('pages.menu'); })->name('index'); // List DataTables view
        Route::get('/create', [MenuController::class, 'create'])->name('create'); // Form Tambah Menu
        Route::post('/', [MenuController::class, 'store'])->name('store'); // Simpan Menu & Komposisi
        Route::get('/{menu}', [MenuController::class, 'show'])->name('show'); // Detail Menu
        Route::get('/{menu}/edit', [MenuController::class, 'edit'])->name('edit'); // Form Edit Menu
        Route::put('/{menu}', [MenuController::class, 'update'])->name('update'); // Update Menu & Komposisi
    });
});


// =========================================================
// --- API (Prefix /api) ---
// =========================================================
Route::prefix('api')->middleware('auth')->group(function () {

    // ---------------------------------------------------------
    // BAHAN (Digunakan oleh DataTables dan Form)
    // ---------------------------------------------------------
    Route::get('/bahan', [BahanController::class, 'getBahan']);
    Route::post('/add-bahan', [BahanController::class, 'addBahan']);
    Route::get('/bahan/{id}', [BahanController::class, 'getDetailBahan']);
    Route::post('/update-bahan/{id}', [BahanController::class, 'updateBahan']);
    Route::delete('/delete-bahan/{id}', [BahanController::class, 'deleteBahan']);

    // ---------------------------------------------------------
    // PENJUALAN / PRODUKSI (BARU DITAMBAHKAN)
    // ---------------------------------------------------------
    // 1. Endpoint untuk DataTables Laporan Produksi (GET /api/penjualan-produksi)
    Route::get('/penjualan-produksi', [PenjualanController::class, 'produksiReport']);
    // 2. Endpoint untuk menyimpan input produksi baru (POST /api/penjualan)
    Route::post('/penjualan', [PenjualanController::class, 'store']);

    // ---------------------------------------------------------
    // MENU
    // ---------------------------------------------------------
    Route::get('/menu-list', [MenuController::class, 'getMenuList']);
    Route::get('/menu', [MenuController::class, 'getMenuData']);
    Route::get('/menu/{id}', [MenuController::class, 'showApi']);
    Route::post('/add-menu', [MenuController::class, 'storeApi']);
    Route::post('/update-menu/{id}', [MenuController::class, 'updateApi']);
    Route::delete('/delete-menu/{id}', [MenuController::class, 'destroy'])->name('delete-menu');

    // ---------------------------------------------------------
    // KOMPOSISI MENU
    // ---------------------------------------------------------
    Route::get('/komposisi/{menu_id}', [KomposisiController::class, 'getKomposisi']);
    Route::post('/add-komposisi', [KomposisiController::class, 'addKomposisi']);
    Route::post('/update-komposisi/{id}', [KomposisiController::class, 'updateKomposisi']);
    Route::delete('/delete-komposisi/{id}', [KomposisiController::class, 'deleteKomposisi']);

})->name('api.');
