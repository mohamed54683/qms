<?php
/**
 * Update table column widths for compact layout
 */

$file = 'resources/views/store-visits/simple-report.blade.php';
$content = file_get_contents($file);

// Replace all table headers with more compact layout
$content = str_replace(
    ['width: 40%', 'width: 15%', 'width: 45%'],
    ['width: 45%', 'width: 12%', 'width: 43%'],
    $content
);

file_put_contents($file, $content);

echo "✅ Updated table column widths for compact layout:\n";
echo "📊 Item column: 40% → 45%\n";
echo "🎯 Status column: 15% → 12% (more compact)\n";
echo "💬 Comments column: 45% → 43%\n";
echo "📄 This will make the report more compact and fit better on fewer pages\n";