<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Assigning Area Manager Filter Permission\n";
echo "=======================================\n\n";

// Give permission to admin user
$adminUser = App\Models\User::find(2);

if ($adminUser) {
    try {
        $adminUser->givePermissionTo('view_area_manager_filter');
        echo "✅ Permission granted to admin user: " . $adminUser->name . "\n";
        
        // Test permission
        $canView = $adminUser->can('view_area_manager_filter');
        echo "✅ Permission check result: " . ($canView ? 'TRUE' : 'FALSE') . "\n";
    } catch (Exception $e) {
        echo "❌ Error granting permission: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Admin user not found\n";
}

// Also give permission to Super Admin role
try {
    $superAdminRole = Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();
    if ($superAdminRole) {
        $superAdminRole->givePermissionTo('view_area_manager_filter');
        echo "✅ Permission granted to Super Admin role\n";
    }
    
    $adminRole = Spatie\Permission\Models\Role::where('name', 'admin')->first();
    if ($adminRole) {
        $adminRole->givePermissionTo('view_area_manager_filter');
        echo "✅ Permission granted to admin role\n";
    }
} catch (Exception $e) {
    echo "❌ Error granting role permission: " . $e->getMessage() . "\n";
}

echo "\n✅ Permission setup completed!\n";

?>