<?php
/**
 * Assign multiple restaurants to it@ghidas.com user
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Restaurant;

echo "\n=== Assigning Multiple Restaurants to User ===\n\n";

// Get the user
$user = User::where('email', 'it@ghidas.com')->first();

if (!$user) {
    echo "❌ User it@ghidas.com not found.\n";
    exit;
}

echo "👤 User: {$user->name}\n";
echo "📧 Email: {$user->email}\n";
echo "🆔 ID: {$user->id}\n\n";

// Show current assignments
$currentRestaurants = $user->restaurants;
echo "📊 Current Assigned Restaurants ({$currentRestaurants->count()}):\n";
if ($currentRestaurants->isEmpty()) {
    echo "   None\n";
} else {
    foreach ($currentRestaurants as $restaurant) {
        echo "   - {$restaurant->name} (ID: {$restaurant->id})\n";
    }
}

echo "\n" . str_repeat("-", 80) . "\n\n";

// Assign 2 restaurants: Al Basateen (ID: 1) and Abha (ID: 6)
$restaurantIds = [1, 6]; // Al Basateen and Abha

echo "🔄 Assigning restaurants...\n";

// Use sync to replace all assignments with these two
$user->restaurants()->sync($restaurantIds);

echo "✅ Successfully assigned!\n\n";

// Show new assignments
$newRestaurants = $user->restaurants()->get();
echo "📊 New Assigned Restaurants ({$newRestaurants->count()}):\n";
foreach ($newRestaurants as $restaurant) {
    echo "   ✓ {$restaurant->name} (ID: {$restaurant->id})\n";
}

echo "\n" . str_repeat("-", 80) . "\n";
echo "\n✅ Done! User it@ghidas.com now has {$newRestaurants->count()} assigned restaurants.\n";
echo "   When creating Temperature Tracking forms, the dropdown will show these restaurants.\n";
echo "   The user will only see reports for these restaurants.\n\n";
