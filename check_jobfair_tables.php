<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = DB::select('SELECT TABLE_NAME FROM information_schema.tables WHERE TABLE_SCHEMA = "jobfair"');
foreach($tables as $t) { echo $t->TABLE_NAME . "\n"; }
