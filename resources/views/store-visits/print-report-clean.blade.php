<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Visit Report - {{ $visit->restaurant_name ?? 'Unknown' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: white;
            padding: 8px;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding: 12px;
            background: #2c3e50;
            color: white;
            border-radius: 6px;
        }
        
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 11px;
            opacity: 0.9;
        }
        
        .section {
            margin-bottom: 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            overflow: hidden;
            page-break-inside: avoid;
        }
        
        .section-header {
            padding: 8px 12px;
            font-weight: bold;
            font-size: 11px;
            color: white;
            text-transform: uppercase;
        }
        
        .basic-info .section-header { background: #6b7280; }
        .customer-qsc .section-header { background: #16a34a; }
        .cashier .section-header { background: #ca8a04; }
        .service .section-header { background: #2563eb; }
        .hygiene .section-header { background: #db2777; }
        .equipment .section-header { background: #0284c7; }
        
        .section-content {
            padding: 10px;
            background: white;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 8px;
        }
        
        .info-item {
            padding: 6px;
            background: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #e9ecef;
        }
        
        .info-label {
            font-size: 8px;
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        
        .info-value {
            font-size: 10px;
            font-weight: 600;
            color: #333;
        }
        
        .checklist-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }
        
        .checklist-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 6px;
            background: #f8f9fa;
            border-radius: 3px;
            border: 1px solid #e9ecef;
        }
        
        .item-label {
            font-size: 9px;
            font-weight: 500;
            flex: 1;
        }
        
        .status-badge {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            font-weight: bold;
            color: white;
        }
        
        .status-yes { background: #28a745; }
        .status-no { background: #dc3545; }
        .status-na { background: #6c757d; }
        
        .score-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            color: white;
            margin-left: 5px;
        }
        
        .score-excellent { background: #28a745; }
        .score-good { background: #007bff; }
        .score-average { background: #ffc107; color: #333; }
        .score-poor { background: #dc3545; }
        
        .comments-section {
            margin-top: 8px;
            padding: 6px;
            background: #fff3cd;
            border-radius: 4px;
            border: 1px solid #ffeaa7;
        }
        
        .comments-title {
            font-size: 8px;
            font-weight: bold;
            color: #856404;
            margin-bottom: 3px;
        }
        
        .comments-text {
            font-size: 9px;
            color: #856404;
            font-style: italic;
        }
        
        .signature-section {
            margin-top: 15px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .signature-box {
            text-align: center;
            padding: 10px;
            border: 2px dashed #666;
            border-radius: 4px;
        }
        
        .signature-line {
            height: 25px;
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
        }
        
        .signature-label {
            font-size: 9px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
        }
        
        @media print {
            body { 
                padding: 5px; 
                font-size: 10px;
            }
            .section { 
                margin-bottom: 8px; 
                break-inside: avoid;
            }
            .info-grid { 
                grid-template-columns: repeat(4, 1fr);
                gap: 4px;
            }
            .checklist-grid { 
                grid-template-columns: repeat(4, 1fr);
                gap: 4px;
            }
        }
        
        @page {
            size: A4;
            margin: 6mm;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🏪 STORE VISIT REPORT</h1>
            <p>Quality Management System Inspection Report</p>
        </div>
        
        <!-- Basic Information Section -->
        <div class="section basic-info">
            <div class="section-header">📋 Basic Information</div>
            <div class="section-content">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Restaurant</div>
                        <div class="info-value">{{ $visit->restaurant_name ?? 'Unknown' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">MIC</div>
                        <div class="info-value">{{ $visit->mic ?? 'Not specified' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($visit->visit_date ?? $visit->created_at)->format('M d, Y') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Score</div>
                        <div class="info-value">
                            {{ $visit->score ?? 0 }}%
                            @php
                                $score = $visit->score ?? 0;
                                $scoreClass = $score >= 90 ? 'score-excellent' : 
                                             ($score >= 80 ? 'score-good' : 
                                             ($score >= 70 ? 'score-average' : 'score-poor'));
                            @endphp
                            <span class="score-badge {{ $scoreClass }}">
                                @if($score >= 90) Excellent
                                @elseif($score >= 80) Good  
                                @elseif($score >= 70) Average
                                @else Poor
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                
                @if($visit->purpose_of_visit)
                <div class="info-item" style="grid-column: 1 / -1; margin-top: 6px;">
                    <div class="info-label">Purpose of Visit</div>
                    <div class="info-value">
                        @if(is_array($visit->purpose_of_visit))
                            {{ implode(', ', $visit->purpose_of_visit) }}
                        @else
                            {{ $visit->purpose_of_visit }}
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Customer / QSC Section -->
        <div class="section customer-qsc">
            <div class="section-header">👥 Customer / QSC</div>
            <div class="section-content">
                <div class="checklist-grid">
                    <div class="checklist-item">
                        <div class="item-label">OCA Board Followed</div>
                        <div class="status-badge {{ $visit->oca_board_followed === true ? 'status-yes' : ($visit->oca_board_followed === false ? 'status-no' : 'status-na') }}">
                            @if($visit->oca_board_followed === true) Y
                            @elseif($visit->oca_board_followed === false) N  
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Staff Know Duty</div>
                        <div class="status-badge {{ $visit->staff_know_duty === true ? 'status-yes' : ($visit->staff_know_duty === false ? 'status-no' : 'status-na') }}">
                            @if($visit->staff_know_duty === true) Y
                            @elseif($visit->staff_know_duty === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Coaching & Directing</div>
                        <div class="status-badge {{ $visit->coaching_directing === true ? 'status-yes' : ($visit->coaching_directing === false ? 'status-no' : 'status-na') }}">
                            @if($visit->coaching_directing === true) Y
                            @elseif($visit->coaching_directing === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cashier Section -->
        <div class="section cashier">
            <div class="section-header">💰 Cashier</div>
            <div class="section-content">
                <div class="checklist-grid">
                    <div class="checklist-item">
                        <div class="item-label">Smile & Greetings</div>
                        <div class="status-badge {{ $visit->smile_greetings === true ? 'status-yes' : ($visit->smile_greetings === false ? 'status-no' : 'status-na') }}">
                            @if($visit->smile_greetings === true) Y
                            @elseif($visit->smile_greetings === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Suggestive Selling</div>
                        <div class="status-badge {{ $visit->suggestive_selling === true ? 'status-yes' : ($visit->suggestive_selling === false ? 'status-no' : 'status-na') }}">
                            @if($visit->suggestive_selling === true) Y
                            @elseif($visit->suggestive_selling === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Offer Promotion</div>
                        <div class="status-badge {{ $visit->offer_promotion === true ? 'status-yes' : ($visit->offer_promotion === false ? 'status-no' : 'status-na') }}">
                            @if($visit->offer_promotion === true) Y
                            @elseif($visit->offer_promotion === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Thank & Direction</div>
                        <div class="status-badge {{ $visit->thank_direction === true ? 'status-yes' : ($visit->thank_direction === false ? 'status-no' : 'status-na') }}">
                            @if($visit->thank_direction === true) Y
                            @elseif($visit->thank_direction === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Service Standards Section -->
        <div class="section service">
            <div class="section-header">⚡ Service Standards</div>
            <div class="section-content">
                <div class="checklist-grid">
                    <div class="checklist-item">
                        <div class="item-label">Team Work & Hustle</div>
                        <div class="status-badge {{ $visit->team_work_hustle === true ? 'status-yes' : ($visit->team_work_hustle === false ? 'status-no' : 'status-na') }}">
                            @if($visit->team_work_hustle === true) Y
                            @elseif($visit->team_work_hustle === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Order Accuracy</div>
                        <div class="status-badge {{ $visit->order_accuracy === true ? 'status-yes' : ($visit->order_accuracy === false ? 'status-no' : 'status-na') }}">
                            @if($visit->order_accuracy === true) Y
                            @elseif($visit->order_accuracy === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Service Time</div>
                        <div class="status-badge {{ $visit->service_time === true ? 'status-yes' : ($visit->service_time === false ? 'status-no' : 'status-na') }}">
                            @if($visit->service_time === true) Y
                            @elseif($visit->service_time === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Dine In</div>
                        <div class="status-badge {{ $visit->dine_in === true ? 'status-yes' : ($visit->dine_in === false ? 'status-no' : 'status-na') }}">
                            @if($visit->dine_in === true) Y
                            @elseif($visit->dine_in === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Take Out</div>
                        <div class="status-badge {{ $visit->take_out === true ? 'status-yes' : ($visit->take_out === false ? 'status-no' : 'status-na') }}">
                            @if($visit->take_out === true) Y
                            @elseif($visit->take_out === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Family</div>
                        <div class="status-badge {{ $visit->family === true ? 'status-yes' : ($visit->family === false ? 'status-no' : 'status-na') }}">
                            @if($visit->family === true) Y
                            @elseif($visit->family === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Delivery</div>
                        <div class="status-badge {{ $visit->delivery === true ? 'status-yes' : ($visit->delivery === false ? 'status-no' : 'status-na') }}">
                            @if($visit->delivery === true) Y
                            @elseif($visit->delivery === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Drive Thru</div>
                        <div class="status-badge {{ $visit->drive_thru === true ? 'status-yes' : ($visit->drive_thru === false ? 'status-no' : 'status-na') }}">
                            @if($visit->drive_thru === true) Y
                            @elseif($visit->drive_thru === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Food Safety & Hygiene Section -->
        <div class="section hygiene">
            <div class="section-header">🛡️ Food Safety & Hygiene</div>
            <div class="section-content">
                <div class="checklist-grid">
                    <div class="checklist-item">
                        <div class="item-label">TTF Followed</div>
                        <div class="status-badge {{ $visit->ttf_followed === true ? 'status-yes' : ($visit->ttf_followed === false ? 'status-no' : 'status-na') }}">
                            @if($visit->ttf_followed === true) Y
                            @elseif($visit->ttf_followed === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Sandwich Assembly</div>
                        <div class="status-badge {{ $visit->sandwich_assembly === true ? 'status-yes' : ($visit->sandwich_assembly === false ? 'status-no' : 'status-na') }}">
                            @if($visit->sandwich_assembly === true) Y
                            @elseif($visit->sandwich_assembly === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">QSC Completed</div>
                        <div class="status-badge {{ $visit->qsc_completed === true ? 'status-yes' : ($visit->qsc_completed === false ? 'status-no' : 'status-na') }}">
                            @if($visit->qsc_completed === true) Y
                            @elseif($visit->qsc_completed === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Oil Standards</div>
                        <div class="status-badge {{ $visit->oil_standards === true ? 'status-yes' : ($visit->oil_standards === false ? 'status-no' : 'status-na') }}">
                            @if($visit->oil_standards === true) Y
                            @elseif($visit->oil_standards === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Day Labels</div>
                        <div class="status-badge {{ $visit->day_labels === true ? 'status-yes' : ($visit->day_labels === false ? 'status-no' : 'status-na') }}">
                            @if($visit->day_labels === true) Y
                            @elseif($visit->day_labels === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Employee Appearance</div>
                        <div class="status-badge {{ $visit->employee_appearance === true ? 'status-yes' : ($visit->employee_appearance === false ? 'status-no' : 'status-na') }}">
                            @if($visit->employee_appearance === true) Y
                            @elseif($visit->employee_appearance === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Equipment & Facilities Section -->
        <div class="section equipment">
            <div class="section-header">⚙️ Equipment & Cleanliness</div>
            <div class="section-content">
                <div class="checklist-grid">
                    <div class="checklist-item">
                        <div class="item-label">Equipment Working</div>
                        <div class="status-badge {{ $visit->equipment_working === true ? 'status-yes' : ($visit->equipment_working === false ? 'status-no' : 'status-na') }}">
                            @if($visit->equipment_working === true) Y
                            @elseif($visit->equipment_working === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Fryer Condition</div>
                        <div class="status-badge {{ $visit->fryer_condition === true ? 'status-yes' : ($visit->fryer_condition === false ? 'status-no' : 'status-na') }}">
                            @if($visit->fryer_condition === true) Y
                            @elseif($visit->fryer_condition === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Vegetable Prep</div>
                        <div class="status-badge {{ $visit->vegetable_prep === true ? 'status-yes' : ($visit->vegetable_prep === false ? 'status-no' : 'status-na') }}">
                            @if($visit->vegetable_prep === true) Y
                            @elseif($visit->vegetable_prep === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Equipment Wrapped</div>
                        <div class="status-badge {{ $visit->equipment_wrapped === true ? 'status-yes' : ($visit->equipment_wrapped === false ? 'status-no' : 'status-na') }}">
                            @if($visit->equipment_wrapped === true) Y
                            @elseif($visit->equipment_wrapped === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Sink Setup</div>
                        <div class="status-badge {{ $visit->sink_setup === true ? 'status-yes' : ($visit->sink_setup === false ? 'status-no' : 'status-na') }}">
                            @if($visit->sink_setup === true) Y
                            @elseif($visit->sink_setup === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Sanitizer Standard</div>
                        <div class="status-badge {{ $visit->sanitizer_standard === true ? 'status-yes' : ($visit->sanitizer_standard === false ? 'status-no' : 'status-na') }}">
                            @if($visit->sanitizer_standard === true) Y
                            @elseif($visit->sanitizer_standard === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Dining Area Clean</div>
                        <div class="status-badge {{ $visit->dining_area_clean === true ? 'status-yes' : ($visit->dining_area_clean === false ? 'status-no' : 'status-na') }}">
                            @if($visit->dining_area_clean === true) Y
                            @elseif($visit->dining_area_clean === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="item-label">Restroom Clean</div>
                        <div class="status-badge {{ $visit->restroom_clean === true ? 'status-yes' : ($visit->restroom_clean === false ? 'status-no' : 'status-na') }}">
                            @if($visit->restroom_clean === true) Y
                            @elseif($visit->restroom_clean === false) N
                            @else —
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Comments Section -->
        @if($visit->general_comments)
        <div class="comments-section">
            <div class="comments-title">💬 General Comments</div>
            <div class="comments-text">{{ $visit->general_comments }}</div>
        </div>
        @endif
        
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">MIC Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Area Manager Signature</div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>