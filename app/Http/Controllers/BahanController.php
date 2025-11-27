<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    // ============================
    // TAMBAH BAHAN
    // ============================
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

        // Hitung harga per satuan
        $dataFilter['harga_satuan'] = $dataFilter['harga_persatuan'] / $dataFilter['jumlah_satuan'];

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

        // Hitung ulang harga satuan
        $dataFilter['harga_satuan'] = $dataFilter['harga_persatuan'] / $dataFilter['jumlah_satuan'];

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
}
