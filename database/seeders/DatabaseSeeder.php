<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Bahan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Seed User ---
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

        // --- Seed Bahan (satuan terkecil) ---
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

        // Insert tanpa harga_satuan
        foreach ($bahans as $bahan) {
            Bahan::create($bahan);
        }
    }
}
