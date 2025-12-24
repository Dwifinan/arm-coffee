<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Bahan;

$names = ['Susu Cair Full Cream', 'Susu', 'Telur Ayam'];

foreach ($names as $name) {
    $item = Bahan::where('nama', 'LIKE', "%$name%")->first();
    if ($item) {
        echo "Found: " . $item->nama . "\n";
        echo "  ID: " . $item->id . "\n";
        echo "  Harga Persatuan (Package Price?): " . $item->harga_persatuan . "\n";
        echo "  Jumlah Satuan (Qty per Package?): " . $item->jumlah_satuan . "\n";
        echo "  Satuan: " . $item->satuan . "\n";
        // Calculate unit price if possible
        if ($item->jumlah_satuan > 0) {
            echo "  Calculated Unit Price: " . ($item->harga_persatuan / $item->jumlah_satuan) . "\n";
        }
    } else {
        echo "Not Found: $name\n";
    }
    echo "-------------------\n";
}
