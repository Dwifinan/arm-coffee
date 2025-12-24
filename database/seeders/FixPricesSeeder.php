<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bahan;

class FixPricesSeeder extends Seeder
{
    public function run()
    {
        // 1. GARAM (Salt)
        // Fix: Set unit to 250g/500g Pack, Price to ~5000
        $garam = Bahan::where('nama', 'LIKE', '%Garam%')->first();
        if ($garam) {
            $garam->update([
                'satuan_beli' => 'Pack',
                'jumlah_satuan' => 250, // Standard small pack
                'harga_persatuan' => 5000 // 5k per pack
            ]);
        }

        // 2. KECAP MANIS (Soy Sauce)
        // Fix: Set unit to 600ml Pouch, Price to ~20000
        $kecap = Bahan::where('nama', 'LIKE', '%Kecap Manis%')->first();
        if ($kecap) {
            $kecap->update([
                'satuan_beli' => 'Pouch',
                'jumlah_satuan' => 550, // Standard refill
                'harga_persatuan' => 18000
            ]);
        }

        // 3. GULA AREN (Palm Sugar) - Currently 35/1000 (Too cheap)
        // Fix: Set price to 35000
        $gulaAren = Bahan::where('nama', 'Gula Aren')->first();
        if ($gulaAren) {
            $gulaAren->update([
                'satuan_beli' => 'Kg',
                'jumlah_satuan' => 1000,
                'harga_persatuan' => 35000
            ]);
        }

        // 4. GULA PASIR (Sugar) - Check validity
         $gulaPasir = Bahan::where('nama', 'Gula Pasir')->first();
         if ($gulaPasir) {
             $gulaPasir->update([
                 'satuan_beli' => 'Kg',
                 'jumlah_satuan' => 1000,
                 'harga_persatuan' => 18000
             ]);
         }
         
        // 5. AIR MINERAL - Check validity (Galon)
        $air = Bahan::where('nama', 'Air Mineral')->first();
        if ($air) {
            $air->update([
                'satuan_beli' => 'Galon',
                'jumlah_satuan' => 19000,
                'harga_persatuan' => 20000
            ]);
        }
        $kopiArabica = Bahan::where('nama', 'Biji Kopi Arabica')->first();
        if ($kopiArabica) {
            $kopiArabica->update([
                'satuan_beli' => 'Kg',
                'jumlah_satuan' => 1000,
                'harga_persatuan' => 250000
            ]);
        }

        $this->command->info("Prices fixed for Garam, Kecap, Gula, Air.");
    }
}
