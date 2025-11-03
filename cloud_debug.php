<?php
/**
 * Cloud Server Dashboard Debug Script
 * Upload this to your cloud host and access it to debug the 500 error
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>🌩️ Cloud Server Dashboard Debug</h1>";

try {
    // Check if Laravel files exist
    if (!file_exists('vendor/autoload.php')) {
        throw new Exception("❌ Composer autoload not found. Run 'composer install'");
    }
    
    if (!file_exists('bootstrap/app.php')) {
        throw new Exception("❌ Laravel bootstrap file not found");
    }

    // Bootstrap Laravel
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    echo "<h2>✅ Laravel Bootstrap Successful</h2>";

    // Check environment
    echo "<h2>🔧 Environment Check</h2>";
    echo "APP_ENV: " . config('app.env') . "<br>";
    echo "APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "<br>";
    echo "PHP Version: " . PHP_VERSION . "<br>";
    echo "Laravel Version: " . app()->version() . "<br>";

    // Test database connection
    echo "<h2>🗄️ Database Connection</h2>";
    try {
        $pdo = \DB::connection()->getPdo();
        echo "✅ Database connected successfully<br>";
        echo "Database: " . \DB::connection()->getDatabaseName() . "<br>";
        
        // Test a simple query
        $result = \DB::select('SELECT COUNT(*) as count FROM users');
        echo "✅ Users table accessible (count: " . $result[0]->count . ")<br>";
        
    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "<br>";
        echo "Check your .env database settings<br>";
    }

    // Test User model and roles
    echo "<h2>👤 User Model & Roles Test</h2>";
    try {
        // Test User model
        $user = \App\Models\User::first();
        if ($user) {
            echo "✅ User model works: " . $user->name . "<br>";
            
            // Test roles relationship (this is likely causing the 500 error)
            try {
                $roles = $user->roles;
                echo "✅ Roles relationship works (count: " . $roles->count() . ")<br>";
                
                // Test hasRole method
                $isAdmin = $user->hasRole('admin');
                echo "✅ hasRole method works (admin: " . ($isAdmin ? 'yes' : 'no') . ")<br>";
                
            } catch (Exception $e) {
                echo "❌ ROLES ERROR (likely cause of 500): " . $e->getMessage() . "<br>";
                echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
            }
            
        } else {
            echo "⚠️ No users found in database<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ User model error: " . $e->getMessage() . "<br>";
    }

    // Test Spatie Permission tables
    echo "<h2>🔐 Permission System Tables</h2>";
    $tables = ['roles', 'permissions', 'model_has_roles', 'model_has_permissions', 'role_has_permissions'];
    foreach ($tables as $table) {
        try {
            $count = \DB::table($table)->count();
            echo "✅ $table: $count records<br>";
        } catch (Exception $e) {
            echo "❌ $table: " . $e->getMessage() . "<br>";
        }
    }

    // Test dashboard controller logic simulation
    echo "<h2>📊 Dashboard Logic Simulation</h2>";
    try {
        // Simulate what dashboard controller does
        $user = auth()->user() ?: \App\Models\User::first();
        
        if ($user) {
            echo "👤 Testing with user: " . $user->name . "<br>";
            
            // This is likely where the error occurs
            $isAdmin = $user->hasRole('admin') || $user->hasRole('Super Admin');
            echo "✅ Admin check passed: " . ($isAdmin ? 'yes' : 'no') . "<br>";
            
            // Test restaurant relationship
            $restaurants = $user->restaurants;
            echo "✅ Restaurant relationship: " . $restaurants->count() . " restaurants<br>";
            
        } else {
            echo "❌ No authenticated user found<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ DASHBOARD LOGIC ERROR: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }

} catch (Exception $e) {
    echo "<h2>💥 Critical Bootstrap Error</h2>";
    echo "<p style='color: red; font-weight: bold;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<h3>🔧 Quick Fixes to Try:</h3>";
echo "<ol>";
echo "<li>Run: <code>php artisan config:clear</code></li>";
echo "<li>Run: <code>php artisan cache:clear</code></li>";
echo "<li>Run: <code>php artisan view:clear</code></li>";
echo "<li>Run: <code>composer install --no-dev --optimize-autoloader</code></li>";
echo "<li>Check file permissions (755 for folders, 644 for files)</li>";
echo "<li>Ensure storage/ and bootstrap/cache/ are writable</li>";
echo "</ol>";
?>