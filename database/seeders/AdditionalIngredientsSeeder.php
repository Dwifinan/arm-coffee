<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bahan;
use Illuminate\Support\Str;

class AdditionalIngredientsSeeder extends Seeder
{
    public function run()
    {
        $newIngredients = [
            [
                'nama' => 'Extra Joss',
                'satuan' => 'pcs', // Stok dalam pcs/sachet
                'harga_persatuan' => 12000, // Price per PACK (12000)
                'satuan_beli' => 'Pack',
                'jumlah_satuan' => 12, // 1 Pack = 12 Pcs
                'stok_minimal' => 24, // Min 2 Pack
                'stok' => 48, // Start with 4 Pack
            ],
            [
                'nama' => 'Gula Aren Cair',
                'satuan' => 'ml',
                'harga_persatuan' => 35000, // Price per BOTOL (35k)
                'satuan_beli' => 'Botol', // Or Liter
                'jumlah_satuan' => 1000,
                'stok_minimal' => 2000, // 2 Botol
                'stok' => 5000, // 5 Botol
            ],
            [
                'nama' => 'Gula Cair (Simple Syrup)',
                'satuan' => 'ml',
                'harga_persatuan' => 20000, // Price per LITER (20k)
                'satuan_beli' => 'Liter',
                'jumlah_satuan' => 1000,
                'stok_minimal' => 3000, // 3 Liter
                'stok' => 10000, // 10 Liter
            ]
        ];

        foreach ($newIngredients as $ing) {
            // Check if exists
            $exists = Bahan::where('nama', $ing['nama'])->first();
            
            if (!$exists) {
                Bahan::create([
                    'kode' => 'BHN-' . strtoupper(Str::random(5)),
                    'nama' => $ing['nama'],
                    'satuan' => $ing['satuan'],
                    'harga_persatuan' => $ing['harga_persatuan'],
                    'satuan_beli' => $ing['satuan_beli'],
                    'jumlah_satuan' => $ing['jumlah_satuan'],
                    'stok_minimal' => $ing['stok_minimal'],
                    'stok' => $ing['stok']
                ]);
                $this->command->info("Added: {$ing['nama']}");
            } else {
                // Update existing to fix price
                $exists->update([
                    'satuan_beli' => $ing['satuan_beli'],
                    'jumlah_satuan' => $ing['jumlah_satuan'],
                    'harga_persatuan' => $ing['harga_persatuan']
                ]);
                $this->command->info("Updated Price & Unit: {$ing['nama']}");
            }
        }
    }
}
