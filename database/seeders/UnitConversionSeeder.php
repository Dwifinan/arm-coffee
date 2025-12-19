<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bahan;

class UnitConversionSeeder extends Seeder
{
    public function run()
    {
        $mappings = [
            // === GENERAL (KG -> 1000g) ===
            ['names' => ['Biji Kopi Arabica', 'Bubuk Kopi', 'Gula Pasir', 'Gula Aren', 'Daging Ayam', 'Wortel', 'Tepung Tapioka', 'Mie Telur', 'Sawi', 'Kol', 'Daun Bawang', 'Bawang Putih', 'Bawang Merah', 'Cabai'], 'satuan_beli' => 'Kg', 'jumlah_satuan' => 1000],
            
            // === LIQUIDS (LITER -> 1000ml / GALON -> 19000ml) ===
            ['names' => ['Air Mineral'], 'satuan_beli' => 'Galon', 'jumlah_satuan' => 19000],
            ['names' => ['Susu Cair', 'Minyak Goreng'], 'satuan_beli' => 'Liter', 'jumlah_satuan' => 1000],
            ['names' => ['Sirup'], 'satuan_beli' => 'Botol', 'jumlah_satuan' => 750], // Estimasi sirup
            
            // === PACKS/CANS ===
            ['names' => ['Susu Kental Manis'], 'satuan_beli' => 'Kaleng', 'jumlah_satuan' => 370], // Gram/Ml varies but std 370g
            ['names' => ['Es Batu'], 'satuan_beli' => 'Pack', 'jumlah_satuan' => 10000], // 10kg pack
            ['names' => ['Bubuk Cokelat', 'Bubuk Matcha', 'Bubuk Milo', 'Red Velvet', 'Krimer'], 'satuan_beli' => 'Pack', 'jumlah_satuan' => 1000], // 1kg pack
            
            // === UNITS ===
            ['names' => ['Telur Ayam'], 'satuan_beli' => 'Tray', 'jumlah_satuan' => 30],
            ['names' => ['Saus Sambal', 'Saus Tiram', 'Saus Tomat'], 'satuan_beli' => 'Pack', 'jumlah_satuan' => 1000], // 1L refill pack
        ];

        foreach ($mappings as $map) {
            foreach ($map['names'] as $namePart) {
                // Find all bahan matching the name part
                $items = Bahan::where('nama', 'LIKE', "%{$namePart}%")->get();
                foreach ($items as $item) {
                    $item->update([
                        'satuan_beli' => $map['satuan_beli'],
                        'jumlah_satuan' => $map['jumlah_satuan']
                    ]);
                    $this->command->info("Updated {$item->nama}: Beli per {$map['satuan_beli']} (Isi {$map['jumlah_satuan']} {$item->satuan})");
                }
            }
        }
    }
}
