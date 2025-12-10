<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Metode untuk API DataTables (GET /api/menu)
     * Mengambil semua data menu untuk ditampilkan di tabel utama (menu.blade.php)
     */
    public function getMenuData()
    {
        // Ambil semua data menu
        $menu = Menu::select('id', 'nama', 'harga', 'tersedia')->get();

        return response()->json([
            'data' => $menu
        ]);
    }

    /**
     * Metode untuk API Dropdown Produksi (GET /api/menu-list)
     * Mengambil daftar menu yang tersedia (hanya ID, nama, dan harga) untuk dropdown input produksi.
     */
    public function getMenuList()
    {
        // Ambil ID, nama, dan harga hanya untuk menu yang tersedia (tersedia = 1)
        $menuList = Menu::select('id', 'nama', 'harga')
                      ->where('tersedia', 1)
                      ->orderBy('nama')
                      ->get();

        return response()->json([
            'success' => true,
            'data' => $menuList
        ]);
    }

    // --- Skeleton CRUD Web Routes ---

    // Menangani tampilan utama DataTables (GET /menu)
    public function index()
    {
        return view('pages.menu');
    }

    // Menangani form tambah menu (GET /menu/create)
    public function create()
    {
        // Logika untuk menampilkan form tambah
    }

    // Menangani penyimpanan menu baru (POST /menu)
    public function store(Request $request)
    {
        // Logika penyimpanan menu dan komposisi
    }

    // Menangani detail menu (GET /menu/{menu})
    public function show(Menu $menu)
    {
        // Logika menampilkan detail menu
    }

    // Menangani form edit menu (GET /menu/{menu}/edit)
    public function edit(Menu $menu)
    {
        // Logika menampilkan form edit
    }

    // Menangani update menu (PUT /menu/{menu})
    public function update(Request $request, Menu $menu)
    {
        // Logika update menu dan komposisi
    }

    // Menangani penghapusan menu melalui API (DELETE /api/delete-menu/{id})
    public function destroy($id)
    {
        // Logika penghapusan menu dan komposisinya
        try {
            $menu = Menu::findOrFail($id);
            $menu->komposisi()->delete(); // Hapus komposisi terkait
            $menu->delete();
            return response()->json(['success' => true, 'message' => 'Menu berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Skeleton API untuk form edit (GET /api/menu/{id})
    public function showApi($id)
    {
        // Logika untuk mengambil detail menu untuk form edit API
    }

    // Skeleton API untuk menyimpan menu baru (POST /api/add-menu)
    public function storeApi(Request $request)
    {
        // Logika untuk menyimpan menu melalui API
    }

    // Skeleton API untuk update menu (POST /api/update-menu/{id})
    public function updateApi(Request $request, $id)
    {
        // Logika untuk update menu melalui API
    }
}
