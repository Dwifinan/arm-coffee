<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\Penjualan;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil semua Menu
        $menus = Menu::all();
        
        if ($menus->isEmpty()) {
            $this->command->info('Tidak ada menu. Jalankan DatabaseSeeder dulu.');
            return;
        }

        // 2. Loop 7 hari terakhir
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Random jumlah batch per hari (misal 5 - 15 transaksi)
            $dailyTransactions = rand(5, 15);

            for ($j = 0; $j < $dailyTransactions; $j++) {
                // Generate Batch ID
                $batchId = 'BATCH-' . Str::upper(Str::random(8));
                
                // Random jam dalam hari tersebut (08:00 - 20:00)
                $transactionTime = $date->copy()->setTime(rand(8, 20), rand(0, 59));

                // Random jumlah item dalam 1 batch (1 - 3 menu berbeda)
                $itemsCount = rand(1, 3);
                
                // Ambil menu acak
                $randomMenus = $menus->random($itemsCount);

                foreach ($randomMenus as $menu) {
                    $qty = rand(1, 5);
                    $totalHarga = $menu->harga * $qty;
                    $kodeTransaksi = Str::upper(Str::random(10));

                    Penjualan::create([
                        'kode_transaksi' => $kodeTransaksi,
                        'batch_id'       => $batchId,
                        'menu_id'        => $menu->id,
                        'jumlah'         => $qty,
                        'total_harga'    => $totalHarga,
                        'created_at'     => $transactionTime,
                        'updated_at'     => $transactionTime,
                    ]);
                }
            }
        }
    }
}
