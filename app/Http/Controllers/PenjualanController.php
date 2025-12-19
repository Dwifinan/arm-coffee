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
     * Endpoint untuk DataTables Laporan Produksi HARIAN (GET /api/penjualan-produksi)
     * Mengelompokkan berdasarkan TANGGAL (created_at)
     */
    public function produksiReport()
    {
        $data = Penjualan::select(
            DB::raw('DATE(created_at) as tgl'), 
            DB::raw('SUM(jumlah) as total_items'),
            DB::raw('SUM(total_harga) as total_pendapatan'),
            DB::raw('COUNT(*) as form_count')
        )
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('tgl', 'desc')
        ->get();

        return response()->json(['data' => $data]);
    }

    /**
     * Endpoint untuk Menampilkan Detail Harian (GET /api/penjualan-produksi/{date})
     */
    public function batchDetails($date)
    {
        // $date format YYYY-MM-DD
        $details = Penjualan::with('menu')
                    ->whereDate('created_at', $date)
                    ->get();
        
        return response()->json(['data' => $details]);
    }

    /**
     * Endpoint untuk Menyimpan Input Penjualan/Produksi Baru (POST /api/penjualan)
     */
    public function store(Request $request)
    {
        // Check if it is a bulk request
        if ($request->has('items') && is_array($request->items)) {
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.menu_id' => 'required|exists:menu,id',
                'items.*.jumlah' => 'required|integer|min:1',
                'items.*.total_harga' => 'required|numeric|min:0',
            ]);

            $itemsToProcess = $request->items;
        } else {
            // Fallback for single item
            $request->validate([
                'menu_id' => 'required|exists:menu,id',
                'jumlah' => 'required|integer|min:1',
                'total_harga' => 'required|numeric|min:0',
            ]);
            $itemsToProcess = [
                [
                    'menu_id' => $request->menu_id,
                    'jumlah' => $request->jumlah,
                    'total_harga' => $request->total_harga
                ]
            ];
        }

        DB::beginTransaction();

        try {
            $batch_id = 'BATCH-' . Str::upper(Str::random(8));

            foreach ($itemsToProcess as $item) {
                $kodeTransaksi = Str::upper(Str::random(10));

                // 1. Catat Penjualan
                $penjualan = Penjualan::create([
                    'kode_transaksi' => $kodeTransaksi,
                    'batch_id' => $batch_id,
                    'menu_id' => $item['menu_id'],
                    'jumlah' => $item['jumlah'],
                    'total_harga' => $item['total_harga'],
                ]);

                // 2. STOK BAHAN LOGIC
                $menu_id = $item['menu_id'];
                $jumlah_terjual = $item['jumlah'];
                
                $komposisi = Komposisi::where('menu_id', $menu_id)->get();

                if ($komposisi->isEmpty()) {
                    $menuName = Menu::find($menu_id)->nama ?? 'Unknown';
                    throw new \Exception("Resep (Komposisi) untuk menu '$menuName' tidak ditemukan.");
                }

                foreach ($komposisi as $kompo) {
                    $bahan_id = $kompo->bahan_id;
                    $jumlah_bahan_terpakai = $kompo->jumlah_bahan * $jumlah_terjual;

                    $bahan = Bahan::find($bahan_id);
                    if (!$bahan) {
                        throw new \Exception("Bahan baku ID {$bahan_id} tidak ditemukan.");
                    }

                    // A. Cek Total Stok (Cached)
                    if ($bahan->stok < $jumlah_bahan_terpakai) {
                        throw new \Exception("Stok '{$bahan->nama}' tidak cukup ({$bahan->stok} tersedia, butuh $jumlah_bahan_terpakai).");
                    }

                    // B. FIFO / FEFO LOGIC (First Expired First Out)
                    $sisaKebutuhan = $jumlah_bahan_terpakai; // Awalnya butuh full
                    
                    // Ambil batch yang masih ada sisa, urutkan dari yang paling cepat expired
                    $antrianBatch = \App\Models\BahanMasuk::where('bahan_id', $bahan_id)
                                ->where('sisa_stok', '>', 0)
                                ->orderBy('expired', 'asc')
                                ->orderBy('created_at', 'asc')
                                ->lockForUpdate() // Cegah race condition
                                ->get();

                    foreach ($antrianBatch as $batch) {
                        if ($sisaKebutuhan <= 0) break;

                        if ($batch->sisa_stok >= $sisaKebutuhan) {
                            // Batch ini cukup untuk memenuhi sisa kebutuhan
                            $batch->decrement('sisa_stok', $sisaKebutuhan);
                            $sisaKebutuhan = 0;
                        } else {
                            // Batch ini hanya cukup sebagian, habiskan!
                            $stokDiambil = $batch->sisa_stok;
                            $batch->update(['sisa_stok' => 0]);
                            $sisaKebutuhan -= $stokDiambil;
                        }
                    }

                    // Safety Check: Jika setelah loop masih kurang (artinya data cached stok tidak sinkron dengan fisik batch)
                    // Safety Check: Jika setelah loop masih kurang (artinya data cached stok tidak sinkron dengan fisik batch)
                    if ($sisaKebutuhan > 0) {
                         // Force fix master stock or throw error?
                         // Throw error is safer to detect anomaly
                         throw new \Exception("Inkonsistensi Data: Stok Master cukup tapi Batch kosong. Hubungi teknisi.");
                    }

                    // C. Update Master Stock (untuk display cepat)
                    $bahan->stok -= $jumlah_bahan_terpakai;
                    $bahan->save();

                    // D. Log Keluar (History Only - No Logic uses this anymore, just for report)
                    BahanKeluar::create([
                        'bahan_id' => $bahan_id,
                        'jumlah' => $jumlah_bahan_terpakai,
                        'harga' => 0,
                        'harga_satuan' => null,
                        'total_harga' => 0,
                        'expired' => now(), // Not relevant for OUT log usually
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($itemsToProcess) . ' Item Produksi berhasil dicatat (FIFO).'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Endpoint untuk Menghapus/Void Produksi HARIAN (DELETE /api/penjualan-produksi/{date})
     * Logic: Hapus SEMUA data penjualan di tanggal tsb dan kembalikan stok.
     */
    public function destroy($date)
    {
        // Validate date string if needed, simple approach:
        $transaksi = Penjualan::whereDate('created_at', $date)->get();

        if ($transaksi->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Data produksi tidak ditemukan untuk tanggal ini.'], 404);
        }

        DB::beginTransaction();
        try {
            // Restore Stok
            foreach ($transaksi as $item) {
                // Ambil resep
                $komposisi = Komposisi::where('menu_id', $item->menu_id)->get();
                
                foreach ($komposisi as $resep) {
                    $bahan = Bahan::find($resep->bahan_id);
                    if ($bahan) {
                        $qtyRestore = $resep->jumlah_bahan * $item->jumlah;
                        
                        $bahan->stok += $qtyRestore;
                        $bahan->save();

                        // Catat Void Masuk (Return Stock)
                        \App\Models\BahanMasuk::create([
                            'bahan_id' => $bahan->id,
                            'jumlah' => $qtyRestore,
                            'sisa_stok' => $qtyRestore, // Restore full amount to sisa
                            'harga' => 0,
                            'harga_satuan' => 0,
                            'total_harga' => 0,
                            'expired' => now()->addDays(30), // Default safe, or ideally fetch original expiry if complex tracking
                        ]);
                    }
                }
            }

            // Hapus Record Penjualan
            Penjualan::whereDate('created_at', $date)->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Seluruh data produksi tanggal '.$date.' berhasil dihapus/void.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()], 500);
        }
    }
}
