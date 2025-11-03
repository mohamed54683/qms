<?php
// Create test admin user
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Create test admin user
$testUser = User::updateOrCreate(
    ['email' => 'admin@test.com'],
    [
        'name' => 'Test Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('password123'),
        'email_verified_at' => now()
    ]
);

// Ensure admin role exists
$adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

// Ensure area manager filter permission exists
$permission = Permission::firstOrCreate(['name' => 'view area manager filter', 'guard_name' => 'web']);

// Assign admin role to test user
$testUser->assignRole('admin');

// Give admin role all permissions
$adminRole->givePermissionTo($permission);

echo "<h1>Test User Created Successfully!</h1>";
echo "<h2>Login Credentials:</h2>";
echo "<p><strong>Email:</strong> admin@test.com</p>";
echo "<p><strong>Password:</strong> password123</p>";
echo "<p><a href='http://127.0.0.1:8000/login'>Click here to login</a></p>";
echo "<p>After login, go to: <a href='http://127.0.0.1:8000/qms/action-plans'>Action Plans</a></p>";