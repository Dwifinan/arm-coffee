<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\BahanMasuk;
use App\Models\Belanja;
use App\Models\BelanjaDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BelanjaController extends Controller
{
    // List Request (Owner & Staff)
    public function index()
    {
        // Owner sees all, Staff sees pending tasks (or all?)
        // Let's show all for now, maybe filter by status in view or here
        $belanja = Belanja::with(['userRequest', 'details'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('pages.belanja.index', compact('belanja'));
    }

    // Form Create (Owner)
    public function create()
    {
        if (Auth::user()->role !== 'owner') {
            return redirect()->route('belanja.index')->with('error', 'Akses ditolak.');
        }

        $bahan = Bahan::all();
        return view('pages.belanja.create', compact('bahan'));
    }


    // Store Request (Owner)
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.bahan_id' => 'required|exists:bahan,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Create Header
            $belanja = Belanja::create([
                'kode' => 'REQ-' . date('ymd') . '-' . strtoupper(Str::random(4)),
                'user_id_request' => Auth::id(),
                'status' => 'pending',
                'total_estimasi' => 0 // Hitung nanti
            ]);

            $totalEstimasi = 0;

            foreach ($request->items as $item) {
                // Get Price
                $bahan = Bahan::find($item['bahan_id']);
                $price = $bahan->harga_satuan ?? 0; // Use Satuan Price
                $subtotal = $price * $item['jumlah'];

                BelanjaDetail::create([
                    'belanja_id' => $belanja->id,
                    'bahan_id' => $item['bahan_id'],
                    'jumlah_estimasi' => $item['jumlah'],
                    'harga_satuan_estimasi' => $price,
                    'subtotal_estimasi' => $subtotal,
                ]);

                $totalEstimasi += $subtotal;
            }

            $belanja->update(['total_estimasi' => $totalEstimasi]);

            DB::commit();
            return redirect()->route('belanja.index')->with('success', 'Request Belanja berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat request: ' . $e->getMessage());
        }
    }

    // Show Detail
    public function show($id)
    {
        $belanja = Belanja::with(['details.bahan', 'userRequest', 'userSelesai'])->findOrFail($id);
        return view('pages.belanja.show', compact('belanja'));
    }

    // Form Complete (Staff) -> Using Edit method naming convention
    public function edit($id)
    {
        if (Auth::user()->role !== 'staff') {
            return redirect()->route('belanja.index')->with('error', 'Akses ditolak.');
        }

        $belanja = Belanja::with(['details.bahan'])->where('status', 'pending')->findOrFail($id);
        return view('pages.belanja.complete', compact('belanja'));
    }

    // Process Complete (Staff) -> Update method
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'staff') abort(403);

        $belanja = Belanja::with('details')->findOrFail($id);

        // Unmask Rupiah Format in Items
        if ($request->has('items')) {
            $items = $request->items;
            foreach ($items as $key => $val) {
                if (isset($val['subtotal_akhir'])) {
                    $items[$key]['subtotal_akhir'] = preg_replace('/\D/', '', $val['subtotal_akhir']);
                }
            }
            $request->merge(['items' => $items]);
        }

        $request->validate([
            'foto_bukti' => 'required|image|max:2048', // 2MB
            'items' => 'required|array',
            'items.*.jumlah_akhir' => 'required|integer|min:1',
            'items.*.subtotal_akhir' => 'required|numeric|min:0', // Staff inputs Total Price per item
            'items.*.expired' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // Upload Foto
            $fotoPath = null;
            if ($request->hasFile('foto_bukti')) {
                $file = $request->file('foto_bukti');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/img/bukti_belanja'), $filename);
                $fotoPath = 'assets/img/bukti_belanja/' . $filename;
            }

            $totalAkhir = 0;

            foreach ($request->items as $detailId => $data) {
                $detail = BelanjaDetail::findOrFail($detailId);

                // Calculate Unit Price
                $subtotal = $data['subtotal_akhir']; // Input by user
                $unitPrice = ($data['jumlah_akhir'] > 0) ? ($subtotal / $data['jumlah_akhir']) : 0;
                
                $totalAkhir += $subtotal;

                // Update Detail
                $detail->update([
                    'jumlah_akhir' => $data['jumlah_akhir'],
                    'harga_satuan_akhir' => $unitPrice,
                    'subtotal_akhir' => $subtotal,
                    'expired' => $data['expired']
                ]);

                // Update Stock (Table Bahan)
                $bahan = Bahan::find($detail->bahan_id);
                $bahan->stok += $data['jumlah_akhir'];
                $bahan->save();

                // Create History (Table BahanMasuk)
                BahanMasuk::create([
                    'bahan_id' => $detail->bahan_id,
                    'jumlah' => $data['jumlah_akhir'],
                    'harga' => $unitPrice,
                    'harga_satuan' => $unitPrice,
                    'total_harga' => $subtotal,
                    'expired' => $data['expired'],
                    // Link to belanja? Not in schema but implicitly linked by time/context
                ]);
            }

            // Update Header
            $belanja->update([
                'status' => 'selesai',
                'user_id_selesai' => Auth::id(),
                'total_akhir' => $totalAkhir,
                'foto_bukti' => $fotoPath
            ]);

            DB::commit();
            return redirect()->route('belanja.index')->with('success', 'Belanja berhasil diselesaikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan belanja: ' . $e->getMessage());
        }
    }

    // Keep Riwayat
    public function riwayat()
    {
         $riwayat = BahanMasuk::with('bahan')->orderBy('created_at', 'desc')->get();
         return view('pages.belanja.riwayat', compact('riwayat'));
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'owner') abort(403);
        $belanja = Belanja::findOrFail($id);
        if ($belanja->status !== 'pending') {
             return back()->with('error', 'Hanya request pending yang bisa dihapus.');
        }
        $belanja->delete();
        return back()->with('success', 'Request dihapus.');
    }
}
