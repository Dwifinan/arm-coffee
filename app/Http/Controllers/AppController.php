<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\BahanMasuk;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppController extends Controller
{


    public function dashboardView(){
        // 1. Stok Kritis
        // Filter: Exclude items that are already in a PENDING Belanja request
        $stokKritis = Bahan::whereNotNull('stok')
                          ->whereColumn('stok', '<=', 'stok_minimal')
                          ->whereDoesntHave('belanjaDetails', function($query) {
                                $query->whereHas('belanja', function($q) {
                                    $q->where('status', 'pending');
                                });
                          })
                          ->get();

        // 2. Expiring Items (Next 7 Days, excluding today)
        // Check History of Incoming Items that are expiring soon AND whose Bahan still has stock
        $expiringItems = BahanMasuk::with('bahan')
                            ->whereDate('expired', '>', Carbon::today())
                            ->whereDate('expired', '<=', Carbon::today()->addDays(7))
                            ->get()
                            ->filter(function($batch) {
                                // Only alert if the Bahan itself still has stock > 0
                                return $batch->bahan && $batch->bahan->stok > 0;
                            })
                            ->unique('bahan_id'); // Unique by Bahan
        
        // 3. Already Expired Items (Past Date + Today, Still in Stock)
        $expiredItems = BahanMasuk::with('bahan')
                            ->where('is_resolved', false) // Filter resolved
                            ->whereDate('expired', '<=', Carbon::today())
                            ->get()
                            ->filter(function($batch) {
                                return $batch->bahan && $batch->bahan->stok > 0;
                            })
                            ->unique('bahan_id');

        // 3. Setup Dates (Last 7 Days)
        $dates = [];
        $dateLabels = [];
        for ($i = 6; $i >= 0; $i--) {
             $d = Carbon::now()->subDays($i);
             $dates[] = $d->format('Y-m-d');
             $dateLabels[] = $d->locale('id')->isoFormat('dddd');
        }

        // 4. Fetch Sales Data grouped by Date & Menu
        // We need all sales in the date range
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        
        $sales = Penjualan::with('menu')
                    ->where('created_at', '>=', $startDate)
                    ->selectRaw('DATE(created_at) as date, menu_id, SUM(jumlah) as total')
                    ->groupBy('date', 'menu_id')
                    ->get();
        
        // 5. Identify all distinct menus sold in this period
        $menus = $sales->map(function($sale) {
             return $sale->menu->nama ?? 'Unknown';
        })->unique()->values();

        // 6. Build Datasets
        $datasets = [];
        $colors = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
            '#858796', '#5a5c69', '#2c9faf', '#c0392b', '#f1c40f'
        ];
        
        foreach ($menus as $index => $menuName) {
            $data = [];
            foreach ($dates as $date) {
                // Find sale for this menu on this date
                $qty = $sales->filter(function($s) use ($date, $menuName) {
                     return $s->date == $date && ($s->menu->nama ?? 'Unknown') == $menuName;
                })->sum('total');
                $data[] = $qty;
            }
            
            // Assign color (cycle through palette)
            $color = $colors[$index % count($colors)];

            $datasets[] = [
                'label' => $menuName,
                'data' => $data,
                'backgroundColor' => $color,
            ];
        }

        // 7. Best Selling Menu (Last 7 Days - Matching Chart)
        // User Request: "menu terlaris untuk minggu ini saja berarti 7 hari ke belakang sama dengan data chart"
        
        $bestSelling = Penjualan::with('menu')
                        ->where('created_at', '>=', $startDate) // Use same start date as chart
                        ->select('menu_id', DB::raw('SUM(jumlah) as total_sold'))
                        ->groupBy('menu_id')
                        ->orderByDesc('total_sold')
                        ->take(5)
                        ->get();

        return view('pages.dashboard', [
            'stokKritis' => $stokKritis,
            'expiredItems' => $expiredItems,
            'expiringItems' => $expiringItems,
            'chartDates' => $dateLabels,
            'chartDatasets' => $datasets,
            'bestSelling' => $bestSelling
        ]);
    }

    public function menuView(){

        return view('pages.menu');
    }
}
