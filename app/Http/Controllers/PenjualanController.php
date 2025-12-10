<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Menu;
use App\Models\Komposisi;
use App\Models\Bahan;
use App\Models\BahanKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PenjualanController extends Controller
{
    /**
     * Endpoint untuk DataTables Laporan Produksi (GET /api/penjualan-produksi)
     * Mengambil data penjualan yang sudah di-JOIN dengan nama menu.
     */
    public function produksiReport()
    {
        $data = Penjualan::select(
            'penjualan.id',

            'penjualan.kode_transaksi',
            'penjualan.created_at',
            'penjualan.jumlah',
            'penjualan.total_harga',
            'menu.nama as menu_nama'
        )
        ->join('menu', 'penjualan.menu_id', '=', 'menu.id')
        ->orderBy('penjualan.created_at', 'desc')
        ->get();

        return response()->json(['data' => $data]);
    }

    /**
     * Endpoint untuk Menyimpan Input Penjualan/Produksi Baru (POST /api/penjualan)
     * Ini memicu pengurangan stok bahan baku.
     */
    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menu,id',
            'jumlah' => 'required|integer|min:1',
            'total_harga' => 'required|numeric|min:0',
            // Tambahkan validasi user_id jika ada
        ]);

        // Memastikan semua operasi database berjalan sukses atau tidak sama sekali
        DB::beginTransaction();

        try {
            // 1. Catat Penjualan (Log Produksi)
            $penjualan = Penjualan::create([
                'kode_transaksi' => Str::upper(Str::random(10)),
                'menu_id' => $request->menu_id,
                'jumlah' => $request->jumlah,
                'total_harga' => $request->total_harga,
                // 'user_id' => auth()->id(), // Tambahkan jika Anda menggunakan autentikasi
            ]);

            // 2. LOGIKA PENGURANGAN STOK BAHAN BAKU
            $menu_id = $request->menu_id;
            $jumlah_terjual = $request->jumlah;

            // A. Ambil Komposisi (Resep)
            $komposisi = Komposisi::where('menu_id', $menu_id)->get();

            if ($komposisi->isEmpty()) {
                throw new \Exception("Resep (Komposisi) untuk menu ini tidak ditemukan. Harap cek tabel komposisi.");
            }

            foreach ($komposisi as $kompo) {
                $bahan_id = $kompo->bahan_id;
                $jumlah_bahan_terpakai = $kompo->jumlah_bahan * $jumlah_terjual;

                $bahan = Bahan::find($bahan_id);
                if (!$bahan) {
                    throw new \Exception("Bahan baku ID {$bahan_id} tidak ditemukan.");
                }

                // Cek ketersediaan stok sebelum pengurangan
                if ($bahan->stok < $jumlah_bahan_terpakai) {
                    throw new \Exception("Stok {$bahan->nama} tidak cukup ({$bahan->stok} unit tersedia). Gagal mencatat produksi.");
                }

                // B. Catat Bahan Keluar dan Kurangi Stok
                $bahan->stok -= $jumlah_bahan_terpakai;
                $bahan->save();

                // C. Catat Log Bahan Keluar
                BahanKeluar::create([
                    'bahan_id' => $bahan_id,
                    'jumlah' => $jumlah_bahan_terpakai,
                    // Di dunia nyata, Anda akan menghitung harga COGS di sini.
                    'harga' => 0,
                    'harga_satuan' => null, // DITAMBAHKAN: Mengatasi potensi error karena kolom ini ada di skema
                    'total_harga' => 0,
                    'expired' => now()->addDays(30),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penjualan/Produksi berhasil dicatat dan stok bahan baku dikurangi.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() // Mengembalikan pesan error spesifik ke frontend
            ], 400);
        }
    }
}
