<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Bahan;
use App\Models\MenuDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    /**
     * Menampilkan daftar semua menu.
     */
    public function index()
    {
        $menus = Menu::all();
        return view('pages.menu', compact('menus'));
    }

    /**
     * Menampilkan formulir untuk menambah menu baru.
     */
    public function create()
    {
        $bahans = Bahan::orderBy('nama', 'asc')->get();
        return view('pages.create_menu', compact('bahans'));
    }

    /**
     * Menyimpan menu dan detail bahan yang digunakan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50|unique:menu',
            'harga' => 'required|integer|min:1000',
            'bahan_id' => 'required|array',
            'bahan_id.*' => 'nullable|exists:bahan,id',
            'jumlah_bahan' => 'required|array',
            'jumlah_bahan.*' => 'nullable|integer|min:1',
        ], [
            'nama.unique' => 'Nama menu sudah ada.',
            'bahan_id.required' => 'Menu harus memiliki minimal satu bahan baku.',
        ]);

        try {
            DB::beginTransaction();
            $menu = Menu::create([
                'nama' => $request->nama,
                'harga' => $request->harga,
            ]);

            $menuDetails = [];
            foreach ($request->bahan_id as $index => $bahanId) {
                $jumlah = $request->jumlah_bahan[$index];

                if (!empty($bahanId) && $jumlah >= 1) {
                    $menuDetails[] = [
                        'menu_id' => $menu->id,
                        'bahan_id' => $bahanId,
                        'jumlah' => $jumlah,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (empty($menuDetails)) {
                 DB::rollBack();
                 return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal menyimpan menu. Pastikan Anda memilih bahan dan memasukkan jumlah yang valid.');
            }

            MenuDetail::insert($menuDetails);
            DB::commit();

            return redirect()->route('menu.index')
                ->with('success', 'Menu "' . $menu->nama . '" berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan menu. Error: ' . $e->getMessage());
        }
    }

    // =========================================================
    // BARU: SHOW (DETAIL)
    // =========================================================
    /**
     * Menampilkan detail menu beserta bahan baku yang digunakan.
     */
    public function show(Menu $menu)
    {
        // Memuat relasi details dan bahan yang terhubung
        $menu->load('details.bahan');
        return view('pages.menu_detail', compact('menu'));
    }

    // =========================================================
    // BARU: EDIT DAN UPDATE
    // =========================================================
    /**
     * Menampilkan formulir edit menu.
     */
    public function edit(Menu $menu)
    {
        // Muat relasi detail menu dan bahan baku yang digunakan
        $menu->load('details');
        // Ambil semua bahan yang tersedia untuk dropdown
        $bahans = Bahan::orderBy('nama', 'asc')->get();

        return view('pages.edit_menu', compact('menu', 'bahans'));
    }

    /**
     * Memperbarui menu dan detail bahan yang digunakan.
     */
    public function update(Request $request, Menu $menu)
    {
        // 1. Validasi Data
        $request->validate([
            // Nama harus unik, kecuali untuk menu yang sedang di-edit
            'nama' => ['required', 'string', 'max:50', Rule::unique('menu')->ignore($menu->id)],
            'harga' => 'required|integer|min:1000',
            'bahan_id' => 'required|array',
            'bahan_id.*' => 'nullable|exists:bahan,id',
            'jumlah_bahan' => 'required|array',
            'jumlah_bahan.*' => 'nullable|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 2. Update Menu Utama
            $menu->update([
                'nama' => $request->nama,
                'harga' => $request->harga,
            ]);

            // 3. Hapus detail lama, dan masukkan yang baru
            $menu->details()->delete();

            $menuDetails = [];
            foreach ($request->bahan_id as $index => $bahanId) {
                $jumlah = $request->jumlah_bahan[$index];

                if (!empty($bahanId) && $jumlah >= 1) {
                    $menuDetails[] = [
                        'menu_id' => $menu->id,
                        'bahan_id' => $bahanId,
                        'jumlah' => $jumlah,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (empty($menuDetails)) {
                 DB::rollBack();
                 return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal menyimpan menu. Menu harus memiliki minimal satu bahan baku yang valid.');
            }

            MenuDetail::insert($menuDetails);
            DB::commit();

            return redirect()->route('menu.index')
                ->with('success', 'Menu "' . $menu->nama . '" berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui menu. Error: ' . $e->getMessage());
        }
    }

    // =========================================================
    // BARU: DESTROY (HAPUS)
    // =========================================================
    /**
     * Menghapus menu dan semua detail bahan terkait.
     */
    public function destroy(Menu $menu)
    {
        try {
            // Karena relasi sudah diatur ON DELETE CASCADE di database (menu_detail),
            // menghapus menu juga akan menghapus detailnya.
            $menu->delete();
            return redirect()->route('menu.index')
                ->with('success', 'Menu "' . $menu->nama . '" berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus menu. Error: ' . $e->getMessage());
        }
    }
}
