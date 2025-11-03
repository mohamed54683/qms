<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StoreVisit;
use App\Models\ActionPlan;

class TestActionPlans extends Command
{
    protected $signature = 'test:action-plans';
    protected $description = 'Test action plan generation';

    public function handle()
    {
        // Create a quick test store visit with some "No" answers
        $storeVisit = StoreVisit::create([
            'user_id' => 1,
            'restaurant_name' => 'Test Restaurant',
            'mic' => 'Morning',
            'visit_date' => now(),
            'purpose_of_visit' => ['Quality Audit'],
            'oca_board_followed' => false, // This will generate an action plan
            'staff_know_duty' => false,   // This will generate an action plan
            'smile_greetings' => true,    // This won't
            'status' => 'Submitted',
            'oca_board_comments' => 'Board not properly updated',
            'staff_duty_comments' => 'Staff unclear about duties'
        ]);

        $this->info("Created test store visit ID: {$storeVisit->id}");

        // Test getActionItems
        $actionItems = $storeVisit->getActionItems();
        $this->info("Found " . count($actionItems) . " action items:");
        
        foreach ($actionItems as $item) {
            $this->line("- " . $item['question'] . " (" . $item['field'] . ")");
        }

        // Test if we can access the confirm route
        $this->info("\nTo test the confirm functionality, visit:");
        $this->info("http://localhost:8000/qms/store-visits/{$storeVisit->id}?confirm=1");
        
        return 0;
    }
}
