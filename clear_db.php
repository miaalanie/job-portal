<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting data cleanup...\n";

try {
    Schema::disableForeignKeyConstraints();

    $tablesToClear = [
        'lamarans',
        'wishlists',
        'kehadirans',
        'lowongans',
        'lowonganklasifikasis',
        'pelamardokumens',
        'pelamarpendidikans',
        'pelamarpengalamen',
        'pelamarskills',
        'pelamars',
        'register_payments',
        'registers',
        'even_sesis',
        'even_sponsors',
        'even_pakets',
        'evens',
        'perusahaan_dokumens',
        'perusahaans'
    ];

    foreach ($tablesToClear as $table) {
        if (Schema::hasTable($table)) {
            echo "Truncating $table...\n";
            DB::table($table)->truncate();
        }
    }

    echo "Cleaning up Pelamar & Perusahaan users...\n";
    DB::table('users')->whereNotNull('idperusahaan')->orWhereNotNull('idpelamar')->delete();

    Schema::enableForeignKeyConstraints();
    echo "Cleanup finished successfully!\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
