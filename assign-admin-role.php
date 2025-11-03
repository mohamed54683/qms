<?php
// Give admin role to first user for testing
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

$user = User::first();
if (!$user) {
    echo "No users found!";
    exit;
}

// Make sure admin role exists
$adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

// Assign admin role to first user
$user->assignRole('admin');

echo "Successfully assigned admin role to user: " . $user->name . " (" . $user->email . ")";