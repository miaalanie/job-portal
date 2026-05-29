<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$evens = App\Models\Even::all();
foreach ($evens as $e) { echo $e->id . ': ' . $e->namaperiode . "\n"; }
