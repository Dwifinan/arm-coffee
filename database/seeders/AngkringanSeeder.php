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
            // Kopi
            ['nama' => 'Biji Kopi Arabica', 'satuan' => 'gram', 'harga_satuan' => 300, 'stok' => 1000],
            ['nama' => 'Bubuk Kopi', 'satuan' => 'gram', 'harga_satuan' => 100, 'stok' => 1000],
            // Dasar Kopi
            ['nama' => 'Gula Pasir', 'satuan' => 'gram', 'harga_satuan' => 15, 'stok' => 2000],
            ['nama' => 'Gula Aren', 'satuan' => 'gram', 'harga_satuan' => 30, 'stok' => 1000],
            ['nama' => 'Susu Kental Manis', 'satuan' => 'ml', 'harga_satuan' => 40, 'stok' => 2000],
            ['nama' => 'Susu Cair Full Cream', 'satuan' => 'ml', 'harga_satuan' => 20, 'stok' => 5000],
            ['nama' => 'Krimer Bubuk', 'satuan' => 'gram', 'harga_satuan' => 50, 'stok' => 1000],
            // Cairan & Es
            ['nama' => 'Air Mineral', 'satuan' => 'ml', 'harga_satuan' => 5, 'stok' => 19000], // Galon
            ['nama' => 'Es Batu', 'satuan' => 'kg', 'harga_satuan' => 2000, 'stok' => 10],
            // Rasa Bubuk
            ['nama' => 'Bubuk Cokelat', 'satuan' => 'gram', 'harga_satuan' => 80, 'stok' => 500],
            ['nama' => 'Bubuk Matcha', 'satuan' => 'gram', 'harga_satuan' => 150, 'stok' => 500],
            ['nama' => 'Bubuk Milo', 'satuan' => 'gram', 'harga_satuan' => 100, 'stok' => 500],
            ['nama' => 'Oreo', 'satuan' => 'pcs', 'harga_satuan' => 1000, 'stok' => 50],
            ['nama' => 'Bubuk Red Velvet', 'satuan' => 'gram', 'harga_satuan' => 120, 'stok' => 500],
            // Sirup & Buah
            ['nama' => 'Sirup Vanilla', 'satuan' => 'ml', 'harga_satuan' => 100, 'stok' => 700],
            ['nama' => 'Sirup Caramel', 'satuan' => 'ml', 'harga_satuan' => 100, 'stok' => 700],
            ['nama' => 'Sirup Mangga', 'satuan' => 'ml', 'harga_satuan' => 80, 'stok' => 700],
            ['nama' => 'Mangga Segar', 'satuan' => 'gram', 'harga_satuan' => 30, 'stok' => 2000],
            ['nama' => 'Jahe Segar', 'satuan' => 'gram', 'harga_satuan' => 25, 'stok' => 1000],
            // Makanan - Dimsum
            ['nama' => 'Kulit Dimsum', 'satuan' => 'lembar', 'harga_satuan' => 200, 'stok' => 100],
            ['nama' => 'Daging Ayam Giling', 'satuan' => 'gram', 'harga_satuan' => 60, 'stok' => 2000],
            ['nama' => 'Wortel', 'satuan' => 'gram', 'harga_satuan' => 15, 'stok' => 1000],
            ['nama' => 'Tepung Tapioka', 'satuan' => 'gram', 'harga_satuan' => 20, 'stok' => 1000],
            // Makanan - Otak-otak & Sate
            ['nama' => 'Otak-otak Ikan', 'satuan' => 'pcs', 'harga_satuan' => 1500, 'stok' => 50],
            ['nama' => 'Sosis', 'satuan' => 'pcs', 'harga_satuan' => 2000, 'stok' => 50],
            ['nama' => 'Bakso', 'satuan' => 'pcs', 'harga_satuan' => 1000, 'stok' => 100],
            ['nama' => 'Nugget', 'satuan' => 'pcs', 'harga_satuan' => 1500, 'stok' => 50],
            ['nama' => 'Mie Instan', 'satuan' => 'pcs', 'harga_satuan' => 3500, 'stok' => 40],
            ['nama' => 'Mie Telur', 'satuan' => 'gram', 'harga_satuan' => 50, 'stok' => 1000],
            ['nama' => 'Telur Ayam', 'satuan' => 'butir', 'harga_satuan' => 2000, 'stok' => 30],
            // Sayur & Bumbu
            ['nama' => 'Sawi', 'satuan' => 'gram', 'harga_satuan' => 10, 'stok' => 500],
            ['nama' => 'Kol', 'satuan' => 'gram', 'harga_satuan' => 10, 'stok' => 500],
            ['nama' => 'Daun Bawang', 'satuan' => 'gram', 'harga_satuan' => 20, 'stok' => 200],
            ['nama' => 'Bawang Putih', 'satuan' => 'gram', 'harga_satuan' => 40, 'stok' => 500],
            ['nama' => 'Bawang Merah', 'satuan' => 'gram', 'harga_satuan' => 50, 'stok' => 500],
            ['nama' => 'Cabai Kering', 'satuan' => 'gram', 'harga_satuan' => 80, 'stok' => 200],
            ['nama' => 'Cabai Fresh', 'satuan' => 'gram', 'harga_satuan' => 60, 'stok' => 500],
            ['nama' => 'Biji Wijen', 'satuan' => 'gram', 'harga_satuan' => 100, 'stok' => 100],
            ['nama' => 'Minyak Goreng', 'satuan' => 'ml', 'harga_satuan' => 20, 'stok' => 2000],
            ['nama' => 'Garam', 'satuan' => 'gram', 'harga_satuan' => 10, 'stok' => 500],
            ['nama' => 'Merica', 'satuan' => 'gram', 'harga_satuan' => 150, 'stok' => 100],
            ['nama' => 'Penyedap Rasa', 'satuan' => 'gram', 'harga_satuan' => 20, 'stok' => 250],
            ['nama' => 'Kecap Manis', 'satuan' => 'ml', 'harga_satuan' => 30, 'stok' => 600],
            ['nama' => 'Saus Tiram', 'satuan' => 'ml', 'harga_satuan' => 40, 'stok' => 300],
            ['nama' => 'Saus Sambal', 'satuan' => 'ml', 'harga_satuan' => 25, 'stok' => 1000],
            ['nama' => 'Saus Tomat', 'satuan' => 'ml', 'harga_satuan' => 25, 'stok' => 1000],
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
                'stok_minimal' => 10,
            ]);
        }

        // 3. Insert Menu
        $menus = [
            'V60', 'V60 Arabica', 'Latte', 'Vietnam Drip', 'Coffee Arm', 'Sweet Latte', 'Kopi Tubruk',
            'Sweet Manggo', 'Red Velvet', 'Choco Matcha', 'Matcha Latte', 'Full Cream Milo', 'Es Susu',
            'Josu (Jahe Susu)',
            'Dimsum', 'Otak-otak Chilli Oil', 'Indomie', 'Sate-satean', 'Mie Nyemek'
        ];

        foreach ($menus as $m) {
            Menu::create([
                'nama' => $m,
                'harga' => 15000, // Placeholder price
                'tersedia' => 1
            ]);
        }
    }
}
