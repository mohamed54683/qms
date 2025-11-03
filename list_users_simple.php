<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Current Users ===\n";
$users = DB::table('users')->get();
foreach ($users as $user) {
    echo $user->name . ' - ' . $user->email . "\n";
}