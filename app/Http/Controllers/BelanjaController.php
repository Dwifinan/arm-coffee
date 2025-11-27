<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Bahan; 
use App\Models\BahanMasuk; 
use App\Models\BelanjaRequest; // Model yang digunakan untuk Request Aktif (DB Shared)
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Validation\Rule;

class BelanjaController extends Controller
{
    /**
     * Menampilkan Histori Pembelian (route: belanja.index).
     * Dibatasi untuk Owner saja.
     */
    public function index()
    {
        // Owner melihat Histori Permanen dari database (BahanMasuk)
        $historiBelanja = BahanMasuk::with('bahan')
                            ->orderBy('created_at', 'desc')
                            ->get();
        
        // Variabel ini tidak dipakai di view ini, tapi dikirim untuk konsistensi
        $requestList = collect([]); 

        return view('pages.belanja', compact('historiBelanja', 'requestList'));
    }

    /**
     * Menampilkan Form Tambah dan Daftar Request Aktif (route: belanja.request).
     * Diakses oleh Owner (untuk tambah/hapus) dan Staff (untuk selesai).
     */
    public function request()
    {
        $bahan = Bahan::all();
        
        // SEKARANG DARI DATABASE: Ambil semua request aktif dari tabel 'belanja_requests'
        $requestList = BelanjaRequest::with('bahan')->get();
        
        // Mengirim data ke view request aktif
        return view('pages.belanja_request', compact('bahan', 'requestList'));
    }

    public function create()
    {
        // Method ini menangkap panggilan ke route lama (belanja.create) 
        // dan mengarahkan ke route yang benar (belanja.request)
        return redirect()->route('belanja.request');
    }

    public function tambah(Request $request)
    {
        if (Auth::user()->role !== 'owner') {
             return redirect()->route('belanja.request')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'bahan_id' => 'required|exists:bahan,id',
            'jumlah' => 'required|numeric|min:1'
        ]);

        try {
            // KE DATABASE: Simpan request aktif
            BelanjaRequest::create([
                'bahan_id' => $request->bahan_id,
                'jumlah' => $request->jumlah
            ]);

            return redirect()->route('belanja.request')->with('success', 'Request berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan request: ' . $e->getMessage());
        }
    }

    public function hapus($id)
    {
        if (Auth::user()->role !== 'owner') {
             return redirect()->route('belanja.request')->with('error', 'Akses ditolak.');
        }
        
        try {
            // DARI DATABASE: Hapus request berdasarkan ID
            BelanjaRequest::findOrFail($id)->delete();
            return redirect()->route('belanja.request')->with('success', 'Request berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('belanja.request')->with('error', 'Gagal menghapus request: ' . $e->getMessage());
        }
    }

    public function selesai($id)
    {
        if (Auth::user()->role !== 'staff') {
             return redirect()->route('belanja.request')->with('error', 'Akses ditolak.');
        }

        $requestItem = BelanjaRequest::with('bahan')->find($id);

        if ($requestItem) {
            try {
                DB::beginTransaction();

                // 1. Catat ke Histori (Tabel BahanMasuk)
                BahanMasuk::create([
                    'bahan_id' => $requestItem->bahan_id,
                    'jumlah' => $requestItem->jumlah,
                    'harga' => 0, // Perlu di-input jika fungsionalitas di-perluas
                    'harga_satuan' => 0, // Perlu di-input jika fungsionalitas di-perluas
                    'total_harga' => 0, // Perlu di-input jika fungsionalitas di-perluas
                    'expired' => now()->addYears(10), // Nilai placeholder sementara
                ]);

                // 2. Update stok bahan
                 $bahan = Bahan::find($requestItem->bahan_id);
                 $bahan->stok += $requestItem->jumlah;
                 $bahan->save();

                 // 3. Hapus dari daftar request aktif di database
                 $requestItem->delete();
                 
                 DB::commit();

                 return redirect()->route('belanja.request')->with('success', 'Bahan berhasil dibeli dan stok diperbarui.');

            } catch (\Exception $e) {
                DB::rollBack();
                 return redirect()->route('belanja.request')->with('error', 'Gagal update stok atau simpan histori: ' . $e->getMessage());
            }
        }

        return redirect()->route('belanja.request')->with('error', 'Item request tidak ditemukan.');
    }
}