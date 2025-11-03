<?php

require_once 'vendor/autoload.php';

// Use the same database configuration
$host = 'localhost';
$dbname = 'qmsnew';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== READING STORE VISIT DATA ===\n\n";
    
    // First, let's check the table structure
    echo "📋 STORE VISITS TABLE STRUCTURE:\n";
    $stmt = $pdo->query("DESCRIBE store_visits");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']})\n";
    }
    echo "\n" . str_repeat("=", 60) . "\n\n";
    
    // Get all store visits with their data
    $stmt = $pdo->query("
        SELECT 
            sv.*,
            r.name as restaurant_name,
            r.location as restaurant_location
        FROM store_visits sv
        LEFT JOIN restaurants r ON sv.restaurant_id = r.id
        ORDER BY sv.created_at DESC 
        LIMIT 10
    ");
    
    $visits = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($visits)) {
        echo "❌ No store visits found in the database\n";
        echo "💡 Try creating some visits first in the application\n";
        exit;
    }
    
    echo "✅ Found " . count($visits) . " store visits\n\n";
    
    foreach ($visits as $index => $visit) {
        echo "📊 VISIT #" . ($index + 1) . " - ID: {$visit['id']}\n";
        echo str_repeat("-", 50) . "\n";
        
        // Basic Information
        echo "🏪 Restaurant: " . ($visit['restaurant_name'] ?: 'Unknown') . "\n";
        echo "📍 Location: " . ($visit['restaurant_location'] ?: 'Not specified') . "\n";
        echo "📅 Date: " . ($visit['date'] ?: $visit['created_at']) . "\n";
        echo "👤 Created by: " . ($visit['created_by'] ?: 'System User') . "\n";
        echo "🕒 Shift: " . ($visit['mic'] ?: 'Not specified') . "\n";
        echo "📊 Status: " . ($visit['status'] ?: 'Draft') . "\n";
        echo "🎯 Score: " . ($visit['score'] ?: '0') . "%\n";
        echo "🎭 Purpose: " . ($visit['purpose'] ?: 'Regular Inspection') . "\n\n";
        
        // Inspection Data
        echo "🛡️ FOOD SAFETY & HYGIENE:\n";
        $hygieneFields = [
            'personal_hygiene' => 'Personal Hygiene',
            'temperature_control' => 'Temperature Control',
            'food_storage' => 'Food Storage',
            'kitchen_cleanliness' => 'Kitchen Cleanliness'
        ];
        
        foreach ($hygieneFields as $field => $label) {
            $value = $visit[$field];
            $comment = $visit[$field . '_comment'] ?? '';
            $status = $value === '1' ? '✅ PASS' : ($value === '0' ? '❌ FAIL' : '⚪ N/A');
            echo "  • {$label}: {$status}";
            if ($comment) {
                echo " - Comment: \"{$comment}\"";
            }
            echo "\n";
        }
        
        echo "\n🤝 CUSTOMER SERVICE:\n";
        $serviceFields = [
            'smile_greetings' => 'Smile & Greetings',
            'staff_know_duty' => 'Staff Knowledge',
            'correct_sequence' => 'Service Sequence'
        ];
        
        foreach ($serviceFields as $field => $label) {
            $value = $visit[$field];
            $comment = $visit[$field . '_comment'] ?? '';
            $status = $value === '1' ? '✅ PASS' : ($value === '0' ? '❌ FAIL' : '⚪ N/A');
            echo "  • {$label}: {$status}";
            if ($comment) {
                echo " - Comment: \"{$comment}\"";
            }
            echo "\n";
        }
        
        echo "\n⚙️ OPERATIONS & COMPLIANCE:\n";
        $operationsFields = [
            'oca_board_followed' => 'OCA Board Followed',
            'pos_system_working' => 'POS System Working',
            'equipment_working' => 'Equipment Working',
            'facilities_clean' => 'Facilities Clean'
        ];
        
        foreach ($operationsFields as $field => $label) {
            $value = $visit[$field];
            $comment = $visit[$field . '_comment'] ?? '';
            $status = $value === '1' ? '✅ PASS' : ($value === '0' ? '❌ FAIL' : '⚪ N/A');
            echo "  • {$label}: {$status}";
            if ($comment) {
                echo " - Comment: \"{$comment}\"";
            }
            echo "\n";
        }
        
        // Additional comments
        if ($visit['comments']) {
            echo "\n💬 ADDITIONAL COMMENTS:\n";
            echo "  \"{$visit['comments']}\"\n";
        }
        
        echo "\n" . str_repeat("=", 60) . "\n\n";
    }
    
    // Check for action plans
    echo "📋 CHECKING ACTION PLANS:\n";
    $stmt = $pdo->query("
        SELECT 
            ap.*,
            sv.restaurant_id,
            r.name as restaurant_name
        FROM action_plans ap
        JOIN store_visits sv ON ap.store_visit_id = sv.id
        LEFT JOIN restaurants r ON sv.restaurant_id = r.id
        ORDER BY ap.created_at DESC
        LIMIT 10
    ");
    
    $actionPlans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($actionPlans)) {
        echo "✅ Found " . count($actionPlans) . " action plans\n\n";
        
        foreach ($actionPlans as $plan) {
            echo "📌 Action Plan ID: {$plan['id']}\n";
            echo "🏪 Restaurant: {$plan['restaurant_name']}\n";
            echo "📝 Item: {$plan['item_name']}\n";
            echo "⚠️ Issue: {$plan['description']}\n";
            if ($plan['comment']) {
                echo "💬 Comment: {$plan['comment']}\n";
            }
            echo "🎯 Priority: " . ($plan['priority'] ?: 'Medium') . "\n";
            echo "📅 Due Date: {$plan['due_date']}\n";
            echo "📊 Status: " . ($plan['status'] ?: 'Pending') . "\n\n";
        }
    } else {
        echo "❌ No action plans found\n";
        echo "💡 Action plans are created when visits have 'NO' answers\n\n";
    }
    
    // Summary Statistics
    echo "📈 SUMMARY STATISTICS:\n";
    echo str_repeat("-", 30) . "\n";
    
    // Total visits by status
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM store_visits GROUP BY status");
    $statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📊 Visits by Status:\n";
    foreach ($statusCounts as $status) {
        echo "  • {$status['status']}: {$status['count']}\n";
    }
    
    // Average score
    $stmt = $pdo->query("SELECT AVG(score) as avg_score FROM store_visits WHERE score IS NOT NULL");
    $avgScore = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($avgScore['avg_score']) {
        echo "🎯 Average Score: " . round($avgScore['avg_score'], 1) . "%\n";
    }
    
    // Total action plans
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM action_plans");
    $totalActionPlans = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "📋 Total Action Plans: {$totalActionPlans['count']}\n";
    
    echo "\n✅ Data reading complete!\n";
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "💡 Make sure the database 'qmsnew' exists and contains store visit data\n";
}