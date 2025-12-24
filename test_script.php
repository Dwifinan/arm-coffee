<?php

use Illuminate\Http\Request;
use App\Models\Bahan;
use App\Models\User;
use App\Http\Controllers\BahanController;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Bootstrapped Laravel...\n";

// Authenticate (Test User)
$user = User::first();
if (!$user) {
    echo "No user found, creating one...\n";
    $user = User::factory()->create();
}
auth()->login($user);
echo "Logged in as: " . $user->name . "\n";

// Instantiate Controller
$controller = new BahanController();

// 1. TEST ADD BAHAN
echo "Testing Add Bahan...\n";
$reqAdd = Request::create('/api/add-bahan', 'POST', [
    'kode' => 'TEST-SCRIPT-001',
    'nama' => 'Bahan Script Test',
    'satuan' => 'gram',
    'satuan_beli' => 'Dos', // Target
    'harga_persatuan' => 100,
    'jumlah_satuan' => 10,
    'low_limit' => 5 // note: controller validation uses 'low_limit' for stok_minimal? Wait, let's check controller code again.
    // In updateBahan/addBahan (JSON API) it used "stok_minimal".
    // IN STORE (Form Request) it used "low_limit".
    // The previous analysis showed:
    // addBahan (API endpoints) -> uses "stok_minimal"
    // store (Form Submit) -> uses "low_limit"
    // My fix targetted `addBahan` and `updateBahan` validation primarily?
    // Let's re-read the controller content I saw earlier.
]);
// Wait! `addBahan` (API) used `$request->validate([... 'stok_minimal' ...])`
// `store` (Web) used `$request->validate([... 'low_limit' ...])`
// The blade file uses `name="stok_minimal"` in the form.
// Does the blade form submit to `api/add-bahan` or `bahan.store`?
// The blade JS says: `$.post("{{ url('api/add-bahan') }}", ...)`
// So it uses the API endpoint `addBahan`.
// So the field expected is `stok_minimal`.

$reqAdd->replace([
    'kode' => 'TEST-SCRIPT-001',
    'nama' => 'Bahan Script Test',
    'satuan' => 'gram',
    'satuan_beli' => 'Dos',
    'harga_persatuan' => 100,
    'jumlah_satuan' => 10,
    'stok_minimal' => 5
]);


try {
    // Calling method directly (bypassing route middleware but keeping validation logic inside if manually invoked?)
    // Controller `addBahan(Request $request)` does `$request->validate`.
    // We need to pass the request.
    $resAdd = $controller->addBahan($reqAdd);
    
    // Response is JSON
    if ($resAdd->getStatusCode() == 200) {
        $content = $resAdd->getData(true);
        if ($content['message'] == 'created') {
            echo "PASS: Add Bahan Success.\n";
            // Verify DB
            $b = Bahan::where('kode', 'TEST-SCRIPT-001')->first();
            if ($b && $b->satuan_beli === 'Dos') {
                echo "PASS: DB Validation Correct (satuan_beli = Dos).\n";
            } else {
                echo "FAIL: DB Validation Failed. Got: " . ($b->satuan_beli ?? 'NULL') . "\n";
            }
        } else {
            echo "FAIL: Unexpected response message: " . $content['message'] . "\n";
        }
    } else {
        echo "FAIL: Status Code " . $resAdd->getStatusCode() . "\n";
    }

    // 2. TEST UPDATE BAHAN
    if (isset($b)) {
        echo "Testing Update Bahan...\n";
        $reqUpdate = Request::create('/api/update-bahan/' . $b->id, 'POST', [
            'kode' => 'TEST-SCRIPT-001',
            'nama' => 'Bahan Script Test Updated',
            'satuan' => 'gram',
            'satuan_beli' => 'Karton', // Changed
            'harga_persatuan' => 200,
            'jumlah_satuan' => 20,
            'stok_minimal' => 10
        ]);
        
        $resUpdate = $controller->updateBahan($reqUpdate, $b->id);
        
        if ($resUpdate->getStatusCode() == 200) {
            $b->refresh();
            if ($b->satuan_beli === 'Karton') {
                echo "PASS: Update Success (satuan_beli = Karton).\n";
            } else {
                echo "FAIL: Update Failed. Got: " . $b->satuan_beli . "\n";
            }
        } else {
            echo "FAIL: Update Status Code " . $resUpdate->getStatusCode() . "\n";
            print_r($resUpdate);
        }

        // Cleanup
        $b->delete();
        echo "Cleanup Done.\n";
    }

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
