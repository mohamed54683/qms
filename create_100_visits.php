<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StoreVisit;
use App\Models\User;
use Carbon\Carbon;

echo "Creating 100 store visits for Al Basateen...\n\n";

// Get the first user (or admin)
$user = User::first();

if (!$user) {
    echo "Error: No users found in database. Please create a user first.\n";
    exit(1);
}

echo "Creating visits for user: {$user->name} (ID: {$user->id})\n";

$statuses = ['Draft', 'Submitted', 'Pending Review', 'Approved'];
$mics = ['Morning', 'Afternoon', 'Evening'];
$purposes = [
    ['Quality Audit'],
    ['Operational Assessment'],
    ['Training Audit'],
    ['Measurement & Coaching'],
    ['Quality Audit', 'Operational Assessment'],
    ['Training Audit', 'Measurement & Coaching']
];

$created = 0;
$startDate = Carbon::now()->subMonths(6); // Start from 6 months ago

for ($i = 1; $i <= 100; $i++) {
    try {
        // Random date within the last 6 months
        $visitDate = $startDate->copy()->addDays(rand(0, 180));
        
        // Random values for Yes/No fields
        $yesNo = [true, false];
        
        $visit = StoreVisit::create([
            'user_id' => $user->id,
            'restaurant_name' => 'Al Basateen',
            'mic' => $mics[array_rand($mics)],
            'visit_date' => $visitDate,
            'purpose_of_visit' => $purposes[array_rand($purposes)],
            'status' => $statuses[array_rand($statuses)],
            
            // Customer / QSC
            'oca_board_followed' => $yesNo[array_rand($yesNo)],
            'oca_board_comments' => rand(0, 1) ? 'Sample comment for OCA board check ' . $i : null,
            'staff_know_duty' => $yesNo[array_rand($yesNo)],
            'staff_duty_comments' => rand(0, 1) ? 'Sample comment for staff duty ' . $i : null,
            'coaching_directing' => $yesNo[array_rand($yesNo)],
            'coaching_comments' => rand(0, 1) ? 'Sample comment for coaching ' . $i : null,
            
            // Cashier
            'smile_greetings' => $yesNo[array_rand($yesNo)],
            'smile_comments' => rand(0, 1) ? 'Sample comment for smile/greetings ' . $i : null,
            'suggestive_selling' => $yesNo[array_rand($yesNo)],
            'selling_comments' => rand(0, 1) ? 'Sample comment for suggestive selling ' . $i : null,
            
            // Service Standards
            'team_work_hustle' => $yesNo[array_rand($yesNo)],
            'hustle_comments' => rand(0, 1) ? 'Sample comment for team work ' . $i : null,
            'fast_service' => $yesNo[array_rand($yesNo)],
            'service_comments' => rand(0, 1) ? 'Sample comment for fast service ' . $i : null,
            'product_quality' => $yesNo[array_rand($yesNo)],
            'quality_comments' => rand(0, 1) ? 'Sample comment for product quality ' . $i : null,
            
            // Financials
            'weekly_schedule' => $yesNo[array_rand($yesNo)],
            'schedule_comments' => rand(0, 1) ? 'Sample comment for weekly schedule ' . $i : null,
            'mod_financial_goal' => $yesNo[array_rand($yesNo)],
            'financial_comments' => rand(0, 1) ? 'Sample comment for financial goals ' . $i : null,
            
            // Quality / Pathing
            'ttf_followed' => $yesNo[array_rand($yesNo)],
            'ttf_comments' => rand(0, 1) ? 'Sample comment for TTF ' . $i : null,
            'equipment_working' => $yesNo[array_rand($yesNo)],
            'equipment_comments' => rand(0, 1) ? 'Sample comment for equipment ' . $i : null,
            
            // Cleanliness
            'dining_area_clean' => $yesNo[array_rand($yesNo)],
            'dining_comments' => rand(0, 1) ? 'Sample comment for dining area ' . $i : null,
            'restroom_clean' => $yesNo[array_rand($yesNo)],
            'restroom_comments' => rand(0, 1) ? 'Sample comment for restroom ' . $i : null,
            
            // Follow-Up (only for some visits)
            'last_visit_date' => rand(0, 1) ? $visitDate->copy()->subDays(rand(7, 30))->format('Y-m-d') : null,
            'last_visit_summary' => rand(0, 1) ? 'Summary from previous visit for record ' . $i : null,
            'last_visit_update' => rand(0, 1) ? 'Update on previous issues for record ' . $i : null,
            'other_follow_up' => rand(0, 1) ? 'Other follow-up notes for record ' . $i : null,
            
            // Observation Summary (only for some visits)
            'what_did_you_see' => rand(0, 1) ? 'Observed various operations during visit ' . $i : null,
            'why_had_issue' => rand(0, 1) ? 'Issues were due to staff training gaps ' . $i : null,
            'how_to_improve' => rand(0, 1) ? 'Implement additional training sessions ' . $i : null,
            'who_when_responsible' => rand(0, 1) ? 'Manager to complete by next week ' . $i : null,
            
            // General Comments
            'general_comments' => rand(0, 1) ? 'General observations and notes for visit #' . $i : null,
            
            // Score (70-100 range)
            'score' => rand(70, 100),
        ]);
        
        $created++;
        
        if ($created % 10 == 0) {
            echo "Created {$created} visits...\n";
        }
        
    } catch (Exception $e) {
        echo "Error creating visit #{$i}: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Successfully created {$created} store visits for Al Basateen!\n";
echo "Date range: " . $startDate->format('Y-m-d') . " to " . Carbon::now()->format('Y-m-d') . "\n";
