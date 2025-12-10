<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppController extends Controller
{
    public function dashboardView(){
        $stokKritis = Bahan::whereNotNull('stok')
                          ->whereColumn('stok', '<=', 'stok_minimal')
                          ->get();

        // Ambil data 7 hari terakhir
        $dates = [];
        $salesData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $formattedDate = $date->format('Y-m-d');
            $dates[] = $date->locale('id')->isoFormat('dddd'); // Nama hari saja, bisa diganti format lain

            // Hitung total jumlah produksi pada tanggal tersebut
            $totalProduksi = Penjualan::whereDate('created_at', $formattedDate)->sum('jumlah');
            $salesData[] = $totalProduksi;
        }

        return view('pages.dashboard', [
            'stokKritis' => $stokKritis,
            'chartDates' => $dates,
            'chartData' => $salesData
        ]);
    }

    public function menuView(){

        return view('pages.menu');
    }
}
