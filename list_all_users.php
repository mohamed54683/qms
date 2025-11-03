<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "All Users in System:\n";
echo "==================\n\n";

$users = App\Models\User::with('roles')->get();

foreach ($users as $user) {
    echo "ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Roles: " . ($user->roles ? $user->roles->pluck('name')->implode(', ') : 'None') . "\n";
    echo "------------------------\n";
}

?>