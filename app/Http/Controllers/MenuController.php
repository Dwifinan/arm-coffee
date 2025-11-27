<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Bahan;
use App\Models\Komposisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    // ==================================================
    // WEB METHODS (Untuk form, view HTML, dan CRUD utama)
    // ==================================================

    /**
     * Menampilkan form untuk membuat menu baru (dan komposisi).
     */
    public function create()
    {
        $bahans = Bahan::all();
        return view('pages.create_menu', compact('bahans'));
    }

    /**
     * Menyimpan menu baru dan komposisinya menggunakan database transaction.
     */
    public function store(Request $request)
    {
        // 1. Validasi Data
        $request->validate([
            'nama' => 'required|string|max:100|unique:menu,nama',
            'harga' => 'required|numeric|min:1000',
            'bahan_id' => 'required|array|min:1',
            'bahan_id.*' => 'required|exists:bahan,id',
            'jumlah_bahan' => 'required|array|min:1',
            'jumlah_bahan.*' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 2. Simpan Data Menu Utama
            $menu = Menu::create([
                'nama' => $request->nama,
                'harga' => $request->harga,
                'tersedia' => 1,
            ]);

            // 3. Simpan Komposisi Menu (Details)
            foreach ($request->bahan_id as $index => $bahan_id) {
                if (isset($request->jumlah_bahan[$index])) {
                    // Cek duplikasi bahan_id dalam satu menu request.
                    if (in_array($bahan_id, array_slice($request->bahan_id, 0, $index))) {
                         DB::rollBack();
                         return redirect()->back()->withInput()->with('error', 'Gagal menyimpan menu: Terdapat bahan baku ganda yang dipilih.');
                    }

                    Komposisi::create([
                        'menu_id' => $menu->id,
                        'bahan_id' => $bahan_id,
                        'jumlah_bahan' => $request->jumlah_bahan[$index],
                        'satuan' => '', // Kolom 'satuan' di Model Komposisi.php
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('menu.index')
                ->with('success', 'Menu "' . $menu->nama . '" dan komposisinya berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan menu dan komposisi: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail menu.
     */
    public function show(Menu $menu)
    {
        return view('pages.menu_detail', compact('menu'));
    }

    /**
     * Menampilkan form edit menu (dan komposisi).
     */
    public function edit(Menu $menu)
    {
        $bahans = Bahan::all();
        return view('pages.edit_menu', compact('menu', 'bahans'));
    }

    /**
     * Memperbarui menu dan komposisinya menggunakan database transaction.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:menu,nama,' . $menu->id,
            'harga' => 'required|numeric|min:1000',
            'bahan_id' => 'nullable|array',
            'bahan_id.*' => 'required|exists:bahan,id',
            'jumlah_bahan' => 'nullable|array',
            'jumlah_bahan.*' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 2. Update Data Menu Utama
            $menu->update([
                'nama' => $request->nama,
                'harga' => $request->harga,
            ]);

            // 3. Hapus semua komposisi lama
            $menu->details()->delete();

            // 4. Simpan Komposisi Menu yang baru
            if ($request->bahan_id) {
                foreach ($request->bahan_id as $index => $bahan_id) {
                    if (isset($request->jumlah_bahan[$index])) {
                        // Cek duplikasi bahan_id dalam satu menu request.
                        if (in_array($bahan_id, array_slice($request->bahan_id, 0, $index))) {
                            DB::rollBack();
                            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui menu: Terdapat bahan baku ganda yang dipilih.');
                        }

                        Komposisi::create([
                            'menu_id' => $menu->id,
                            'bahan_id' => $bahan_id,
                            'jumlah_bahan' => $request->jumlah_bahan[$index],
                            'satuan' => '',
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('menu.index')
                ->with('success', 'Menu "' . $menu->nama . '" berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui menu: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus menu dan komposisinya menggunakan transaction.
     * FIX: Menerima $id untuk mengatasi isu Route Model Binding pada rute API kustom.
     */
    public function destroy($id)
    {
        $menu = Menu::find($id); // Cari model secara manual

        if (!$menu) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        try {
            DB::beginTransaction();
            $namaMenu = $menu->nama;

            // Hapus komposisi terlebih dahulu
            $menu->details()->delete(); //

            // Hapus menu utama
            $menu->delete();
            DB::commit();

            return response()->json([
                'message' => "Menu '$namaMenu' berhasil dihapus"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghapus menu: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================================================
    // API METHODS (Untuk DataTables AJAX)
    // ==================================================

    /**
     * Mengambil semua menu (API untuk DataTables).
     */
    public function getMenuData()
    {
        $data = Menu::orderBy('id', 'DESC')->get();

        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * Mengambil detail menu (API)
     */
    public function showApi($id)
    {
        $menu = Menu::find($id);
        if (!$menu) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
        return response()->json(['data' => $menu]);
    }

    /**
     * Menyimpan menu (API modal lama)
     */
    public function storeApi(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'harga' => 'required|numeric|min:0',
            'tersedia' => 'required|in:0,1',
        ]);
        $menu = Menu::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'tersedia' => $request->tersedia,
        ]);
        return response()->json([
            'message' => 'Menu berhasil ditambahkan',
            'data' => $menu
        ]);
    }

    /**
     * Memperbarui menu (API modal lama)
     */
    public function updateApi(Request $request, $id)
    {
        $menu = Menu::find($id);
        if (!$menu) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
        $menu->update([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'tersedia' => $request->tersedia,
        ]);
        return response()->json([
            'message' => 'Menu berhasil diupdate',
            'data' => $menu
        ]);
    }
}
