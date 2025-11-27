<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BelanjaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanController;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\MenuController; 
use App\Models\Bahan;
use App\Models\User;

Route::get('/', function () {
    return redirect('/login');
});


Route::get('login', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

    // Dashboard
    Route::get('dashboard', function () {
        // Ambil data bahan yang stoknya kritis (stok <= low_limit)
        $stokKritis = Bahan::whereNotNull('stok')
                          ->whereColumn('stok', '<=', 'stok_minimal')
                          ->get();

        return view('pages.dashboard', [
            'stokKritis' => $stokKritis // Kirim data ke view dashboard
        ]);
    })->name('dashboard')->middleware('auth');

    // =========================================================
    // --- BELANJA MANAGEMENT (Akses Umum) ---
    // =========================================================
    // Staff actions: Belanja Selesai
    Route::middleware(['role:staff'])->group(function () {
        Route::post('/belanja/selesai/{unique_id}', [BelanjaController::class, 'selesai'])->name('belanja.selesai');
        
        // Redirect Staff ke halaman request aktif ketika mengakses /belanja
        Route::get('/belanja', function(){
            return redirect()->route('belanja.request');
        })->name('belanja.index.staff.redirect');
    });

    // Owner actions: Tambah dan Hapus Request
    Route::middleware(['role:owner'])->group(function(){
        // Owner dapat mengakses Histori Permanen (belanja.index)
        Route::get('/belanja', [BelanjaController::class, 'index'])->name('belanja.index'); 
        
        // Owner dapat membuat dan menghapus request
        Route::post('/belanja/tambah', 'BelanjaController@tambah')->name('belanja.tambah');
        Route::delete('/belanja/hapus/{unique_id}', [BelanjaController::class, 'hapus'])->name('belanja.hapus');
        
        // Route lama yang kini menjadi redirect ke belanja.request
        Route::get('/belanja/create', [BelanjaController::class, 'create'])->name('belanja.create'); 
    });

    // Request Aktif (Dapat diakses Owner dan Staff)
    Route::middleware(['role:owner,staff'])->group(function(){
        Route::get('/belanja/request', [BelanjaController::class, 'request'])->name('belanja.request'); 
        Route::get('/belanja', [BelanjaController::class, 'index'])->name('belanja.index'); 
    });

    // =========================================================
    // --- OWNER MANAGEMENT (CRUD) ---
    // =========================================================
    Route::middleware(['role:owner'])->group(function(){
        // Users Management (CRUD LENGKAP)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create'); // Form Tambah
        Route::post('/users', [UserController::class, 'store'])->name('users.store'); // Simpan Data
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit'); // Form Edit
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update'); // Proses Update
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy'); // Proses Hapus

        // Bahan Management
        Route::get('/bahan', [BahanController::class, "index"])->name('bahan.index');
        Route::post('/bahan', [BahanController::class, "store"])->name('bahan.tambah');
        Route::put('/bahan/{id}', [BahanController::class, 'update'])->name('bahan.update');
        Route::delete('/bahan/{id}', [BahanController::class, 'destroy'])->name('bahan.destroy');
        
        // --- MENU MANAGEMENT (CRUD LENGKAP) ---
        Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
        Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create'); // Menampilkan Form Tambah
        Route::post('/menu', [MenuController::class, 'store'])->name('menu.store'); // Menyimpan Data Baru
        Route::get('/menu/{menu}', [MenuController::class, 'show'])->name('menu.show');
        Route::get('/menu/{menu}/edit', [MenuController::class, 'edit'])->name('menu.edit');
        Route::put('/menu/{menu}', [MenuController::class, 'update'])->name('menu.update');
        Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])->name('menu.destroy');
    });

    // Produksi
    Route::get('/produksi', function () {
        return view('pages.produksi');
    })->name('produksi')->middleware('auth');

Route::get('/logout', function () {
    abort(404);
});


// =========================================================
// --- BELANJA MANAGEMENT (Akses Umum) ---
// =========================================================
Route::prefix('api')->group(function(){
    Route::get('/bahan', [BahanController::class, 'getBahan']);
    Route::post('/add-bahan', [BahanController::class, 'addBahan']);

    Route::get('/bahan/{id}', [BahanController::class, 'getDetailBahan']);
    Route::post('/update-bahan/{id}', [BahanController::class, 'updateBahan']);
    Route::delete('/delete-bahan/{id}', [BahanController::class, 'deleteBahan']);
})->name('api.');
