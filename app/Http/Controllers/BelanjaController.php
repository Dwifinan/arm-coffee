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
                
                // CONVERSION LOGIC
                // Input is usually "Buy Unit" (e.g., 2 Galon)
                // We keep 'jumlah_estimasi' as the Buy Unit Qty in the database for display
                // The price per unit should be 'Buy Price per Unit' (which is harga_satuan * jumlah_satuan) -> No, price in DB is usually per smallest unit.
                // Wait, if user inputs '2 Galon', total price should be 2 * Harga Galon.
                // Current DB `harga_satuan` is Price Per Smallest Unit (e.g. per ml).
                // So Price Per Buy Unit = harga_satuan * jumlah_satuan.
                
                $conversionFactor = $bahan->jumlah_satuan ?? 1;
                $buyUnitPrice = ($bahan->harga_satuan ?? 0) * $conversionFactor;
                
                $subtotal = $buyUnitPrice * $item['jumlah'];

                BelanjaDetail::create([
                    'belanja_id' => $belanja->id,
                    'bahan_id' => $item['bahan_id'],
                    'jumlah_estimasi' => $item['jumlah'], // Stores BUY UNIT QTY
                    'harga_satuan_estimasi' => $buyUnitPrice, // Stores PRICE PER BUY UNIT
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
            'items.*.subtotal_akhir' => 'required|numeric|min:0', // Staff inputs Total Price per item
            'items.*.expired' => 'nullable|date',
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
                $bahan = Bahan::find($detail->bahan_id);

                // Calculate Unit Price
                $subtotal = $data['subtotal_akhir']; // Input by user (Total Price Payed)
                $qtyBuyUnit = $data['jumlah_akhir'];
                
                // Price per Buy Unit
                $unitPrice = ($qtyBuyUnit > 0) ? ($subtotal / $qtyBuyUnit) : 0;
                
                $totalAkhir += $subtotal;

                // Update Detail
                $detail->update([
                    'jumlah_akhir' => $qtyBuyUnit,
                    'harga_satuan_akhir' => $unitPrice,
                    'subtotal_akhir' => $subtotal,
                    'expired' => $data['expired']
                ]);

                // CONVERSION FOR STOCK ADDITION
                $conversionFactor = $bahan->jumlah_satuan ?? 1;
                $pemasukanStok = $qtyBuyUnit * $conversionFactor;

                // Update Stock (Table Bahan)
                $bahan->stok += $pemasukanStok;
                
                // Update Price Per Smallest Unit for future reference (Weighted Average?)
                // For now, let's just update the reference price if needed, or keep weighted avg logic separately.
                // Simple update:
                if ($pemasukanStok > 0) {
                     $bahan->harga_persatuan = $subtotal / $pemasukanStok; // Update base price
                }
                
                $bahan->save();

                // Create History (Table BahanMasuk)
                BahanMasuk::create([
                    'bahan_id' => $detail->bahan_id,
                    'jumlah' => $pemasukanStok, // STORED AS SMALLEST UNIT
                    'sisa_stok' => $pemasukanStok, // FIFO Initialization
                    'harga' => $subtotal, // Total Batch Price
                    'harga_satuan' => ($pemasukanStok > 0) ? ($subtotal / $pemasukanStok) : 0, // Price Per Smallest Unit
                    'total_harga' => $subtotal,
                    'expired' => $data['expired'],
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

    // Auto Request Critical (Owner Dashboard)
    public function autoRequestCritical()
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $stokKritis = Bahan::whereNotNull('stok')
                          ->whereColumn('stok', '<=', 'stok_minimal')
                          ->whereDoesntHave('belanjaDetails', function($query) {
                                $query->whereHas('belanja', function($q) {
                                    $q->where('status', 'pending');
                                });
                          })
                          ->get();

        if ($stokKritis->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada stok kritis saat ini.']);
        }

        try {
            DB::beginTransaction();

            $belanja = Belanja::create([
                'kode' => 'REQ-AUTO-' . date('ymd') . '-' . strtoupper(Str::random(4)),
                'user_id_request' => Auth::id(),
                'status' => 'pending',
                'total_estimasi' => 0
            ]);

            $totalEstimasi = 0;

            foreach ($stokKritis as $bahan) {
                // Logic: Target = Stok Minimal * 1.5 (Buffer 50%)
                // Request = Target - Current (All in Small Units)
                $target = ceil($bahan->stok_minimal * 1.5);
                $qtySmall = $target - $bahan->stok;

                if ($qtySmall <= 0) $qtySmall = 1;

                // CONVERT TO BUY UNIT
                $conversionFactor = $bahan->jumlah_satuan ?? 1;
                $qtyBuy = ceil($qtySmall / $conversionFactor); // Round up to nearest buy unit

                // Price Estimation (Price Per Buy Unit)
                $pricePerSmall = $bahan->harga_satuan ?? 0;
                $pricePerBuy = $pricePerSmall * $conversionFactor;
                
                $subtotal = $pricePerBuy * $qtyBuy;

                BelanjaDetail::create([
                    'belanja_id' => $belanja->id,
                    'bahan_id' => $bahan->id,
                    'jumlah_estimasi' => $qtyBuy, // STORED AS BUY UNIT
                    'harga_satuan_estimasi' => $pricePerBuy,
                    'subtotal_estimasi' => $subtotal,
                ]);

                $totalEstimasi += $subtotal;
            }

            $belanja->update(['total_estimasi' => $totalEstimasi]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Request belanja otomatis berhasil dibuat.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal membuat request: ' . $e->getMessage()]);
        }
    }
}
