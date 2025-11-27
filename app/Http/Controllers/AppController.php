<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function dashboardView(){
        $stokKritis = Bahan::whereNotNull('stok')
                          ->whereColumn('stok', '<=', 'stok_minimal')
                          ->get();

        return view('pages.dashboard', [
            'stokKritis' => $stokKritis // Kirim data ke view dashboard
        ]);
    }

    public function menuView(){

        return view('pages.menu');
    }
}
