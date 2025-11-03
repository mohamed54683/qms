<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

DB::table('action_plans')->where('id', 5)->update(['is_approved' => true]);
echo 'Updated action plan 5 to approved' . PHP_EOL;