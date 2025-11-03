<?php
// Debug file to check what data is being passed to the action plans page
// Access this via: http://127.0.0.1:8000/debug-action-plans-data.php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Simulate logged-in user (replace with actual authentication)
$user = User::first(); // Get the first user for testing

if (!$user) {
    echo "No users found in the database!";
    exit;
}

echo "<h1>Action Plans Debug Data</h1>";
echo "<h2>User Information:</h2>";
echo "ID: " . $user->id . "<br>";
echo "Name: " . $user->name . "<br>";
echo "Email: " . $user->email . "<br>";

echo "<h2>User Roles:</h2>";
$roles = $user->roles->pluck('name')->toArray();
echo implode(', ', $roles) . "<br>";

echo "<h2>User Permissions:</h2>";
$permissions = $user->getAllPermissions()->pluck('name')->toArray();
echo implode(', ', $permissions) . "<br>";

echo "<h2>Check Specific Permissions:</h2>";
echo "Can view area manager filter: " . ($user->can('view area manager filter') ? 'YES' : 'NO') . "<br>";
echo "Is Admin: " . ($user->hasRole('admin') ? 'YES' : 'NO') . "<br>";

echo "<h2>Area Managers Available:</h2>";
$areaManagers = User::role('Area Manager')->get();
echo "Count: " . $areaManagers->count() . "<br>";
foreach ($areaManagers as $manager) {
    echo "- " . $manager->name . " (ID: " . $manager->id . ")<br>";
}

echo "<h2>Raw Controller Logic Test:</h2>";
$canViewAreaManagerFilter = $user->can('view area manager filter');
$isAdmin = $user->hasRole('admin');

echo "canViewAreaManagerFilter: " . ($canViewAreaManagerFilter ? 'true' : 'false') . "<br>";
echo "isAdmin: " . ($isAdmin ? 'true' : 'false') . "<br>";
echo "Should show filter: " . (($canViewAreaManagerFilter || $isAdmin) ? 'YES' : 'NO') . "<br>";