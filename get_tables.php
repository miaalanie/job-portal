<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (DB::select('SHOW TABLES') as $table) {
    echo array_values((array)$table)[0] . PHP_EOL;
}
