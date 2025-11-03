<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🧹 Cleaning Store Visits and Action Plans Database...\n\n";

try {
    // Get counts before deletion
    $storeVisitsCount = DB::table('store_visits')->count();
    $actionPlansCount = DB::table('action_plans')->count();
    
    echo "📊 Current Records:\n";
    echo "   - Store Visits: {$storeVisitsCount}\n";
    echo "   - Action Plans: {$actionPlansCount}\n\n";
    
    // Start transaction for deletions only
    DB::beginTransaction();
    
    // Delete action plans first (foreign key constraint)
    echo "🗑️  Deleting Action Plans...\n";
    $deletedActionPlans = DB::table('action_plans')->delete();
    echo "   ✅ Deleted {$deletedActionPlans} action plans\n\n";
    
    // Delete store visits
    echo "🗑️  Deleting Store Visits...\n";
    $deletedStoreVisits = DB::table('store_visits')->delete();
    echo "   ✅ Deleted {$deletedStoreVisits} store visits\n\n";
    
    // Commit transaction
    DB::commit();
    
    // Reset auto-increment IDs (outside transaction - DDL statements auto-commit)
    echo "🔄 Resetting auto-increment counters...\n";
    DB::statement('ALTER TABLE action_plans AUTO_INCREMENT = 1');
    DB::statement('ALTER TABLE store_visits AUTO_INCREMENT = 1');
    echo "   ✅ Auto-increment reset\n\n";
    
    // Verify deletion
    $remainingStoreVisits = DB::table('store_visits')->count();
    $remainingActionPlans = DB::table('action_plans')->count();
    
    echo "✨ Database Cleaned Successfully!\n\n";
    echo "📊 Final Records:\n";
    echo "   - Store Visits: {$remainingStoreVisits}\n";
    echo "   - Action Plans: {$remainingActionPlans}\n\n";
    
    if ($remainingStoreVisits === 0 && $remainingActionPlans === 0) {
        echo "✅ All records successfully deleted!\n";
        echo "🎉 Database is now clean and ready for fresh data.\n";
    }
    
} catch (\Exception $e) {
    if (DB::transactionLevel() > 0) {
        DB::rollBack();
    }
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "💡 Tip: Make sure the database is accessible and tables exist.\n";
    exit(1);
}
