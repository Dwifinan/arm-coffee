<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Bahan;
use App\Models\BahanMasuk;
use Carbon\Carbon;

class ExpiredBatchTest extends TestCase
{
    // use RefreshDatabase; // Caution with existing DB

    public function test_expired_batch_remains_expired_if_resolved_with_zero()
    {
        $user = User::first(); // Assume a user exists
        if(!$user) {
            $user = User::factory()->create();
        }

        $bahan = Bahan::create([
            'kode' => 'TEST-EXP',
            'nama' => 'Test Expired Item',
            'satuan' => 'kg',
            'harga_persatuan' => 10000,
            'stok' => 10,
            'stok_minimal' => 5,
            'jumlah_satuan' => 1
        ]);

        $batch = BahanMasuk::create([
            'bahan_id' => $bahan->id,
            'jumlah' => 10,
            'sisa_stok' => 10,
            'harga' => 100000,
            'harga_satuan' => 10000,
            'total_harga' => 100000,
            'expired' => Carbon::now()->subDays(1), // Expired yesterday
            'is_resolved' => false
        ]);

        // Act: Resolve with 0 disposed
        $response = $this->actingAs($user)->postJson('/api/bahan-masuk/' . $batch->id . '/resolve', [
            'qty_disposed' => 0
        ]);

        $response->assertStatus(200);

        $batch->refresh();
        $this->assertTrue($batch->is_resolved == 1);
        $this->assertEquals(10, $batch->sisa_stok);
        $this->assertTrue(Carbon::parse($batch->expired)->isPast()); // Still expired
        
        // Cleanup
        $batch->delete();
        $bahan->delete();
    }
}
