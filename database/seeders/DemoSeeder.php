<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Bahan;
use App\Models\BahanMasuk;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    public function run()
    {
        // 1. SCENARIO: CRITICAL STOCK
        // Set 'Gula Pasir' to be below limit
        $criticalItem = Bahan::where('nama', 'LIKE', '%Gula Pasir%')->first();
        if ($criticalItem) {
            $criticalItem->update(['stok' => 500, 'stok_minimal' => 1000]);
        }

        // 2. SCENARIO: EXPIRING SOON (Next 2 Days)
        // Add a batch for 'Susu Cair Full Cream' that expires in 2 days
        $expiringItem = Bahan::where('nama', 'LIKE', '%Susu Cair Full Cream%')->first();
        
        // Fallback if not found
        if (!$expiringItem) $expiringItem = Bahan::where('nama', 'LIKE', '%Susu%')->first();

        if ($expiringItem) {
            // Ensure stock exists so logic picks it up
            if ($expiringItem->stok == 0) $expiringItem->update(['stok' => 5000]);

            BahanMasuk::create([
                'bahan_id' => $expiringItem->id,
                'jumlah' => 10, // Small batch
                'harga' => 150000,
                'harga_satuan' => 15000,
                'total_harga' => 150000,
                'expired' => Carbon::today()->addDays(2), // +2 Days from TODAY (consistent)
                'created_at' => Carbon::now()->subDays(10), 
            ]);
        }

        // 3. SCENARIO: ALREADY EXPIRED (Action Required)
        // Add a batch for 'Telur Ayam' that expired yesterday
        $expiredItem = Bahan::where('nama', 'LIKE', '%Telur Ayam%')->first();
        // Fallback
        if (!$expiredItem) $expiredItem = Bahan::skip(1)->first(); // Pick 2nd item if 1st used above

        if ($expiredItem) {
            // Ensure stock > 0 so alert appears
            if ($expiredItem->stok == 0) $expiredItem->update(['stok' => 10]);

            BahanMasuk::create([
                'bahan_id' => $expiredItem->id,
                'jumlah' => 5,
                'harga' => 10000,
                'harga_satuan' => 2000,
                'total_harga' => 10000,
                'expired' => Carbon::yesterday(), // Explicit yesterday
                'created_at' => Carbon::now()->subMonth(1), 
                'is_resolved' => false
            ]);
        }

        $this->command->info('Demo Scenarios Injected: Critical (Gula), Expiring (Susu Cair), Expired (Telur/Other).');
    }
}
