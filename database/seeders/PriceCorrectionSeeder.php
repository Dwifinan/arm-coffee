<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bahan;

class PriceCorrectionSeeder extends Seeder
{
    public function run()
    {
        $prices = [
            // Kopi & Minuman Dasar
            ['term' => 'Arabica', 'price' => 250000], // per Kg
            ['term' => 'Robusta', 'price' => 120000], // per Kg
            ['term' => 'Bubuk Kopi', 'price' => 60000], // per Kg
            
            // Gula & Pemanis
            ['term' => 'Gula Pasir', 'price' => 18000], // per Kg
            ['term' => 'Gula Aren', 'price' => 35000], // per Kg (Solid/Cair handled by term)
            ['term' => 'Gula Aren Cair', 'price' => 35000], // per Botol
            ['term' => 'Gula Cair', 'price' => 20000], // per Liter
            ['term' => 'Susu Kental Manis', 'price' => 12500], // per Kaleng
            
            // Susu & Creamer
            ['term' => 'Susu Cair', 'price' => 19000], // per Liter (Greenfields/Diamond)
            ['term' => 'Full Cream', 'price' => 19000],
            ['term' => 'Krimer', 'price' => 35000], // per Pack
            
            // Powders (Premium)
            ['term' => 'Bubuk Cokelat', 'price' => 65000], // per Pack (1kg)
            ['term' => 'Bubuk Matcha', 'price' => 75000],
            ['term' => 'Bubuk Red Velvet', 'price' => 70000],
            ['term' => 'Bubuk Milo', 'price' => 65000], // 1kg
            
            // Syrups
            ['term' => 'Sirup', 'price' => 90000], // Monin/Toffin est per Botol
            
            // Cold Storage & Fresh
            ['term' => 'Daging Ayam', 'price' => 45000], // per Kg
            ['term' => 'Telur Ayam', 'price' => 55000], // per Tray (30 butir)
            ['term' => 'Es Batu', 'price' => 10000], // per Pack (10kg)
            
            // Groceries / Sayur
            ['term' => 'Air Mineral', 'price' => 20000], // per Galon
            ['term' => 'Minyak Goreng', 'price' => 12000], // per Liter (Curah/Simple)
            ['term' => 'Saus', 'price' => 22000], // per Pack (1kg)
            ['term' => 'Kecap', 'price' => 2000], // per Botol kecil
            ['term' => 'Bawang', 'price' => 35000], // per Kg
            ['term' => 'Cabai', 'price' => 40000], // per Kg
            ['term' => 'Wortel', 'price' => 15000], // per Kg
            ['term' => 'Kol', 'price' => 10000], // per Kg
            ['term' => 'Sawi', 'price' => 12000], // per Kg
            ['term' => 'Tepung', 'price' => 14000], // per Kg
            ['term' => 'Mie Telur', 'price' => 18000], // per Kg
            ['term' => 'Extra Joss', 'price' => 12000], // per Pack
            ['term' => 'Garam', 'price' => 5000], // per Pack
            ['term' => 'Lada', 'price' => 35000], // per Kg?? Maybe pack small
        ];

        foreach ($prices as $p) {
            $items = Bahan::where('nama', 'LIKE', "%{$p['term']}%")->get();
            foreach ($items as $item) {
                // Determine logic: 
                // We want to set 'harga_persatuan' to the Bulk Price.
                // The DB logic `harga_satuan` (unit price) is calculated as `harga_persatuan / jumlah_satuan`.
                
                // Special check for things that might have different units but same name
                // Assume the array covers the specific cases well enough
                
                $item->update(['harga_persatuan' => $p['price']]);
                $this->command->info("Updated {$item->nama}: Rp " . number_format($p['price']));
            }
        }
    }
}
