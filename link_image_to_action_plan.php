<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Linking Image to Action Plan ===\n";

// The uploaded image for smile_greetings field
$imagePath = 'action-plans/1761732653_6901e82dc7ed9.png';
$fieldName = 'smile_greetings';
$actionPlanId = 1; // First action plan for Smile and Friendly Greetings

// Check if file exists
$fullPath = __DIR__ . '/storage/app/public/' . $imagePath;
if (!file_exists($fullPath)) {
    echo "ERROR: Image file not found at $fullPath\n";
    exit(1);
}

$fileSize = filesize($fullPath);
$mimeType = mime_content_type($fullPath);
$originalName = '1062.png'; // From the upload

echo "Image file found:\n";
echo "  Path: $imagePath\n";
echo "  Size: $fileSize bytes\n";
echo "  MIME: $mimeType\n";
echo "  Original: $originalName\n";
echo "  Field: $fieldName\n";
echo "  Action Plan ID: $actionPlanId\n\n";

// Insert the record
try {
    $id = DB::table('action_plan_images')->insertGetId([
        'action_plan_id' => $actionPlanId,
        'image_path' => $imagePath,
        'field_name' => $fieldName,
        'original_name' => $originalName,
        'file_size' => $fileSize,
        'mime_type' => $mimeType,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "✅ SUCCESS! Image linked to action plan.\n";
    echo "   action_plan_images record ID: $id\n\n";
    
    // Verify
    $record = DB::table('action_plan_images')->find($id);
    echo "Verification:\n";
    foreach (get_object_vars($record) as $key => $value) {
        echo "  $key: $value\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
