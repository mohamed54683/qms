<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use App\Models\User;
use Spatie\Permission\Models\Role;

// Simple test endpoint to check area manager data
$user = User::find(2); // Assuming user ID 2 based on previous tests
if (!$user) {
    echo "User not found!\n";
    exit;
}

$isAdmin = $user->roles && ($user->roles->contains('name', 'admin') || $user->roles->contains('name', 'Super Admin'));

echo "=== AREA MANAGER DEBUG ===\n";
echo "Current User: {$user->name} (ID: {$user->id})\n";
echo "Is Admin: " . ($isAdmin ? 'YES' : 'NO') . "\n";

// Check permission
$canViewAreaManagerFilter = $isAdmin || $user->can('view_area_manager_filter');
echo "Can View Area Manager Filter: " . ($canViewAreaManagerFilter ? 'YES' : 'NO') . "\n";

// Check Area Manager role
$areaManagerRole = Role::where('name', 'Area Manager')->first();
echo "Area Manager Role Exists: " . ($areaManagerRole ? 'YES' : 'NO') . "\n";

// Get area managers
if ($canViewAreaManagerFilter) {
    if ($isAdmin) {
        $areaManagers = User::role('Area Manager')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        echo "Area Managers Found: " . $areaManagers->count() . "\n";
        foreach ($areaManagers as $manager) {
            echo "  - {$manager->name} (ID: {$manager->id})\n";
        }
    }
}

// Check actual filter response
echo "\n=== SIMULATING CONTROLLER RESPONSE ===\n";
$areaManagers = [];
if ($canViewAreaManagerFilter && $isAdmin) {
    $areaManagers = User::role('Area Manager')
        ->select('id', 'name')
        ->orderBy('name')
        ->get()
        ->toArray();
}

echo "Area Managers Array: " . json_encode($areaManagers, JSON_PRETTY_PRINT) . "\n";
echo "canViewAreaManagerFilter: " . json_encode($canViewAreaManagerFilter) . "\n";
echo "isAdmin: " . json_encode($isAdmin) . "\n";