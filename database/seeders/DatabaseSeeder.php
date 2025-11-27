<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Bahan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- Seed data User ---
        User::create([
            'username' => 'owner1',
            'password' => Hash::make('owner123'),
            'role' => 'owner',
        ]);

        User::create([
            'username' => 'staff1',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
        ]);

        // --- Seed data Bahan ---
        $bahans = [
            ['nama' => 'Gula Pasir',   'kode' => 'B001', 'satuan' => 'kg',     'stok_minimal' => 1,  'stok' => 5],
            ['nama' => 'Kopi Bubuk',   'kode' => 'B002', 'satuan' => 'kg',     'stok_minimal' => 1,  'stok' => 3],
            ['nama' => 'Susu UHT',     'kode' => 'B003', 'satuan' => 'liter',  'stok_minimal' => 2,  'stok' => 10],
            ['nama' => 'Teh Celup',    'kode' => 'B004', 'satuan' => 'pcs',    'stok_minimal' => 20, 'stok' => 50],
            ['nama' => 'Coklat Bubuk', 'kode' => 'B005', 'satuan' => 'kg',     'stok_minimal' => 1,  'stok' => 2],
            ['nama' => 'Air Mineral',  'kode' => 'B006', 'satuan' => 'liter',  'stok_minimal' => 5,  'stok' => 20],
            ['nama' => 'Roti',         'kode' => 'B007', 'satuan' => 'pcs',    'stok_minimal' => 10, 'stok' => 30],
            ['nama' => 'Selai Coklat', 'kode' => 'B008', 'satuan' => 'kg',     'stok_minimal' => 1,  'stok' => 5],
        ];

        foreach ($bahans as $bahan) {
            Bahan::create($bahan);
        }
    }
}
