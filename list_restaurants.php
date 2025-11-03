<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "📋 قائمة المطاعم المسجلة في النظام\n";
echo "=====================================\n\n";

$restaurants = \App\Models\Restaurant::orderBy('name')->get(['code', 'name', 'is_active']);

echo "العدد الإجمالي: " . $restaurants->count() . " مطعم\n";
echo "المطاعم النشطة: " . $restaurants->where('is_active', true)->count() . "\n\n";

echo "قائمة المطاعم:\n";
echo str_repeat("-", 60) . "\n";

foreach ($restaurants as $index => $restaurant) {
    $status = $restaurant->is_active ? '✓' : '✗';
    printf("%2d. [%s] %-10s %s\n", 
        $index + 1, 
        $status,
        $restaurant->code, 
        $restaurant->name
    );
}

echo "\n✅ تم عرض جميع المطاعم بنجاح!\n";
