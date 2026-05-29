<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$kats = App\Models\Kategorilowongan::limit(5)->get();
foreach ($kats as $k) { echo $k->id . ': ' . $k->nama . "\n"; }
echo "\nRegister ID 1 existence: " . (App\Models\Register::find(1) ? 'Yes' : 'No') . "\n";
