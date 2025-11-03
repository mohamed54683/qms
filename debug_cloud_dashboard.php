<?php
/**
 * Debug script to check dashboard errors on cloud server
 * Place this file in your web root and access it via browser
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Bootstrap Laravel
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    echo "<h1>Dashboard Debug - Cloud Server</h1>";
    
    // Test database connection
    echo "<h2>1. Database Connection Test</h2>";
    try {
        \DB::connection()->getPdo();
        echo "✅ Database connection: SUCCESS<br>";
        
        // Check database name
        $dbName = \DB::connection()->getDatabaseName();
        echo "📊 Database name: $dbName<br>";
        
    } catch (Exception $e) {
        echo "❌ Database connection: FAILED<br>";
        echo "Error: " . $e->getMessage() . "<br>";
    }

    // Test User model and authentication
    echo "<h2>2. User Model Test</h2>";
    try {
        $userCount = \App\Models\User::count();
        echo "✅ User model: SUCCESS ($userCount users)<br>";
        
        // Test first user
        $firstUser = \App\Models\User::first();
        if ($firstUser) {
            echo "👤 First user: " . $firstUser->name . " (" . $firstUser->email . ")<br>";
            
            // Test roles
            $roles = $firstUser->roles()->get();
            echo "🎭 User roles: " . $roles->pluck('name')->implode(', ') . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ User model: FAILED<br>";
        echo "Error: " . $e->getMessage() . "<br>";
    }

    // Test required tables
    echo "<h2>3. Required Tables Test</h2>";
    $tables = ['users', 'restaurants', 'store_visits', 'action_plans', 'roles', 'permissions'];
    foreach ($tables as $table) {
        try {
            $count = \DB::table($table)->count();
            echo "✅ Table '$table': $count records<br>";
        } catch (Exception $e) {
            echo "❌ Table '$table': FAILED - " . $e->getMessage() . "<br>";
        }
    }

    // Test Dashboard Controller logic
    echo "<h2>4. Dashboard Controller Test</h2>";
    try {
        // Simulate dashboard data gathering
        $totalUsers = \App\Models\User::count();
        $totalRestaurants = \DB::table('restaurants')->count();
        
        echo "📊 Total Users: $totalUsers<br>";
        echo "🏪 Total Restaurants: $totalRestaurants<br>";
        
        // Check for recent store visits
        if (\Schema::hasTable('store_visits')) {
            $recentVisits = \DB::table('store_visits')
                ->where('created_at', '>=', now()->subDays(30))
                ->count();
            echo "🔍 Recent visits (30 days): $recentVisits<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Dashboard data: FAILED<br>";
        echo "Error: " . $e->getMessage() . "<br>";
    }

    // Test permissions
    echo "<h2>5. Permission System Test</h2>";
    try {
        if (class_exists('\Spatie\Permission\Models\Role')) {
            $roleCount = \Spatie\Permission\Models\Role::count();
            echo "✅ Spatie Roles: $roleCount roles<br>";
        }
        
        if (class_exists('\Spatie\Permission\Models\Permission')) {
            $permissionCount = \Spatie\Permission\Models\Permission::count();
            echo "✅ Spatie Permissions: $permissionCount permissions<br>";
        }
        
    } catch (Exception $e) {
        echo "⚠️ Permission system: " . $e->getMessage() . "<br>";
    }

    // Check Laravel config
    echo "<h2>6. Laravel Configuration</h2>";
    echo "🔧 APP_ENV: " . config('app.env') . "<br>";
    echo "🔧 APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "<br>";
    echo "🔧 DB_CONNECTION: " . config('database.default') . "<br>";
    echo "🔧 Cache driver: " . config('cache.default') . "<br>";
    echo "🔧 Session driver: " . config('session.driver') . "<br>";

} catch (Exception $e) {
    echo "<h2>💥 Critical Error</h2>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<h3>📋 Recommendations:</h3>";
echo "<ul>";
echo "<li>Check your .env file configuration</li>";
echo "<li>Run 'php artisan config:clear' and 'php artisan cache:clear'</li>";
echo "<li>Check server error logs</li>";
echo "<li>Ensure all database tables are migrated</li>";
echo "<li>Verify file permissions</li>";
echo "</ul>";
?>