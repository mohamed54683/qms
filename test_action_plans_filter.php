<?php

require_once 'vendor/autoload.php';

// Simple test to check if area manager filter works
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://127.0.0.1:8000/qms/action-plans',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ),
));

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

echo "HTTP Status: $httpCode\n";

if ($httpCode === 200) {
    echo "✅ Action plans page is accessible\n";
    
    // Check if area manager filter appears in the response
    if (strpos($response, 'area_manager') !== false) {
        echo "✅ Area manager filter is present in the page\n";
    } else {
        echo "❌ Area manager filter is NOT present in the page\n";
    }
    
    if (strpos($response, 'canViewAreaManagerFilter') !== false) {
        echo "✅ canViewAreaManagerFilter property is present\n";
    } else {
        echo "❌ canViewAreaManagerFilter property is NOT present\n";
    }
    
} else {
    echo "❌ Failed to access action plans page\n";
    echo "Error: " . curl_error($curl) . "\n";
}

curl_close($curl);