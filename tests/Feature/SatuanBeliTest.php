<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bahan;

class SatuanBeliTest extends TestCase
{
    public function test_can_add_and_update_bahan_with_satuan_beli()
    {
        // 1. Authenticate
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        // 2. Add Bahan with satuan_beli
        $payload = [
            'kode' => 'UNIT-TEST-001',
            'nama' => 'Bahan Test Unit',
            'satuan' => 'gram',
            'satuan_beli' => 'Pack', // Target Field
            'harga_persatuan' => 500,
            'jumlah_satuan' => 1000,
            'stok_minimal' => 5
        ];

        $response = $this->actingAs($user)->postJson('/api/add-bahan', $payload);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'created']);

        $this->assertDatabaseHas('bahan', [
            'kode' => 'UNIT-TEST-001',
            'satuan_beli' => 'Pack'
        ]);

        $bahan = Bahan::where('kode', 'UNIT-TEST-001')->first();

        // 3. Update Bahan change satuan_beli
        $updatePayload = [
            'kode' => 'UNIT-TEST-001',
            'nama' => 'Bahan Test Unit Updated',
            'satuan' => 'gram',
            'satuan_beli' => 'Karton', // Changed
            'harga_persatuan' => 600,
            'jumlah_satuan' => 1000,
            'stok_minimal' => 10
        ];

        $responseUpdate = $this->actingAs($user)->postJson('/api/update-bahan/' . $bahan->id, $updatePayload);

        $responseUpdate->assertStatus(200)
                       ->assertJson(['message' => 'updated']);

        $this->assertDatabaseHas('bahan', [
            'id' => $bahan->id,
            'satuan_beli' => 'Karton'
        ]);

        // 4. Cleanup
        $bahan->delete();
    }
}
