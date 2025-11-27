<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Komposisi;
use App\Models\Bahan;
use App\Models\Menu;

class KomposisiController extends Controller
{
    /**
     * GET KOMPOSISI PER MENU
     */
    public function getKomposisi($menu_id)
    {
        $komposisi = Komposisi::with('bahan')
            ->where('menu_id', $menu_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $komposisi
        ]);
    }

    /**
     * ADD KOMPOSISI BARU
     */
    public function addKomposisi(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|integer',
            'bahan_id' => 'required|integer',
            'jumlah' => 'required|numeric|min:0.01'
        ]);

        // Ambil harga satuan bahan
        $bahan = Bahan::find($request->bahan_id);

        if (!$bahan) {
            return response()->json(['success' => false, 'message' => 'Bahan tidak ditemukan'], 404);
        }

        // Hitung subtotal = jumlah × harga_satuan
        $subtotal = $request->jumlah * $bahan->harga_satuan;

        $komposisi = Komposisi::create([
            'menu_id' => $request->menu_id,
            'bahan_id' => $request->bahan_id,
            'jumlah' => $request->jumlah,
            'subtotal' => $subtotal,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Komposisi berhasil ditambahkan',
            'data' => $komposisi
        ]);
    }

    /**
     * UPDATE KOMPOSISI
     */
    public function updateKomposisi(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:0.01'
        ]);

        $komposisi = Komposisi::find($id);

        if (!$komposisi) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        // Ambil harga satuan bahan
        $bahan = Bahan::find($komposisi->bahan_id);
        $subtotal = $request->jumlah * $bahan->harga_satuan;

        $komposisi->update([
            'jumlah' => $request->jumlah,
            'subtotal' => $subtotal
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Komposisi berhasil diupdate',
            'data' => $komposisi
        ]);
    }

    /**
     * DELETE KOMPOSISI
     */
    public function deleteKomposisi($id)
    {
        $komposisi = Komposisi::find($id);

        if (!$komposisi) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $komposisi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Komposisi berhasil dihapus'
        ]);
    }
}
