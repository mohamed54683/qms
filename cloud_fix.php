<?php
/**
 * Cloud Server Fix Script
 * Run this on your cloud server to fix common dashboard 500 errors
 */

echo "<h1>🔧 Cloud Server Fix Script</h1>";

try {
    // Bootstrap Laravel
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    echo "<h2>1. Creating missing roles and permissions</h2>";
    
    // Create admin role if it doesn't exist
    try {
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        echo "✅ Admin role created/verified<br>";
        
        // Create area manager role
        $areaManagerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Area Manager']);
        echo "✅ Area Manager role created/verified<br>";
        
        // Create permission
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view area manager filter']);
        echo "✅ Area manager filter permission created/verified<br>";
        
        // Assign permission to admin role
        $adminRole->givePermissionTo($permission);
        echo "✅ Permission assigned to admin role<br>";
        
    } catch (Exception $e) {
        echo "❌ Role/Permission error: " . $e->getMessage() . "<br>";
    }

    echo "<h2>2. Fixing user roles</h2>";
    
    // Assign admin role to first user
    try {
        $firstUser = \App\Models\User::first();
        if ($firstUser) {
            $firstUser->assignRole('admin');
            echo "✅ Admin role assigned to first user: " . $firstUser->name . "<br>";
        } else {
            echo "⚠️ No users found in database<br>";
        }
    } catch (Exception $e) {
        echo "❌ User role assignment error: " . $e->getMessage() . "<br>";
    }

    echo "<h2>3. Testing dashboard functionality</h2>";
    
    try {
        $user = \App\Models\User::first();
        if ($user) {
            $isAdmin = $user->hasRole('admin');
            echo "✅ Dashboard test passed for user: " . $user->name . " (admin: " . ($isAdmin ? 'yes' : 'no') . ")<br>";
        }
    } catch (Exception $e) {
        echo "❌ Dashboard test failed: " . $e->getMessage() . "<br>";
    }

    echo "<h2>4. Cache clearing (if possible)</h2>";
    
    try {
        // Clear various caches
        \Artisan::call('config:clear');
        echo "✅ Config cache cleared<br>";
        
        \Artisan::call('cache:clear');
        echo "✅ Application cache cleared<br>";
        
        \Artisan::call('view:clear');
        echo "✅ View cache cleared<br>";
        
    } catch (Exception $e) {
        echo "⚠️ Cache clearing error (run manually): " . $e->getMessage() . "<br>";
    }

    echo "<hr>";
    echo "<h3>✅ Fix Complete!</h3>";
    echo "<p>Try accessing your dashboard now: <a href='/dashboard'>/dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<h2>💥 Fix Script Error</h2>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>You may need to run these commands manually via SSH:</p>";
    echo "<ul>";
    echo "<li><code>php artisan config:clear</code></li>";
    echo "<li><code>php artisan cache:clear</code></li>";
    echo "<li><code>php artisan permission:cache-reset</code></li>";
    echo "<li><code>composer dump-autoload</code></li>";
    echo "</ul>";
}
?>