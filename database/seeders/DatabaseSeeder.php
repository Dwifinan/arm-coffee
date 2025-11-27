<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Bahan;
use App\Models\Menu;
use App\Models\Komposisi;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * =============================================
         *  1. SEED USER
         * =============================================
         */
        User::create([
            'username' => 'owner',
            'password' => Hash::make('owner123'),
            'role' => 'owner',
        ]);

        User::create([
            'username' => 'staff',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
        ]);


        /**
         * =============================================
         *  2. SEED BAHAN
         *  (tanpa harga_satuan karena dihitung otomatis)
         * =============================================
         */
        $bahans = [
            [
                'kode' => 'B001',
                'nama' => 'Gula Pasir',
                'satuan' => 'g',
                'stok_minimal' => 1000,
                'stok' => 5000,
                'harga_persatuan' => 12000,
                'jumlah_satuan' => 1000,
            ],
            [
                'kode' => 'B002',
                'nama' => 'Kopi Bubuk',
                'satuan' => 'g',
                'stok_minimal' => 1000,
                'stok' => 3000,
                'harga_persatuan' => 45000,
                'jumlah_satuan' => 1000,
            ],
            [
                'kode' => 'B003',
                'nama' => 'Susu UHT',
                'satuan' => 'ml',
                'stok_minimal' => 2000,
                'stok' => 10000,
                'harga_persatuan' => 15000,
                'jumlah_satuan' => 1000,
            ],
            [
                'kode' => 'B004',
                'nama' => 'Teh Celup',
                'satuan' => 'pcs',
                'stok_minimal' => 20,
                'stok' => 50,
                'harga_persatuan' => 1000,
                'jumlah_satuan' => 1,
            ],
            [
                'kode' => 'B005',
                'nama' => 'Coklat Bubuk',
                'satuan' => 'g',
                'stok_minimal' => 1000,
                'stok' => 2000,
                'harga_persatuan' => 30000,
                'jumlah_satuan' => 1000,
            ],
            [
                'kode' => 'B006',
                'nama' => 'Air Mineral',
                'satuan' => 'ml',
                'stok_minimal' => 5000,
                'stok' => 20000,
                'harga_persatuan' => 5000,
                'jumlah_satuan' => 1000,
            ],
            [
                'kode' => 'B007',
                'nama' => 'Roti',
                'satuan' => 'pcs',
                'stok_minimal' => 10,
                'stok' => 30,
                'harga_persatuan' => 2000,
                'jumlah_satuan' => 1,
            ],
            [
                'kode' => 'B008',
                'nama' => 'Selai Coklat',
                'satuan' => 'g',
                'stok_minimal' => 1000,
                'stok' => 5000,
                'harga_persatuan' => 18000,
                'jumlah_satuan' => 1000,
            ],
        ];

        foreach ($bahans as $bahan) {
            Bahan::create($bahan);
        }


        /**
         * =============================================
         *  3. SEED MENU
         * =============================================
         */

        $menus = [
            ['nama' => 'Es Kopi Susu', 'harga' => 15000],
            ['nama' => 'Kopi Tubruk', 'harga' => 10000],
            ['nama' => 'Teh Manis', 'harga' => 5000],
            ['nama' => 'Roti Coklat', 'harga' => 8000],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }


        /**
         * =============================================
         *  4. SEED KOMPOSISI MENU
         *  (diambil dari id yang sudah dibuat)
         * =============================================
         */

        // Ambil ID bahan berdasarkan kode
        $bahan = Bahan::pluck('id', 'kode');
        $menu  = Menu::pluck('id', 'nama');

        $komposisi = [
            // === Es Kopi Susu ===
            [
                'menu' => 'Es Kopi Susu',
                'bahan' => 'B002',
                'jumlah' => 15,
                'satuan' => 'g'
            ],
            [
                'menu' => 'Es Kopi Susu',
                'bahan' => 'B003',
                'jumlah' => 50,
                'satuan' => 'ml'
            ],
            [
                'menu' => 'Es Kopi Susu',
                'bahan' => 'B001',
                'jumlah' => 10,
                'satuan' => 'g'
            ],

            // === Kopi Tubruk ===
            [
                'menu' => 'Kopi Tubruk',
                'bahan' => 'B002',
                'jumlah' => 10,
                'satuan' => 'g'
            ],

            // === Teh Manis ===
            [
                'menu' => 'Teh Manis',
                'bahan' => 'B004',
                'jumlah' => 1,
                'satuan' => 'pcs'
            ],
            [
                'menu' => 'Teh Manis',
                'bahan' => 'B001',
                'jumlah' => 15,
                'satuan' => 'g'
            ],

            // === Roti Coklat ===
            [
                'menu' => 'Roti Coklat',
                'bahan' => 'B007',
                'jumlah' => 1,
                'satuan' => 'pcs'
            ],
            [
                'menu' => 'Roti Coklat',
                'bahan' => 'B008',
                'jumlah' => 20,
                'satuan' => 'g'
            ],
        ];

        foreach ($komposisi as $k) {
            Komposisi::create([
                'menu_id'  => $menu[$k['menu']],
                'bahan_id' => $bahan[$k['bahan']],
                'jumlah_bahan' => $k['jumlah'],
                'satuan' => $k['satuan'],
            ]);
        }
    }
}
