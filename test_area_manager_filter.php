<?php<?php



require_once 'vendor/autoload.php';require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

use App\Models\User;$kernel->bootstrap();

use App\Models\ActionPlan;

use App\Models\Restaurant;echo "Testing Store Visits Area Manager Filter\n";

use App\Models\StoreVisit;echo "======================================\n\n";



echo "=== AREA MANAGER FILTER TEST ===\n\n";// Test admin user (should see area manager filter)

$adminUser = App\Models\User::with('roles')->find(2);

// 1. Check if area managers existecho "Testing with Admin User: " . $adminUser->name . "\n";

$areaManagers = User::role('Area Manager')->get(['id', 'name']);echo "Roles: " . $adminUser->roles->pluck('name')->implode(', ') . "\n";

echo "Area Managers found: " . $areaManagers->count() . "\n";

foreach ($areaManagers as $manager) {// Test isAdmin logic

    echo "- {$manager->name} (ID: {$manager->id})\n";$isAdmin = $adminUser->roles && (

        $adminUser->roles->contains('name', 'admin') || 

    // Get restaurants assigned to this manager    $adminUser->roles->contains('name', 'Super Admin')

    $restaurants = $manager->restaurants()->get(['id', 'name']););

    echo "  Assigned restaurants: " . $restaurants->count() . "\n";echo "isAdmin: " . ($isAdmin ? 'TRUE' : 'FALSE') . "\n\n";

    foreach ($restaurants as $restaurant) {

        echo "    - {$restaurant->name}\n";// Get area managers (same logic as controller)

        $areaManagers = [];

        // Get action plans for this restaurantif ($isAdmin) {

        $actionPlans = ActionPlan::whereHas('storeVisit', function($q) use ($restaurant) {    $areaManagers = App\Models\User::role('Area Manager')

            $q->where('restaurant_name', $restaurant->name);        ->select('id', 'name')

        })->count();        ->orderBy('name')

        echo "      Action plans: {$actionPlans}\n";        ->get();

    }}

    echo "\n";

}echo "Area Managers Available for Filter:\n";

echo "==================================\n";

// 2. Test the filter logic (simulate what happens when you select an area manager)if ($areaManagers->count() > 0) {

if ($areaManagers->count() > 0) {    foreach ($areaManagers as $manager) {

    $testManager = $areaManagers->first();        echo "- ID: " . $manager->id . " | Name: " . $manager->name . "\n";

    echo "=== TESTING FILTER FOR: {$testManager->name} ===\n";        

            // Check what restaurants this manager has

    // Get restaurants assigned to this area manager        $managerWithRestaurants = App\Models\User::with('restaurants')->find($manager->id);

    $assignedRestaurants = $testManager->restaurants->pluck('name')->toArray();        if ($managerWithRestaurants && $managerWithRestaurants->restaurants->count() > 0) {

    echo "Assigned restaurant names: " . implode(', ', $assignedRestaurants) . "\n";            echo "  Restaurants: " . $managerWithRestaurants->restaurants->pluck('name')->implode(', ') . "\n";

            } else {

    if (!empty($assignedRestaurants)) {            echo "  Restaurants: None assigned\n";

        // Filter action plans (same logic as controller)        }

        $filteredActionPlans = ActionPlan::whereHas('storeVisit', function($q) use ($assignedRestaurants) {    }

            $q->whereIn('restaurant_name', $assignedRestaurants);} else {

        })->get(['id', 'item', 'status']);    echo "No area managers found\n";

        }

        echo "Filtered action plans count: " . $filteredActionPlans->count() . "\n";

        echo "\n";

        foreach ($filteredActionPlans->take(5) as $plan) {

            echo "- Action Plan #{$plan->id}: {$plan->item} (Status: {$plan->status})\n";// Test filtering by area manager

        }echo "Testing Area Manager Filter Functionality:\n";

    } else {echo "=========================================\n";

        echo "No restaurants assigned to this area manager\n";

    }if ($areaManagers->count() > 0) {

}    $testManager = $areaManagers->first();

    echo "Testing filter with: " . $testManager->name . " (ID: " . $testManager->id . ")\n";

echo "\n=== FILTER URL EXAMPLE ===\n";    

echo "To filter by area manager, use URL like:\n";    // Get area manager's restaurants

echo "http://127.0.0.1:8000/qms/action-plans?area_manager=1\n";    $areaManagerUser = App\Models\User::with('restaurants')->find($testManager->id);

echo "Replace '1' with the actual area manager ID\n";    if ($areaManagerUser && $areaManagerUser->restaurants->count() > 0) {
        $assignedRestaurants = $areaManagerUser->restaurants->pluck('name')->toArray();
        echo "Manager's restaurants: " . implode(', ', $assignedRestaurants) . "\n";
        
        // Count store visits for these restaurants
        $filteredVisits = App\Models\StoreVisit::whereIn('restaurant_name', $assignedRestaurants)->get();
        echo "Store visits in manager's restaurants: " . $filteredVisits->count() . "\n";
        
        if ($filteredVisits->count() > 0) {
            echo "Sample visits:\n";
            foreach ($filteredVisits->take(3) as $visit) {
                echo "  - " . $visit->restaurant_name . " | " . $visit->visit_date . " | " . $visit->user->name . "\n";
            }
        }
    } else {
        echo "Manager has no assigned restaurants\n";
    }
} else {
    echo "No area managers to test with\n";
}

?>