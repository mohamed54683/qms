<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QSC Checklist Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header h2 {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 16px;
            font-weight: normal;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-item {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        .info-item strong {
            display: block;
            color: #333;
            margin-bottom: 5px;
        }
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin: 20px 0;
        }
        .stat-card {
            text-align: center;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
        }
        .category {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .category-header {
            background-color: #f5f5f5;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            font-size: 14px;
        }
        .checklist-table {
            width: 100%;
            border-collapse: collapse;
        }
        .checklist-table th,
        .checklist-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .checklist-table th {
            background-color: #f9f9f9;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }
        .checklist-table .item-cell {
            width: 35%;
            font-size: 11px;
        }
        .checklist-table .time-cell {
            width: 16.25%;
            text-align: center;
            font-size: 10px;
        }
        .answer {
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px;
        }
        .answer-yes {
            background-color: #d4edda;
            color: #155724;
        }
        .answer-no {
            background-color: #f8d7da;
            color: #721c24;
        }
        .answer-na {
            background-color: #e2e3e5;
            color: #383d41;
        }
        .answer-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .remarks {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 3px solid #007bff;
            font-size: 11px;
        }
        .action-items {
            margin-top: 20px;
            border: 2px solid #dc3545;
            border-radius: 5px;
        }
        .action-items-header {
            background-color: #dc3545;
            color: white;
            padding: 10px;
            font-weight: bold;
        }
        .action-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        .action-item:last-child {
            border-bottom: none;
        }
        .signature-section {
            margin-top: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>🔍 Q.S.C CHECKLIST REPORT</h1>
        <h2>Quality, Service & Cleanliness Inspection</h2>
    </div>

    <!-- General Information -->
    <div class="info-grid">
        <div class="info-item">
            <strong>Store Name:</strong>
            {{ $checklist->store_name }}
        </div>
        <div class="info-item">
            <strong>Date:</strong>
            {{ $checklist->checklist_date->format('M d, Y') }}
        </div>
        <div class="info-item">
            <strong>Day:</strong>
            {{ $checklist->day }}
        </div>
        <div class="info-item">
            <strong>Manager on Duty:</strong>
            {{ $checklist->mod_name }}
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-stats">
        <div class="stat-card">
            <div class="stat-value" style="color: #28a745;">{{ $summaryStats['Yes'] }}</div>
            <div class="stat-label">✅ Yes Answers</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #dc3545;">{{ $summaryStats['No'] }}</div>
            <div class="stat-label">❌ No Answers</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #6c757d;">{{ $summaryStats['N/A'] }}</div>
            <div class="stat-label">➖ N/A Answers</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #ffc107;">{{ $summaryStats['Pending'] }}</div>
            <div class="stat-label">⏳ Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #007bff;">{{ round((($summaryStats['Yes'] + $summaryStats['No'] + $summaryStats['N/A']) / ($summaryStats['Yes'] + $summaryStats['No'] + $summaryStats['N/A'] + $summaryStats['Pending'])) * 100, 1) }}%</div>
            <div class="stat-label">📈 Completion</div>
        </div>
    </div>

    <!-- Instructions -->
    <div style="background-color: #e3f2fd; padding: 15px; margin: 20px 0; border-radius: 5px; border-left: 4px solid #2196f3;">
        <strong>Instructions:</strong> QSC path checking must be done every 3 hours per shift. Always follow the 7 steps of customer service and ensure cheerful service.
    </div>

    <!-- Checklist Categories -->
    @foreach($categories as $categoryKey => $category)
        <div class="category">
            <div class="category-header">{{ $category['title'] }}</div>
            
            <table class="checklist-table">
                <thead>
                    <tr>
                        <th class="item-cell">Item</th>
                        @foreach($timeSlots as $timeKey => $timeLabel)
                            <th class="time-cell">{{ $timeLabel }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($category['questions'] as $questionKey => $questionText)
                        <tr>
                            <td class="item-cell">{{ $questionText }}</td>
                            @foreach($timeSlots as $timeKey => $timeLabel)
                                @php
                                    $fieldName = $questionKey . '_' . $timeKey;
                                    $value = $checklist->$fieldName;
                                    $answerClass = '';
                                    $answerDisplay = '';
                                    
                                    switch($value) {
                                        case 'Yes':
                                            $answerClass = 'answer-yes';
                                            $answerDisplay = '✅ Yes';
                                            break;
                                        case 'No':
                                            $answerClass = 'answer-no';
                                            $answerDisplay = '❌ No';
                                            break;
                                        case 'N/A':
                                            $answerClass = 'answer-na';
                                            $answerDisplay = '➖ N/A';
                                            break;
                                        default:
                                            $answerClass = 'answer-pending';
                                            $answerDisplay = '⏳ Pending';
                                    }
                                @endphp
                                <td class="time-cell">
                                    <span class="answer {{ $answerClass }}">{{ $answerDisplay }}</span>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($checklist->{$categoryKey . '_remarks'})
                <div class="remarks">
                    <strong>{{ $category['title'] }} - Remarks:</strong><br>
                    {{ $checklist->{$categoryKey . '_remarks'} }}
                </div>
            @endif
        </div>
    @endforeach

    <!-- Action Items (if any issues found) -->
    @php
        $actionItems = [];
        foreach($categories as $categoryKey => $category) {
            foreach($category['questions'] as $questionKey => $questionText) {
                foreach($timeSlots as $timeKey => $timeLabel) {
                    $fieldName = $questionKey . '_' . $timeKey;
                    if($checklist->$fieldName === 'No') {
                        $actionItems[] = [
                            'category' => $category['title'],
                            'question' => $questionText,
                            'time_slot' => $timeLabel,
                        ];
                    }
                }
            }
        }
    @endphp

    @if(count($actionItems) > 0)
        <div class="action-items">
            <div class="action-items-header">
                🚨 ISSUES REQUIRING ACTION ({{ count($actionItems) }} Total)
            </div>
            @foreach($actionItems as $index => $item)
                <div class="action-item">
                    <strong>{{ $index + 1 }}. {{ $item['category'] }}</strong><br>
                    Issue: {{ $item['question'] }}<br>
                    Time Slot: {{ $item['time_slot'] }}
                </div>
            @endforeach
        </div>
    @endif

    <!-- Manager Signature -->
    @if($checklist->manager_signature || $checklist->approved_at)
        <div class="signature-section">
            <h3 style="margin: 0 0 10px 0;">👤 Manager Approval</h3>
            @if($checklist->manager_signature)
                <p><strong>Signature/Approval:</strong> {{ $checklist->manager_signature }}</p>
            @endif
            @if($checklist->approved_at)
                <p><strong>Approved Date:</strong> {{ $checklist->approved_at->format('M d, Y H:i') }}</p>
            @endif
            <p><strong>Status:</strong> 
                <span class="answer 
                    @if($checklist->status === 'Approved') answer-yes
                    @elseif($checklist->status === 'Submitted') answer-pending
                    @else answer-na @endif">
                    {{ $checklist->status }}
                </span>
            </p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Generated on {{ now()->format('M d, Y H:i') }} | QMS System | Created by {{ $checklist->user->name }}</p>
        <p>QSC Checklist ID: {{ $checklist->id }} | {{ $checklist->store_name }}</p>
    </div>
</body>
</html>