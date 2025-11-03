<?php
// Simple test to check authentication and action plans access
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\ActionPlan;

echo "<h1>Action Plans Access Test</h1>";

// Check if there are any users
$userCount = User::count();
echo "<h2>Users in Database: $userCount</h2>";

if ($userCount == 0) {
    echo "<p style='color: red;'>No users found! This might be why you're getting 404.</p>";
    echo "<p>The application might be redirecting to login page.</p>";
} else {
    $firstUser = User::first();
    echo "<p>First user: " . $firstUser->name . " (" . $firstUser->email . ")</p>";
    
    // Check if user has necessary roles/permissions
    echo "<h3>User Roles:</h3>";
    $roles = $firstUser->roles->pluck('name')->toArray();
    if (empty($roles)) {
        echo "<p style='color: orange;'>No roles assigned to user</p>";
    } else {
        echo "<ul>";
        foreach ($roles as $role) {
            echo "<li>$role</li>";
        }
        echo "</ul>";
    }
}

// Check action plans
$actionPlanCount = ActionPlan::count();
echo "<h2>Action Plans in Database: $actionPlanCount</h2>";

// Check if the route is accessible (try to simulate the controller)
echo "<h2>Route Test:</h2>";
echo "<p>Route 'qms/action-plans' should be accessible</p>";
echo "<p>If you're seeing 404, it might be an authentication issue</p>";

echo "<h2>Recommendations:</h2>";
echo "<ul>";
echo "<li>Make sure you're logged in</li>";
echo "<li>Try accessing: <a href='http://127.0.0.1:8000/login'>http://127.0.0.1:8000/login</a></li>";
echo "<li>After login, try: <a href='http://127.0.0.1:8000/qms/action-plans'>http://127.0.0.1:8000/qms/action-plans</a></li>";
echo "</ul>";