<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use App\Models\User;
use App\Models\ActionPlan;
use App\Models\Restaurant;
use App\Models\StoreVisit;

echo "=== AREA MANAGER FILTER TEST ===\n\n";

// 1. Check if area managers exist
$areaManagers = User::role('Area Manager')->get(['id', 'name']);
echo "Area Managers found: " . $areaManagers->count() . "\n";
foreach ($areaManagers as $manager) {
    echo "- {$manager->name} (ID: {$manager->id})\n";
    
    // Get restaurants assigned to this manager
    $restaurants = $manager->restaurants()->get(['id', 'name']);
    echo "  Assigned restaurants: " . $restaurants->count() . "\n";
    foreach ($restaurants as $restaurant) {
        echo "    - {$restaurant->name}\n";
        
        // Get action plans for this restaurant
        $actionPlans = ActionPlan::whereHas('storeVisit', function($q) use ($restaurant) {
            $q->where('restaurant_name', $restaurant->name);
        })->count();
        echo "      Action plans: {$actionPlans}\n";
    }
    echo "\n";
}

// 2. Test the filter logic (simulate what happens when you select an area manager)
if ($areaManagers->count() > 0) {
    $testManager = $areaManagers->first();
    echo "=== TESTING FILTER FOR: {$testManager->name} ===\n";
    
    // Get restaurants assigned to this area manager
    $assignedRestaurants = $testManager->restaurants->pluck('name')->toArray();
    echo "Assigned restaurant names: " . implode(', ', $assignedRestaurants) . "\n";
    
    if (!empty($assignedRestaurants)) {
        // Filter action plans (same logic as controller)
        $filteredActionPlans = ActionPlan::whereHas('storeVisit', function($q) use ($assignedRestaurants) {
            $q->whereIn('restaurant_name', $assignedRestaurants);
        })->get(['id', 'item', 'status']);
        
        echo "Filtered action plans count: " . $filteredActionPlans->count() . "\n";
        
        foreach ($filteredActionPlans->take(5) as $plan) {
            echo "- Action Plan #{$plan->id}: {$plan->item} (Status: {$plan->status})\n";
        }
    } else {
        echo "No restaurants assigned to this area manager\n";
    }
}

echo "\n=== FILTER URL EXAMPLE ===\n";
echo "To filter by area manager, use URL like:\n";
echo "http://127.0.0.1:8000/qms/action-plans?area_manager=1\n";
echo "Replace '1' with the actual area manager ID\n";