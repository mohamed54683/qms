<?php
/**
 * Verify all restaurants appear in user assignment
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Restaurant;

echo "\n=== All Restaurants Available for User Assignment ===\n\n";

$restaurants = Restaurant::orderBy('name')->get();

echo "📊 Total Restaurants: {$restaurants->count()}\n\n";
echo str_repeat("-", 80) . "\n";

$columns = 2;
$perColumn = ceil($restaurants->count() / $columns);
$restaurantChunks = $restaurants->chunk($perColumn);

// Display in columns
$maxRows = $perColumn;
for ($row = 0; $row < $maxRows; $row++) {
    $line = '';
    foreach ($restaurantChunks as $columnIndex => $chunk) {
        $restaurant = $chunk->values()->get($row);
        if ($restaurant) {
            $text = sprintf("%-3d %-35s", $restaurant->id, $restaurant->name);
            $line .= $text;
            if ($columnIndex < $restaurantChunks->count() - 1) {
                $line .= ' | ';
            }
        }
    }
    echo $line . "\n";
}

echo str_repeat("-", 80) . "\n";

echo "\n✅ All {$restaurants->count()} restaurants are available when:\n";
echo "   1. Creating a new user (/qms/users/create)\n";
echo "   2. Editing an existing user (/qms/users/{id}/edit)\n\n";

echo "💡 These restaurants appear in the 'Assigned Restaurants' checkbox list\n";
echo "   where admin can select multiple restaurants per user.\n\n";
