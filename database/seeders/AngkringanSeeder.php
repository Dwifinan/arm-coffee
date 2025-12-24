<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Bahan;
use App\Models\Menu;
use Illuminate\Support\Facades\Schema;

class AngkringanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        // 1. Truncate Tables
        DB::table('komposisi')->truncate();
        DB::table('penjualan')->truncate();
        DB::table('belanja_detail')->truncate();
        DB::table('belanja')->truncate();
        DB::table('bahan_masuk')->truncate();
        DB::table('menu')->truncate();
        DB::table('bahan')->truncate();

        Schema::enableForeignKeyConstraints();

        // 2. Insert Bahan
        $bahans = [
            // Kopi & Dasar
            ['nama' => 'Biji Kopi Arabica', 'satuan' => 'gram', 'harga_satuan' => 300, 'stok' => 5000, 'stok_minimal' => 1000],
            ['nama' => 'Bubuk Kopi', 'satuan' => 'gram', 'harga_satuan' => 100, 'stok' => 5000, 'stok_minimal' => 1000],
            ['nama' => 'Gula Pasir', 'satuan' => 'gram', 'harga_satuan' => 15, 'stok' => 10000, 'stok_minimal' => 2000],
            ['nama' => 'Gula Aren', 'satuan' => 'gram', 'harga_satuan' => 30, 'stok' => 5000, 'stok_minimal' => 1000],
            ['nama' => 'Susu Kental Manis', 'satuan' => 'ml', 'harga_satuan' => 40, 'stok' => 10000, 'stok_minimal' => 2000],
            ['nama' => 'Susu Cair Full Cream', 'satuan' => 'ml', 'harga_satuan' => 20, 'stok' => 20000, 'stok_minimal' => 5000],
            ['nama' => 'Krimer Bubuk', 'satuan' => 'gram', 'harga_satuan' => 50, 'stok' => 3000, 'stok_minimal' => 500],
            // Cairan & Es
            ['nama' => 'Air Mineral', 'satuan' => 'ml', 'harga_satuan' => 5, 'stok' => 50000, 'stok_minimal' => 10000], 
            ['nama' => 'Es Batu', 'satuan' => 'gram', 'harga_satuan' => 2000, 'stok' => 50, 'stok_minimal' => 10],
            // Rasa Bubuk
            ['nama' => 'Bubuk Cokelat', 'satuan' => 'gram', 'harga_satuan' => 80, 'stok' => 2000, 'stok_minimal' => 500],
            ['nama' => 'Bubuk Matcha', 'satuan' => 'gram', 'harga_satuan' => 150, 'stok' => 2000, 'stok_minimal' => 500],
            ['nama' => 'Bubuk Milo', 'satuan' => 'gram', 'harga_satuan' => 100, 'stok' => 2000, 'stok_minimal' => 500],
            ['nama' => 'Oreo', 'satuan' => 'pcs', 'harga_satuan' => 1000, 'stok' => 200, 'stok_minimal' => 50],
            ['nama' => 'Bubuk Red Velvet', 'satuan' => 'gram', 'harga_satuan' => 120, 'stok' => 2000, 'stok_minimal' => 500],
            // Sirup & Buah
            ['nama' => 'Sirup Vanilla', 'satuan' => 'ml', 'harga_satuan' => 100, 'stok' => 3000, 'stok_minimal' => 500],
            ['nama' => 'Sirup Caramel', 'satuan' => 'ml', 'harga_satuan' => 100, 'stok' => 3000, 'stok_minimal' => 500],
            ['nama' => 'Sirup Mangga', 'satuan' => 'ml', 'harga_satuan' => 80, 'stok' => 3000, 'stok_minimal' => 500],
            ['nama' => 'Mangga Segar', 'satuan' => 'gram', 'harga_satuan' => 30, 'stok' => 5000, 'stok_minimal' => 1000],
            ['nama' => 'Jahe Segar', 'satuan' => 'gram', 'harga_satuan' => 25, 'stok' => 3000, 'stok_minimal' => 500],
            // Makanan - Dimsum & Sate
            ['nama' => 'Kulit Dimsum', 'satuan' => 'lembar', 'harga_satuan' => 200, 'stok' => 500, 'stok_minimal' => 100],
            ['nama' => 'Daging Ayam Giling', 'satuan' => 'gram', 'harga_satuan' => 60, 'stok' => 5000, 'stok_minimal' => 1000],
            ['nama' => 'Wortel', 'satuan' => 'gram', 'harga_satuan' => 15, 'stok' => 2000, 'stok_minimal' => 500],
            ['nama' => 'Tepung Tapioka', 'satuan' => 'gram', 'harga_satuan' => 20, 'stok' => 3000, 'stok_minimal' => 1000],
            ['nama' => 'Otak-otak Ikan', 'satuan' => 'pcs', 'harga_satuan' => 1500, 'stok' => 200, 'stok_minimal' => 50],
            ['nama' => 'Sosis', 'satuan' => 'pcs', 'harga_satuan' => 2000, 'stok' => 200, 'stok_minimal' => 50],
            ['nama' => 'Bakso', 'satuan' => 'pcs', 'harga_satuan' => 1000, 'stok' => 300, 'stok_minimal' => 50],
            ['nama' => 'Nugget', 'satuan' => 'pcs', 'harga_satuan' => 1500, 'stok' => 200, 'stok_minimal' => 50],
            ['nama' => 'Mie Instan', 'satuan' => 'pcs', 'harga_satuan' => 3500, 'stok' => 100, 'stok_minimal' => 24], // 1 kardus
            ['nama' => 'Telur Ayam', 'satuan' => 'butir', 'harga_satuan' => 2000, 'stok' => 300, 'stok_minimal' => 50], // 1-2 tray
            // Sayur & Bumbu
            ['nama' => 'Sawi', 'satuan' => 'gram', 'harga_satuan' => 10, 'stok' => 2000, 'stok_minimal' => 500],
            ['nama' => 'Kol', 'satuan' => 'gram', 'harga_satuan' => 10, 'stok' => 2000, 'stok_minimal' => 500],
            ['nama' => 'Daun Bawang', 'satuan' => 'gram', 'harga_satuan' => 20, 'stok' => 1000, 'stok_minimal' => 200],
            ['nama' => 'Bawang Putih', 'satuan' => 'gram', 'harga_satuan' => 40, 'stok' => 2000, 'stok_minimal' => 500],
            ['nama' => 'Bawang Merah', 'satuan' => 'gram', 'harga_satuan' => 50, 'stok' => 2000, 'stok_minimal' => 500],
            ['nama' => 'Cabai Kering', 'satuan' => 'gram', 'harga_satuan' => 80, 'stok' => 1000, 'stok_minimal' => 200],
            ['nama' => 'Cabai Fresh', 'satuan' => 'gram', 'harga_satuan' => 60, 'stok' => 2000, 'stok_minimal' => 500],
            ['nama' => 'Biji Wijen', 'satuan' => 'gram', 'harga_satuan' => 100, 'stok' => 500, 'stok_minimal' => 100],
            ['nama' => 'Minyak Goreng', 'satuan' => 'ml', 'harga_satuan' => 20, 'stok' => 5000, 'stok_minimal' => 2000], // 2L
            ['nama' => 'Garam', 'satuan' => 'gram', 'harga_satuan' => 10, 'stok' => 1000, 'stok_minimal' => 250],
            ['nama' => 'Merica', 'satuan' => 'gram', 'harga_satuan' => 150, 'stok' => 500, 'stok_minimal' => 100],
            ['nama' => 'Penyedap Rasa', 'satuan' => 'gram', 'harga_satuan' => 20, 'stok' => 1000, 'stok_minimal' => 250],
            ['nama' => 'Kecap Manis', 'satuan' => 'ml', 'harga_satuan' => 30, 'stok' => 2000, 'stok_minimal' => 600],
            ['nama' => 'Saus Tiram', 'satuan' => 'ml', 'harga_satuan' => 40, 'stok' => 1000, 'stok_minimal' => 300],
            ['nama' => 'Saus Sambal', 'satuan' => 'ml', 'harga_satuan' => 25, 'stok' => 3000, 'stok_minimal' => 1000],
            ['nama' => 'Saus Tomat', 'satuan' => 'ml', 'harga_satuan' => 25, 'stok' => 3000, 'stok_minimal' => 1000],
        ];

        foreach ($bahans as $b) {
            // Create Bahan code automatically
            $code = 'BHN-' . strtoupper(substr(str_replace(' ', '', $b['nama']), 0, 3)) . rand(10, 99);
            Bahan::create([
                'kode' => $code,
                'nama' => $b['nama'],
                'satuan' => $b['satuan'],
                // harga_satuan is generated, so we insert the source columns
                'harga_persatuan' => $b['harga_satuan'], // Assuming price per 1 unit is the same
                'jumlah_satuan' => 1,
                'stok' => $b['stok'],
                'stok_minimal' => $b['stok_minimal'], // Use specific min stock
            ]);
        }

        // 3. Insert Menu
        $menus = [
            'V60' => 18000,
            'V60 Arabica' => 20000,
            'Latte' => 18000,
            'Vietnam Drip' => 15000,
            'Coffee Arm' => 22000,
            'Sweet Latte' => 18000,
            'Kopi Tubruk' => 10000,
            'Sweet Manggo' => 16000,
            'Red Velvet' => 18000,
            'Choco Matcha' => 18000,
            'Matcha Latte' => 18000,
            'Full Cream Milo' => 15000,
            'Es Susu' => 10000,
            'Josu (Jahe Susu)' => 8000,
            'Dimsum' => 15000,
            'Otak-otak Chilli Oil' => 12000,
            'Indomie' => 8000,
            'Sate-satean' => 15000,
            'Mie Nyemek' => 15000
        ];

        foreach ($menus as $nama => $harga) {
            Menu::create([
                'nama' => $nama,
                'harga' => $harga,
                'tersedia' => 1
            ]);
        }

        // 4. Insert Komposisi (Mapping Name to Name logic)
        // Helper to get ID by rough name matching or specific mapping
        $allBahan = Bahan::all();
        $allMenu = Menu::all();

        // Prepare Mapping: Menu Name => [ [Bahan Name Part, Qty, Unit], ... ]
        $recipes = [
            'V60' => [['Biji Kopi', 15, 'gram'], ['Air Mineral', 200, 'ml']],
            'V60 Arabica' => [['Biji Kopi Arabica', 15, 'gram'], ['Air Mineral', 200, 'ml']],
            'Latte' => [['Biji Kopi', 18, 'gram'], ['Susu Cair', 150, 'ml']],
            'Vietnam Drip' => [['Bubuk Kopi', 15, 'gram'], ['Susu Kental', 30, 'ml'], ['Air Mineral', 150, 'ml']],
            'Coffee Arm' => [['Biji Kopi', 18, 'gram'], ['Susu Cair', 100, 'ml'], ['Gula Aren', 15, 'gram']],
            'Sweet Latte' => [['Biji Kopi', 18, 'gram'], ['Susu Cair', 150, 'ml'], ['Sirup Vanilla', 10, 'ml']],
            'Kopi Tubruk' => [['Bubuk Kopi', 15, 'gram'], ['Air Mineral', 200, 'ml'], ['Gula Pasir', 10, 'gram']],
            'Sweet Manggo' => [['Mangga Segar', 50, 'gram'], ['Sirup Mangga', 20, 'ml'], ['Susu Cair', 100, 'ml'], ['Es Batu', 0.1, 'kg']],
            'Red Velvet' => [['Bubuk Red Velvet', 20, 'gram'], ['Susu Cair', 150, 'ml'], ['Susu Kental', 20, 'ml']],
            'Choco Matcha' => [['Bubuk Cokelat', 15, 'gram'], ['Bubuk Matcha', 15, 'gram'], ['Susu Cair', 150, 'ml']],
            'Matcha Latte' => [['Bubuk Matcha', 20, 'gram'], ['Susu Cair', 150, 'ml'], ['Gula Pasir', 10, 'gram']],
            'Full Cream Milo' => [['Bubuk Milo', 25, 'gram'], ['Susu Cair', 150, 'ml'], ['Susu Kental', 20, 'ml']],
            'Es Susu' => [['Susu Cair', 200, 'ml'], ['Gula Pasir', 15, 'gram'], ['Es Batu', 0.1, 'kg']],
            'Josu' => [['Jahe', 20, 'gram'], ['Susu Kental', 40, 'ml'], ['Air Mineral', 200, 'ml']],
            'Dimsum' => [['Daging Ayam', 50, 'gram'], ['Kulit Dimsum', 1, 'lembar'], ['Tepung Tapioka', 10, 'gram']],
            'Otak-otak' => [['Otak-otak', 1, 'pcs'], ['Minyak Goreng', 10, 'ml'], ['Saus Sambal', 10, 'ml']],
            'Indomie' => [['Mie Instan', 1, 'pcs'], ['Telur', 1, 'butir'], ['Sawi', 20, 'gram']],
            'Sate' => [['Sosis', 1, 'pcs'], ['Bakso', 1, 'pcs'], ['Minyak Goreng', 20, 'ml']],
            'Mie Nyemek' => [['Mie Instan', 1, 'pcs'], ['Telur', 1, 'butir'], ['Kecap Manis', 10, 'ml'], ['Sawi', 30, 'gram']]
        ];

        foreach ($allMenu as $menu) {
            // Find constraints for this menu
            // Use LIKE matching for key
            $foundKey = null;
            foreach (array_keys($recipes) as $key) {
                if (stripos($menu->nama, $key) !== false) {
                    $foundKey = $key;
                    break;
                }
            }

            if ($foundKey) {
                $ingredients = $recipes[$foundKey];
                foreach ($ingredients as $ing) {
                    $bahanNamePart = $ing[0];
                    $qty = $ing[1];
                    $unit = $ing[2];

                    // Find Bahan by name part
                    $bahan = $allBahan->filter(function($item) use ($bahanNamePart) {
                        return stripos($item->nama, $bahanNamePart) !== false;
                    })->first();

                    if ($bahan) {
                        DB::table('komposisi')->insert([
                            'menu_id' => $menu->id,
                            'bahan_id' => $bahan->id,
                            'jumlah_bahan' => $qty,
                            'satuan' => $unit,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
