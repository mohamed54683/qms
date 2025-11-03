<?php
/**
 * Script to insert restaurant names into the live server database
 * Run this script to add all the restaurant locations to your QMS system
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Restaurant;

// List of all restaurant names to insert
$restaurantNames = [
    'Abha',
    'Abha-TNDR',
    'Al-Qassim',
    'Al-Sultan-T',
    'Albadia',
    'Ameer Fawaz',
    'Awali',
    'Aziziyah-Madina',
    'Aziziyah-Riyad',
    'Basateen',
    'Batha Quresh',
    'DAMMAM',
    'Dahiat Laban',
    'Faisaliya',
    'Hail',
    'Hamdaniya',
    'Hamdaniya-TNDR',
    'Hassan Bin Thabit',
    'Hijra',
    'Hira',
    'Iceland',
    'Imam Saud -TNDR',
    'KHARG',
    'KING FAHAD MED',
    'Khaleej',
    'Khamis Mushet',
    'Khobar',
    'Khulias',
    'King Salman',
    'Marwa',
    'Marwa-TNDR',
    'Mohamadiah',
    'Nahdah',
    'Nakheel',
    'Nassem',
    'Nassem-TNDR',
    'Nozha',
    'Obhur',
    'Qurtuba',
    'Rabigh',
    'Raqi',
    'Rawabi Plaza',
    'Rehab',
    'Riy-Warehouse Dep',
    'Riyadh- Factory',
    'Rowdah',
    'Rowdah-TNDR',
    'Samer',
    'King Salman', // Note: This appears twice in your list
    'Sari',
    'Shoukiya',
    'Shraia',
    'Sultana -TNDR',
    'Tahlia',
    'Taif-Hawiyah',
    'Taif-Shehar',
    'Thumama',
    'Umluj',
    'Waziriyah',
    'Yanbu 1',
    'Yanbu 2',
    'Yasmen',
    'Zaidi (Sasco)',
    'Jed-Warehouse Dep',
    'Jeddah Factory'
];

echo "<h1>Restaurant Insertion Script</h1>";
echo "<p>Starting to insert " . count($restaurantNames) . " restaurants...</p>";

$inserted = 0;
$skipped = 0;
$errors = 0;

foreach ($restaurantNames as $index => $name) {
    try {
        // Generate a unique code for each restaurant
        $code = 'R' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
        
        // Check if restaurant already exists
        $existingRestaurant = Restaurant::where('name', $name)
            ->orWhere('code', $code)
            ->first();
            
        if ($existingRestaurant) {
            echo "<p style='color: orange;'>⚠️ Skipped: '$name' (already exists)</p>";
            $skipped++;
            continue;
        }
        
        // Create new restaurant
        $restaurant = Restaurant::create([
            'name' => $name,
            'code' => $code,
            'address' => 'Saudi Arabia', // Default address
            'phone' => null,
            'email' => null,
            'manager_name' => null,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "<p style='color: green;'>✅ Inserted: '$name' (Code: $code)</p>";
        $inserted++;
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error inserting '$name': " . $e->getMessage() . "</p>";
        $errors++;
    }
}

echo "<hr>";
echo "<h2>Summary:</h2>";
echo "<p><strong>✅ Successfully inserted:</strong> $inserted restaurants</p>";
echo "<p><strong>⚠️ Skipped (already exist):</strong> $skipped restaurants</p>";
echo "<p><strong>❌ Errors:</strong> $errors restaurants</p>";
echo "<p><strong>📊 Total processed:</strong> " . count($restaurantNames) . " restaurants</p>";

if ($inserted > 0) {
    echo "<hr>";
    echo "<h3>Next Steps:</h3>";
    echo "<ul>";
    echo "<li>✅ Restaurants have been inserted into your database</li>";
    echo "<li>🔧 You can now assign users to these restaurants</li>";
    echo "<li>📋 These restaurants will appear in all dropdown lists</li>";
    echo "<li>📊 You can start creating store visits and action plans for these locations</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='http://127.0.0.1:8000/qms/restaurants'>View Restaurants in QMS System</a></p>";
?>