<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class BahanController extends Controller
{
    /**
     * Menampilkan daftar semua bahan baku.
     */
public function getBahan()
    {
        $bahanAll = Bahan::all();
        return response()->json([
            'message' => 'success',
            'data' => $bahanAll
        ]);
    }

    public function addBahan(Request $request)
    {
        $dataFilter = $request->validate([
            "kode" => "required|string|max:50|unique:bahan,kode",
            "nama" => "required|string|max:50",
            "satuan" => "required|string|max:50",
            "harga_persatuan" => "required|numeric|min:0",
            "jumlah_satuan" => "required|numeric|min:1",
            "stok_minimal" => "required|integer|min:0",
        ]);

        $simpan = Bahan::create($dataFilter);

        return response()->json([
            'message' => 'created',
            'data' => $simpan
        ]);
    }

    // ============================
    // GET DETAIL BAHAN (EDIT)
    // ============================
    public function getDetailBahan($id)
    {
        $bahan = Bahan::find($id);

        if (!$bahan) {
            return response()->json([
                'message' => 'not found'
            ], 404);
        }

        return response()->json([
            'message' => 'success',
            'data' => $bahan
        ]);
    }

    // ============================
    // UPDATE BAHAN
    // ============================
    public function updateBahan(Request $request, $id)
    {
        $bahan = Bahan::find($id);

        if (!$bahan) {
            return response()->json([
                'message' => 'not found'
            ], 404);
        }

        $dataFilter = $request->validate([
            "kode" => "required|string|max:50|unique:bahan,kode," . $id,
            "nama" => "required|string|max:50",
            "satuan" => "required|string|max:50",
            "harga_persatuan" => "required|numeric|min:0",
            "jumlah_satuan" => "required|numeric|min:1",
            "stok_minimal" => "required|integer|min:0",
        ]);

        $bahan->update($dataFilter);

        return response()->json([
            'message' => 'updated',
            'data' => $bahan
        ]);
    }

    // ============================
    // HAPUS BAHAN
    // ============================
    public function deleteBahan($id)
    {
        $bahan = Bahan::find($id);

        if (!$bahan) {
            return response()->json([
                'message' => 'not found'
            ], 404);
        }

        $bahan->delete();

        return response()->json([
            'message' => 'deleted'
        ]);
    }
    public function index()
    {
        // Mengambil semua data bahan baku
        return view('pages.bahan', [
            "bahan" => Bahan::all()
        ]);
    }

    /**
     * Menyimpan bahan baku baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Data dengan Custom Messages
        $validated = $request->validate([
            "kode" => "required|string|max:50|unique:bahan,kode",
            "nama" => "required|string|max:50",
            "satuan" => "required|string|max:50",
            "low_limit" => "required|integer|min:0",
        ], [
            "kode.required" => "Kode Bahan Harus Di Isi",
            "kode.unique" => "Kode Bahan sudah ada. Gunakan kode lain.",
            "nama.required" => "Nama Bahan Harus Di Isi",
            "satuan.required" => "Satuan Bahan Harus Di Isi",
            "low_limit.required" => "Minimal stok Bahan Harus Di Isi",
            "low_limit.numeric" => "Minimal stok harus berupa angka.",
        ]);

        try {
            // 2. Tambahkan stok awal 0 sebelum disimpan
            $validated['stok'] = 0;
            Bahan::create($validated);

            // 3. Redirect dengan pesan sukses
            return redirect()->route('bahan.index')
                ->with('success', 'Bahan baku "' . $validated['nama'] . '" berhasil ditambahkan.');

        } catch (\Exception $e) {
            // Jika terjadi kegagalan database, kembali dengan error
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan bahan: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui bahan baku yang ada.
     */
    public function update(Request $request, string $id)
    {
        $bahan = Bahan::findOrFail($id);

        // 1. Validasi Data dengan Custom Messages
        $validated = $request->validate([
            // Kode unik, kecuali kode milik bahan ini sendiri (PENTING untuk EDIT)
            "kode" => [
                "required",
                "string",
                "max:50",
                Rule::unique('bahan', 'kode')->ignore($bahan->id)
            ],
            "nama" => "required|string|max:50",
            "satuan" => "required|string|max:50",
            "low_limit" => "required|integer|min:0",
        ], [
            "kode.required" => "Kode Bahan Harus Di Isi",
            "kode.unique" => "Kode Bahan sudah ada. Gunakan kode lain.",
            "nama.required" => "Nama Bahan Harus Di Isi",
            "satuan.required" => "Satuan Bahan Harus Di Isi",
            "low_limit.required" => "Minimal stok Bahan Harus Di Isi",
            "low_limit.numeric" => "Minimal stok harus berupa angka.",
        ]);

        try {
            // 2. Update data
            $bahan->update($validated);

            // 3. Redirect dengan pesan sukses
            return redirect()->route('bahan.index')
                ->with('success', 'Bahan "' . $bahan->nama . '" berhasil diperbarui.');

        } catch (\Exception $e) {
            // Jika terjadi kegagalan database, kembali dengan error
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui bahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus bahan baku.
     */
    public function destroy(string $id)
    {
        try {
            $bahan = Bahan::findOrFail($id);
            $nama = $bahan->nama;
            $bahan->delete();

            // Respon JSON untuk fetch/SweetAlert (Sesuai dengan kode view yang Anda kirim)
            return response()->json(['success' => true, 'message' => "Bahan baku '$nama' berhasil dihapus."]);
        } catch (\Exception $e) {
            // Respon error JSON
            return response()->json(['success' => false, 'message' => 'Gagal menghapus bahan: ' . $e->getMessage()], 500);
        }
    }

    // ============================
    // CEK DETAIL BATCH (EXPIRY)
    // ============================
    public function getBatches($id)
    {
        // Ambil data bahan masuk berdasarkan bahan_id
        // Urutkan berdasarkan tanggal expired terdekat (ASC)
        $batches = \App\Models\BahanMasuk::where('bahan_id', $id)
                    ->orderBy('expired', 'asc')
                    ->get();
        
        return response()->json([
            'message' => 'success',
            'data' => $batches
        ]);
    }

    // ============================
    // GET STOCK HISTORY (KARTU STOK)
    // ============================
    public function getHistory($id)
    {
        $masuk = \App\Models\BahanMasuk::where('bahan_id', $id)
                    ->select('created_at', 'jumlah', 'expired', DB::raw("'Masuk' as type"), DB::raw("'Pembelian/Stok Awal' as keterangan"))
                    ->get();

        $keluar = \App\Models\BahanKeluar::where('bahan_id', $id)
                    ->select('created_at', 'jumlah', 'expired', DB::raw("'Keluar' as type"), DB::raw("'Produksi/Buang' as keterangan"))
                    ->get();

        // Merge and Sort
        $history = $masuk->concat($keluar)->sortByDesc('created_at')->values();

        return response()->json([
            'message' => 'success',
            'data' => $history
        ]);
    }

    public function resolveBatch(Request $request, $id)
    {
        $request->validate([
            'qty_disposed' => 'required|integer|min:0'
        ]);

        $batch = \App\Models\BahanMasuk::findOrFail($id);
        $batch->is_resolved = true;
        $batch->save();

        if ($request->qty_disposed > 0) {
            $bahan = Bahan::find($batch->bahan_id);
            if ($bahan) {
                // Reduce Master Stock
                $bahan->stok = max(0, $bahan->stok - $request->qty_disposed);
                $bahan->save();

                // Reduce Sisa Stok on the Batch (FIFO Consistency)
                $batch->sisa_stok = max(0, $batch->sisa_stok - $request->qty_disposed);
                $batch->save();

                // Log Bahan Keluar
                \App\Models\BahanKeluar::create([
                    'bahan_id' => $bahan->id,
                    'jumlah' => $request->qty_disposed,
                    'harga' => 0, // Loss
                    'harga_satuan' => 0,
                    'total_harga' => 0,
                    'expired' => $batch->expired,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
}
