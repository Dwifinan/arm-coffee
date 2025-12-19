<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
        $bahans = \App\Models\Bahan::all();
        return view('pages.create_menu', compact('bahans'));
    }

    // Menangani penyimpanan menu baru (POST /menu)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'bahan_id' => 'required|array',
            'bahan_id.*' => 'required|exists:bahan,id',
            'jumlah_bahan' => 'required|array',
            'jumlah_bahan.*' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $menu = Menu::create([
                'nama' => $request->nama,
                'harga' => $request->harga,
                'tersedia' => 1
            ]);

            foreach ($request->bahan_id as $index => $bahanId) {
                // Fetch satuan from Bahan logic? Or allow standard from Bahan model
                $bahan = \App\Models\Bahan::find($bahanId);
                
                $menu->komposisi()->create([
                    'bahan_id' => $bahanId,
                    'jumlah_bahan' => $request->jumlah_bahan[$index] ?? 1,
                    'satuan' => $bahan->satuan ?? 'pcs' 
                ]);
            }

            DB::commit();
            return redirect()->route('menu.index')->with('success', 'Menu berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan menu: ' . $e->getMessage())->withInput();
        }
    }

    // Menangani detail menu (GET /menu/{menu})
    public function show(Menu $menu)
    {
        $menu->load('komposisi.bahan');
        return view('pages.menu_detail', compact('menu'));
    }

    // Menangani form edit menu (GET /menu/{menu}/edit)
    public function edit(Menu $menu)
    {
        $bahans = \App\Models\Bahan::all();
        $menu->load('komposisi'); 
        return view('pages.edit_menu', compact('menu', 'bahans'));
    }

    // Menangani update menu (PUT /menu/{menu})
    public function update(Request $request, Menu $menu)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'bahan_id' => 'nullable|array',
            'bahan_id.*' => 'required|exists:bahan,id',
            'jumlah_bahan' => 'nullable|array',
            'jumlah_bahan.*' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $menu->update([
                'nama' => $request->nama,
                'harga' => $request->harga
            ]);

            // Sync Komposisi: Delete all and recreate
            $menu->komposisi()->delete();

            if ($request->has('bahan_id')) {
                foreach ($request->bahan_id as $index => $bahanId) {
                    $bahan = \App\Models\Bahan::find($bahanId);
                    $menu->komposisi()->create([
                        'bahan_id' => $bahanId,
                        'jumlah_bahan' => $request->jumlah_bahan[$index] ?? 1,
                        'satuan' => $bahan->satuan ?? 'pcs'
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('menu.index')->with('success', 'Menu berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal update menu: ' . $e->getMessage())->withInput();
        }
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
}
