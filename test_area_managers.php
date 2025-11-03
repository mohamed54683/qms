<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

echo "=== Area Manager Testing ===\n\n";

// Check if view_area_manager_filter permission exists
echo "1. Checking permissions:\n";
$permissions = Permission::all()->pluck('name')->toArray();
echo "All permissions: " . implode(', ', $permissions) . "\n";

if (in_array('view_area_manager_filter', $permissions)) {
    echo "✅ view_area_manager_filter permission EXISTS\n";
} else {
    echo "❌ view_area_manager_filter permission MISSING\n";
}

echo "\n2. Checking Area Manager role:\n";
$areaManagerRole = Role::where('name', 'Area Manager')->first();
if ($areaManagerRole) {
    echo "✅ Area Manager role EXISTS\n";
} else {
    echo "❌ Area Manager role MISSING\n";
}

echo "\n3. Checking users with Area Manager role:\n";
$areaManagers = User::role('Area Manager')->get(['id', 'name', 'email']);
if ($areaManagers->count() > 0) {
    echo "✅ Found " . $areaManagers->count() . " area managers:\n";
    foreach ($areaManagers as $manager) {
        echo "  - {$manager->name} ({$manager->email})\n";
    }
} else {
    echo "❌ No area managers found\n";
}

echo "\n4. Checking current user permissions:\n";
$user = User::find(2); // Assuming user ID 2 based on session
if ($user) {
    echo "Current user: {$user->name} ({$user->email})\n";
    echo "User roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
    
    if ($user->can('view_area_manager_filter')) {
        echo "✅ User CAN view area manager filter\n";
    } else {
        echo "❌ User CANNOT view area manager filter\n";
    }
} else {
    echo "❌ User not found\n";
}
