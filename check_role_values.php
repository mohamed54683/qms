<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Role Values\n";
echo "==================\n\n";

$areaManagerUser = App\Models\User::with('roles')->find(5); // Test user

if ($areaManagerUser) {
    echo "User: " . $areaManagerUser->name . "\n";
    echo "Role from roles collection: " . ($areaManagerUser->roles->first()->name ?? 'No role') . "\n";
    echo "Role from first role: " . ($areaManagerUser->roles->first() ? $areaManagerUser->roles->first()->name : 'No role') . "\n";
    
    // Check all roles
    echo "All roles: ";
    foreach ($areaManagerUser->roles as $role) {
        echo $role->name . ", ";
    }
    echo "\n";
    
    // Test role checking
    echo "Has 'Area Manager' role: " . ($areaManagerUser->hasRole('Area Manager') ? 'TRUE' : 'FALSE') . "\n";
    echo "Has 'area_manager' role: " . ($areaManagerUser->hasRole('area_manager') ? 'TRUE' : 'FALSE') . "\n";
    
} else {
    echo "User not found\n";
}

?>