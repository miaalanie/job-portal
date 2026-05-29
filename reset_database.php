<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tablesToKeep = ['menus', 'aksesmenus', 'roles', 'migrations'];
$tables = DB::connection()->getSchemaBuilder()->getTableListing();

echo "Starting Database Reset...\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

foreach ($tables as $table) {
    if (!in_array($table, $tablesToKeep)) {
        try {
            DB::table($table)->truncate();
            echo "[SUCCESS] Truncated: $table\n";
        } catch (\Exception $e) {
            echo "[ERROR] Failed to truncate $table: " . $e->getMessage() . "\n";
        }
    } else {
        echo "[SKIP] Preserving: $table\n";
    }
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "Database Reset Completed. Except: " . implode(', ', $tablesToKeep) . "\n";
