<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Bahan;

$items = Bahan::all();
$rows = [];
foreach ($items as $item) {
    $unitPrice = $item->jumlah_satuan > 0 ? $item->harga_persatuan / $item->jumlah_satuan : 0;
    $rows[] = ['name' => $item->nama, 'unit_price' => $unitPrice, 'raw' => "{$item->harga_persatuan}/{$item->jumlah_satuan} {$item->satuan}"];
}

usort($rows, function($a, $b) {
    return $b['unit_price'] <=> $a['unit_price']; // Descending
});

echo "TOP 10 EXPENSIVE:\n";
foreach (array_slice($rows, 0, 10) as $row) {
    printf("%-20s : Rp %d (Raw: %s)\n", $row['name'], $row['unit_price'], $row['raw']);
}

echo "\nBOTTOM 5 CHEAPEST:\n";
foreach (array_slice($rows, -5) as $row) {
    printf("%-20s : Rp %d (Raw: %s)\n", $row['name'], $row['unit_price'], $row['raw']);
}
