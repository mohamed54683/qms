<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

echo "=== Diagnosing Visit #16 ===\n\n";

// Check visit
$visit = DB::table('store_visits')->where('id', 16)->first();
if (!$visit) {
    echo "Visit #16 not found!\n";
    exit;
}

echo "Visit Details:\n";
echo "  Restaurant: {$visit->restaurant_name}\n";
echo "  Status: {$visit->status}\n";
echo "  Created: {$visit->created_at}\n\n";

// Check action plans
$plans = DB::table('action_plans')->where('store_visit_id', 16)->get();
echo "Action Plans: " . count($plans) . "\n\n";

foreach ($plans as $plan) {
    echo "Plan #{$plan->id}:\n";
    echo "  Item: {$plan->item}\n";
    echo "  Status: {$plan->status}\n";
    
    // Check images for this plan
    $images = DB::table('action_plan_images')
        ->where('action_plan_id', $plan->id)
        ->get();
    echo "  Images: " . count($images) . "\n";
    
    foreach ($images as $img) {
        echo "    - {$img->image_path} (Field: {$img->field_name})\n";
    }
    echo "\n";
}

// Check all images in storage
echo "=== Storage Files ===\n\n";
$storageDir = storage_path('app/public/action-plans');
if (file_exists($storageDir)) {
    $files = glob($storageDir . '/*');
    echo "Total files: " . count($files) . "\n\n";
    foreach ($files as $file) {
        if (is_file($file)) {
            echo basename($file) . " (" . round(filesize($file) / 1024, 2) . " KB)\n";
        }
    }
} else {
    echo "Directory does not exist!\n";
}

// Check all action_plan_images records
echo "\n=== All Action Plan Images Records ===\n\n";
$allImages = DB::table('action_plan_images')->get();
echo "Total records: " . count($allImages) . "\n\n";

foreach ($allImages as $img) {
    echo "Image #{$img->id}:\n";
    echo "  Action Plan: {$img->action_plan_id}\n";
    echo "  Field: {$img->field_name}\n";
    echo "  Path: {$img->image_path}\n";
    $exists = Storage::disk('public')->exists($img->image_path) ? 'YES' : 'NO';
    echo "  File exists: {$exists}\n\n";
}

// Check which fields had "No" answer
echo "=== Fields with 'No' Answer ===\n\n";
$fields = [
    'oca_board_followed', 'staff_know_duty', 'coaching_directing', 'smile_greetings',
    'suggestive_selling', 'offer_promotion', 'thank_direction', 'team_work_hustle',
    'order_accuracy', 'service_time', 'dine_in', 'take_out', 'family', 'delivery',
    'drive_thru', 'weekly_schedule', 'mod_financial_goal', 'sales_objectives',
    'cash_policies', 'daily_waste', 'ttf_followed', 'sandwich_assembly', 'qsc_completed',
    'oil_standards', 'day_labels', 'equipment_working', 'fryer_condition',
    'vegetable_prep', 'employee_appearance', 'equipment_wrapped', 'sink_setup',
    'sanitizer_standard', 'dining_area_clean', 'restroom_clean',
];

$noAnswers = [];
foreach ($fields as $field) {
    if (isset($visit->$field) && ($visit->$field === 'No' || $visit->$field === false || $visit->$field === 0)) {
        $noAnswers[] = $field;
        echo "{$field}: No\n";
    }
}

if (empty($noAnswers)) {
    echo "No fields with 'No' answer found!\n";
}
