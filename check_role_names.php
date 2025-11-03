<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Role Names\n";
echo "==================\n\n";

// Get user ID 5 (area manager)
$user = App\Models\User::with('roles')->find(5);

if ($user) {
    echo "User: " . $user->name . "\n";
    echo "User ID: " . $user->id . "\n";
    
    if ($user->roles->count() > 0) {
        foreach ($user->roles as $role) {
            echo "Role ID: " . $role->id . "\n";
            echo "Role Name: '" . $role->name . "'\n";
            echo "Role Display Name: '" . ($role->display_name ?? 'Not set') . "'\n";
        }
    } else {
        echo "No roles found\n";
    }
}

echo "\n";

// Get all roles
echo "All Available Roles:\n";
echo "===================\n";

$roles = Spatie\Permission\Models\Role::all();

foreach ($roles as $role) {
    echo "ID: " . $role->id . " | Name: '" . $role->name . "' | Guard: " . $role->guard_name . "\n";
}

?>