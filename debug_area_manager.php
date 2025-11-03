<?php<?php



use Illuminate\Http\Request;require_once 'vendor/autoload.php';

use App\Models\ActionPlan;

use App\Models\User;$app = require_once 'bootstrap/app.php';

use Spatie\Permission\Models\Role;$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();



// Simple test endpoint to check area manager datause App\Models\User;

$user = auth()->user();use App\Models\QscChecklist;

$isAdmin = $user->roles && ($user->roles->contains('name', 'admin') || $user->roles->contains('name', 'Super Admin'));use App\Models\Restaurant;



echo "=== AREA MANAGER DEBUG ===\n";echo "=== DEBUG AREA MANAGER FILTERING ===\n\n";

echo "Current User: {$user->name} (ID: {$user->id})\n";

echo "Is Admin: " . ($isAdmin ? 'YES' : 'NO') . "\n";// 1. Check area managers and their assigned restaurants

echo "1. AREA MANAGERS AND THEIR RESTAURANTS:\n";

// Check permission$areaManagers = User::role('Area Manager')->with('restaurants')->get();

$canViewAreaManagerFilter = $isAdmin || $user->can('view_area_manager_filter');foreach ($areaManagers as $manager) {

echo "Can View Area Manager Filter: " . ($canViewAreaManagerFilter ? 'YES' : 'NO') . "\n";    echo "Area Manager: {$manager->name} (ID: {$manager->id})\n";

    echo "Assigned Restaurants:\n";

// Check Area Manager role    foreach ($manager->restaurants as $restaurant) {

$areaManagerRole = Role::where('name', 'Area Manager')->first();        echo "  - {$restaurant->name} (ID: {$restaurant->id})\n";

echo "Area Manager Role Exists: " . ($areaManagerRole ? 'YES' : 'NO') . "\n";    }

    echo "\n";

// Get area managers}

if ($canViewAreaManagerFilter) {

    if ($isAdmin) {// 2. Check QSC checklist store names

        $areaManagers = User::role('Area Manager')echo "2. QSC CHECKLIST STORE NAMES:\n";

            ->select('id', 'name')$qscStoreNames = QscChecklist::select('store_name')->distinct()->pluck('store_name');

            ->orderBy('name')foreach ($qscStoreNames as $storeName) {

            ->get();    echo "  - {$storeName}\n";

        echo "Area Managers Found: " . $areaManagers->count() . "\n";}

        foreach ($areaManagers as $manager) {echo "\n";

            echo "  - {$manager->name} (ID: {$manager->id})\n";

        }// 3. Check all restaurant names in restaurants table

    }echo "3. ALL RESTAURANTS IN DATABASE:\n";

}$allRestaurants = Restaurant::select('id', 'name')->get();

foreach ($allRestaurants as $restaurant) {

// Check actual filter response    echo "  - {$restaurant->name} (ID: {$restaurant->id})\n";

echo "\n=== SIMULATING CONTROLLER RESPONSE ===\n";}

$areaManagers = [];echo "\n";

if ($canViewAreaManagerFilter && $isAdmin) {

    $areaManagers = User::role('Area Manager')// 4. Test filtering for first area manager

        ->select('id', 'name')if ($areaManagers->count() > 0) {

        ->orderBy('name')    $testManager = $areaManagers->first();

        ->get()    echo "4. TESTING FILTER FOR AREA MANAGER: {$testManager->name}\n";

        ->toArray();    

}    $assignedRestaurants = $testManager->restaurants->pluck('name')->toArray();

    echo "Assigned restaurant names: " . implode(', ', $assignedRestaurants) . "\n";

echo "Area Managers Array: " . json_encode($areaManagers, JSON_PRETTY_PRINT) . "\n";    

echo "canViewAreaManagerFilter: " . json_encode($canViewAreaManagerFilter) . "\n";    $query = QscChecklist::query();

echo "isAdmin: " . json_encode($isAdmin) . "\n";    if (!empty($assignedRestaurants)) {
        $query->where(function($q) use ($assignedRestaurants) {
            foreach ($assignedRestaurants as $restaurant) {
                $q->orWhere('store_name', 'LIKE', '%' . $restaurant . '%');
            }
        });
    }
    
    $matchingChecklists = $query->get();
    echo "Matching checklists count: " . $matchingChecklists->count() . "\n";
    
    if ($matchingChecklists->count() > 0) {
        echo "Matching store names:\n";
        foreach ($matchingChecklists->pluck('store_name')->unique() as $storeName) {
            echo "  - {$storeName}\n";
        }
    }
}

echo "\n=== END DEBUG ===\n";