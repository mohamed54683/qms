<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ActionPlan;
use App\Models\ActionPlanImage;
use Illuminate\Support\Facades\DB;

echo "=== Manually Linking Images to Visit 15 Action Plan ===\n\n";

// Get the action plan for visit 15
$actionPlan = ActionPlan::where('store_visit_id', 15)->first();

if (!$actionPlan) {
    echo "No action plan found for Visit 15\n";
    exit;
}

echo "Action Plan ID: {$actionPlan->id}\n";
echo "Item: {$actionPlan->item}\n\n";

// Get the uploaded image from storage
$storageDir = storage_path('app/public/action-plans');
$files = glob($storageDir . '/*.png');

if (empty($files)) {
    echo "No image files found in storage\n";
    exit;
}

$imageFile = $files[0]; // Get the first (and only) image
$basename = basename($imageFile);
echo "Found image file: $basename\n";

// The field name should be smile_greetings based on the action plan item
$fieldName = 'smile_greetings';

// Create the action plan image record
$image = ActionPlanImage::create([
    'action_plan_id' => $actionPlan->id,
    'image_path' => 'action-plans/' . $basename,
    'field_name' => $fieldName,
    'original_name' => $basename,
    'file_size' => filesize($imageFile),
    'mime_type' => 'image/png',
]);

echo "\n✓ Successfully linked image to action plan!\n";
echo "  ActionPlanImage ID: {$image->id}\n";
echo "  Field Name: {$image->field_name}\n";
echo "  Image Path: {$image->image_path}\n";
echo "  File Size: " . round($image->file_size / 1024, 2) . " KB\n";

echo "\nNow refresh the visit page to see the image!\n";
