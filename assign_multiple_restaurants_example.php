<?php
/**
 * Example: Assign Multiple Restaurants to a User
 * This demonstrates how to assign multiple restaurants to a single user
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Restaurant;

echo "\n=== Assign Multiple Restaurants to User ===\n\n";

// Example 1: Get a user by ID
$userId = 2; // GS QMS Admin
$user = User::find($userId);

if (!$user) {
    echo "❌ User with ID {$userId} not found.\n";
    exit;
}

echo "👤 User: {$user->name} (ID: {$user->id})\n";
echo "📧 Email: {$user->email}\n\n";

// Show current assigned restaurants
$currentRestaurants = $user->restaurants;
echo "📊 Currently Assigned Restaurants ({$currentRestaurants->count()}):\n";
if ($currentRestaurants->isEmpty()) {
    echo "   None\n";
} else {
    foreach ($currentRestaurants as $restaurant) {
        echo "   - {$restaurant->name} (ID: {$restaurant->id})\n";
    }
}

echo "\n" . str_repeat("-", 80) . "\n";
echo "\n💡 Example Commands to Assign Multiple Restaurants:\n\n";

// Example restaurant IDs (Al Basateen, Abha, Al-Qassim, DAMMAM)
$exampleRestaurantIds = [1, 6, 9, 17];
$exampleRestaurants = Restaurant::whereIn('id', $exampleRestaurantIds)->pluck('name', 'id');

echo "// Method 1: ADD restaurants to existing assignments (keeps current assignments)\n";
echo "\$user = User::find({$userId});\n";
echo "\$user->restaurants()->attach([" . implode(', ', $exampleRestaurantIds) . "]);\n\n";

echo "Example will assign these restaurants:\n";
foreach ($exampleRestaurants as $id => $name) {
    echo "   - {$name} (ID: {$id})\n";
}

echo "\n" . str_repeat("-", 80) . "\n\n";

echo "// Method 2: REPLACE all assignments (removes previous, sets new ones)\n";
echo "\$user = User::find({$userId});\n";
echo "\$user->restaurants()->sync([" . implode(', ', $exampleRestaurantIds) . "]);\n\n";

echo "Example will replace current assignments with these restaurants:\n";
foreach ($exampleRestaurants as $id => $name) {
    echo "   - {$name} (ID: {$id})\n";
}

echo "\n" . str_repeat("-", 80) . "\n\n";

echo "// Method 3: Remove specific restaurants\n";
echo "\$user = User::find({$userId});\n";
echo "\$user->restaurants()->detach([1, 6]); // Remove Al Basateen and Abha\n\n";

echo str_repeat("-", 80) . "\n\n";

echo "// Method 4: Remove ALL restaurants\n";
echo "\$user = User::find({$userId});\n";
echo "\$user->restaurants()->detach(); // Remove all assignments\n\n";

echo "\n✅ To actually assign restaurants, uncomment one of these methods:\n\n";

// UNCOMMENT ONE OF THESE TO ASSIGN RESTAURANTS:

// Option A: Add to existing assignments
// $user->restaurants()->attach($exampleRestaurantIds);
// echo "✅ Added {count($exampleRestaurantIds)} restaurants to user!\n";

// Option B: Replace all assignments
// $user->restaurants()->sync($exampleRestaurantIds);
// echo "✅ User now has {count($exampleRestaurantIds)} assigned restaurants!\n";

echo "💡 Tip: Edit this file and uncomment the desired method to assign restaurants.\n\n";
