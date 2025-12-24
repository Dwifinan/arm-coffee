<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Bahan;
use App\Models\Penjualan;
use App\Models\BahanMasuk;
use App\Models\Menu;
use Carbon\Carbon;

class SaturdayPresentationSeeder extends Seeder
{
    public function run()
    {
        // 1. CLEAN SLATE (Transactions only)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('penjualan')->truncate();
        DB::table('bahan_masuk')->truncate();
        DB::table('bahan_keluar')->truncate();
        DB::table('belanja_detail')->truncate();
        DB::table('belanja')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info("Tables cleaned.");

        // Explicitly Remove MIE TELUR (User Request)
        Bahan::where('nama', 'Mie Telur')->delete();

        // 2. RESET STOCKS & PRICES (Baseline)
        // Set all stocks to SAFE levels first
        // 2. RESET STOCKS & PRICES TO BASELINE (Safe Level)
        $bahans = Bahan::all();
        foreach ($bahans as $b) {
            // SCENARIO CHECK
            $isTelur = str_contains($b->nama, 'Telur');
            $isSusu = str_contains($b->nama, 'Susu') && str_contains($b->nama, 'Full');

            // Critical Candidates
            $criticalKeywords = ['Arabica', 'Bubuk Cokelat', 'Sirup Vanilla', 'Oreo', 'Gula Aren'];
            $isCritical = false;
            foreach ($criticalKeywords as $keyword) {
                if (str_contains($b->nama, $keyword)) {
                    $isCritical = true;
                    break;
                }
            }

            $targetStock = 0;
            $batchData = [];

            if ($isCritical) {
                // Scenario Critical: Set stock to 20% of min stock (or hardcoded low value)
                // Ensure Min Stock is reasonable first
                $currentMin = $b->stok_minimal > 0 ? $b->stok_minimal : 1000;
                $b->update(['stok_minimal' => $currentMin]); // Ensure encoded

                $targetStock = floor($currentMin * 0.2); // 20% of minimum
                if ($targetStock <= 0) $targetStock = 1;
                
                // Create 1 Batch
                $batchData[] = [
                    'jumlah' => $targetStock,
                    'expired' => Carbon::now()->addMonths(6), // Safe expiry
                ];
                $this->command->info("Scenario Critical: {$b->nama} set to {$targetStock} (Min: {$currentMin}).");

            } elseif ($isTelur) {
                // Scenario Expired
                // 30 expired, 20 safe. Total 50.
                $targetStock = 50; 
                
                // Batch 1: Expired (30) - 3 Days Ago
                $batchData[] = [
                    'jumlah' => 30,
                    'expired' => Carbon::now()->subDays(3), // Expired 3 days ago
                    'created_at' => Carbon::now()->subDays(10)
                ];
                
                // Batch 2: Safe (20)
                $batchData[] = [
                    'jumlah' => 20,
                    'expired' => Carbon::now()->addWeeks(2)
                ];
                $this->command->info("Scenario Expired: Telur configured.");

            } elseif ($isSusu) {
                // Scenario Near Expired
                // 5000ml near expired, 5000ml safe. Total 10000.
                $targetStock = 10000;

                // Batch 1: Near Expired (5000) - Expiring in 3 Days
                $batchData[] = [
                    'jumlah' => 5000,
                    'expired' => Carbon::now()->addDays(3), // Near Expired (in 3 days)
                    'created_at' => Carbon::now()->subDays(2)
                ];

                // Batch 2: Safe (5000)
                $batchData[] = [
                    'jumlah' => 5000,
                    'expired' => Carbon::now()->addMonths(1)
                ];
                $this->command->info("Scenario Near Expired: Susu configured.");

            } else {
                // Normal Item -> Safe Stock
                $min = $b->stok_minimal > 0 ? $b->stok_minimal : 1000;
                $targetStock = $min * 5;

                // Batch 1: Safe
                $batchData[] = [
                    'jumlah' => $targetStock,
                    'expired' => Carbon::now()->addMonths(6)
                ];
            }

            // EXECUTE UPDATE
            $b->update(['stok' => $targetStock]);

            // EXECUTE BATCH CREATION
            foreach ($batchData as $bd) {
                // Determine price (approx)
                $hargaSatuan = $b->harga_satuan > 0 ? $b->harga_satuan : 100;
                $totalHarga = $hargaSatuan * $bd['jumlah'];

                BahanMasuk::create([
                    'bahan_id' => $b->id,
                    'jumlah' => $bd['jumlah'],
                    'sisa_stok' => $bd['jumlah'], // FIFO
                    'harga' => $totalHarga,
                    'harga_satuan' => $hargaSatuan,
                    'total_harga' => $totalHarga,
                    'expired' => $bd['expired'],
                    'created_at' => $bd['created_at'] ?? Carbon::now()->subDays(rand(1, 10))
                ]);
            }
        }


        // 4. SALES DATA (Chart Fill)
        // Generate from 10 days ago until Today
        $menus = Menu::all();
        $startDate = Carbon::now()->subDays(10);
        
        for ($i = 0; $i <= 10; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            // Random transactions per day (10-20)
            $transCount = rand(10, 20);
            
            for ($k = 0; $k < $transCount; $k++) {
                $menu = $menus->random();
                $qty = rand(1, 3);
                
                Penjualan::create([
                    'menu_id' => $menu->id,
                    'jumlah' => $qty,
                    'total_harga' => $menu->harga * $qty,
                    'created_at' => $currentDate->copy()->addHours(rand(9, 21)) // Business hours
                ]);
            }
        }
        $this->command->info("Sales data seeded up to Dec 21.");
    }
}
