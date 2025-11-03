<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ActionPlan;
use App\Models\StoreVisit;

echo "=== Checking Latest Store Visit and Action Plans ===\n\n";

// Get the most recent store visit
$latestVisit = StoreVisit::orderBy('id', 'desc')->first();

if ($latestVisit) {
    echo "📋 Latest Store Visit:\n";
    echo "  ID: {$latestVisit->id}\n";
    echo "  Restaurant: {$latestVisit->restaurant_name}\n";
    echo "  Date: {$latestVisit->created_at}\n";
    echo "  Attachments: " . ($latestVisit->attachments ?: 'NULL') . "\n";
    
    if ($latestVisit->attachments) {
        $images = json_decode($latestVisit->attachments, true);
        echo "  Number of images: " . count($images) . "\n";
        foreach ($images as $img) {
            echo "    - {$img}\n";
            $fullPath = storage_path('app/public/' . $img);
            if (file_exists($fullPath)) {
                echo "      ✅ File exists (" . round(filesize($fullPath)/1024, 2) . " KB)\n";
            } else {
                echo "      ❌ File NOT found at: {$fullPath}\n";
            }
        }
    } else {
        echo "  ❌ No attachments saved!\n";
        echo "  💡 Check if images were uploaded correctly\n";
    }
    echo "\n";
    
    // Check if there are action plans for this visit
    $actionPlans = ActionPlan::where('store_visit_id', $latestVisit->id)->get();
    echo "📊 Action Plans for this visit: {$actionPlans->count()}\n";
    
    foreach ($actionPlans as $plan) {
        echo "  - Action Plan ID: {$plan->id}\n";
        echo "    Item: {$plan->item}\n";
        echo "    Issue: {$plan->issue}\n";
        
        // Load with relationship
        $planWithVisit = ActionPlan::with('storeVisit')->find($plan->id);
        if ($planWithVisit->storeVisit) {
            echo "    Store Visit Loaded: ✅\n";
            echo "    Store Visit Attachments: " . ($planWithVisit->storeVisit->attachments ?: 'NULL') . "\n";
        } else {
            echo "    Store Visit Loaded: ❌\n";
        }
        echo "\n";
    }
} else {
    echo "❌ No store visits found!\n";
}

echo "=== Storage Directory Check ===\n";
$storageDir = storage_path('app/public/store-visits');
if (is_dir($storageDir)) {
    echo "✅ Storage directory exists: {$storageDir}\n";
    $files = glob($storageDir . '/*');
    echo "Files in directory: " . count($files) . "\n";
    foreach ($files as $file) {
        echo "  - " . basename($file) . " (" . round(filesize($file)/1024, 2) . " KB)\n";
    }
} else {
    echo "❌ Storage directory NOT found: {$storageDir}\n";
}

echo "\n✅ Diagnostic complete!\n";
