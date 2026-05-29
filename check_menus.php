<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$akses = App\Models\Aksesmenu::where('idrole', 3)->with('menu')->get();
foreach ($akses as $a) { echo $a->menu->namamenu . "\n"; }
