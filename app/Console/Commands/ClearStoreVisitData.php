<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StoreVisit;
use App\Models\ActionPlan;

class ClearStoreVisitData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:store-visit-data {--confirm : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all store visit reports and action plans from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get counts before deletion
        $storeVisitCount = StoreVisit::count();
        $actionPlanCount = ActionPlan::count();
        
        $this->info("Found:");
        $this->info("- Store Visits: {$storeVisitCount}");
        $this->info("- Action Plans: {$actionPlanCount}");
        
        if ($storeVisitCount === 0 && $actionPlanCount === 0) {
            $this->info("No data to delete.");
            return;
        }
        
        // Confirm deletion unless --confirm flag is used
        if (!$this->option('confirm')) {
            if (!$this->confirm('Are you sure you want to DELETE ALL store visit data and action plans? This cannot be undone.')) {
                $this->info('Operation cancelled.');
                return;
            }
        }
        
        $this->info('Deleting data...');
        
        // Delete action plans first (they reference store visits)
        if ($actionPlanCount > 0) {
            ActionPlan::query()->delete();
            $this->info("✓ Deleted {$actionPlanCount} action plans");
        }
        
        // Delete store visits
        if ($storeVisitCount > 0) {
            StoreVisit::query()->delete();
            $this->info("✓ Deleted {$storeVisitCount} store visits");
        }
        
        $this->info('');
        $this->info('🎉 All store visit data and action plans have been deleted successfully!');
        $this->info('The database is now clean and ready for new data.');
    }
}
