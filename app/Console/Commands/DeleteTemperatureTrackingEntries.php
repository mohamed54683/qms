<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TemperatureTrackingForm;

class DeleteTemperatureTrackingEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temperature:delete-all {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all temperature tracking entries from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Count existing records
        $formsCount = TemperatureTrackingForm::count();
        
        $this->info("Current Temperature Tracking Database Status:");
        $this->info("Temperature Tracking Forms: {$formsCount}");
        
        if ($formsCount === 0) {
            $this->info("No temperature tracking entries found. Database is already clean.");
            return 0;
        }
        
        // Ask for confirmation unless --force flag is used
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete ALL temperature tracking entries? This action cannot be undone.')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }
        
        $this->info('Deleting temperature tracking entries...');
        
        // Delete all temperature tracking forms
        $deletedForms = TemperatureTrackingForm::query()->delete();
        $this->info("Deleted {$deletedForms} temperature tracking forms.");
        
        $this->info('All temperature tracking entries have been successfully deleted!');
        
        return 0;
    }
}
