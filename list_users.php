<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::all(['id', 'name', 'email']);
echo "\n=== All Users ===\n\n";
foreach ($users as $u) {
    echo "ID: {$u->id} - {$u->name} ({$u->email})\n";
}
echo "\n";
