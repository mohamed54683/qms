<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

echo "=== Clearing All Store Visits and Action Plans ===\n\n";

try {
    // Start transaction
    DB::beginTransaction();
    
    // 1. Delete all action plan images (cascade will handle this, but let's be explicit)
    $imageCount = DB::table('action_plan_images')->count();
    echo "Deleting $imageCount action plan image records...\n";
    DB::table('action_plan_images')->delete();
    
    // 2. Delete all action plans
    $planCount = DB::table('action_plans')->count();
    echo "Deleting $planCount action plans...\n";
    DB::table('action_plans')->delete();
    
    // 3. Delete all store visits
    $visitCount = DB::table('store_visits')->count();
    echo "Deleting $visitCount store visits...\n";
    DB::table('store_visits')->delete();
    
    // 4. Clear image files from storage
    $storagePath = storage_path('app/public/action-plans');
    if (is_dir($storagePath)) {
        $files = glob($storagePath . '/*');
        $fileCount = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $fileCount++;
            }
        }
        echo "Deleted $fileCount image files from storage...\n";
    }
    
    // 5. Clear session data
    echo "Clearing session data...\n";
    session()->flush();
    
    // Commit transaction
    DB::commit();
    
    echo "\n✅ SUCCESS!\n";
    echo "- $visitCount store visits deleted\n";
    echo "- $planCount action plans deleted\n";
    echo "- $imageCount image records deleted\n";
    echo "- All image files removed from storage\n";
    echo "\nDatabase is now clean and ready for testing!\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}
