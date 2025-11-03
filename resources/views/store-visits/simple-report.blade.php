<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Store Visit Report</title>
<style>
    @page { size: A4; margin: 15mm; }
    body {
        font-family: "Arial", sans-serif;
        font-size: 12px;
        color: #000;
    }
    h1 {
        text-align: center;
        font-size: 18px;
        margin-bottom: 10px;
        text-transform: uppercase;
        border-bottom: 2px solid #000;
        padding-bottom: 5px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }
    th, td {
        border: 1px solid #000;
        padding: 5px;
        vertical-align: top;
    }
    .no-border td {
        border: none;
        padding: 2px 5px;
    }
    .section-title {
        font-weight: bold;
        background-color: #f5f5f5;
        text-transform: uppercase;
        padding: 4px;
    }
    .signature {
        width: 48%;
        display: inline-block;
        text-align: center;
        margin-top: 25px;
    }
    .legend, .purpose {
        width: 49%;
        display: inline-block;
        vertical-align: top;
    }
    .purpose input {
        margin-right: 5px;
    }
    textarea {
        width: 100%;
        border: 1px solid #000;
        min-height: 40px;
        resize: none;
    }
    .page-break {
        page-break-before: always;
    }
    .checkbox {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 1px solid #000;
        margin-right: 5px;
        text-align: center;
        vertical-align: middle;
    }
    .checked {
        background-color: #000;
        color: white;
    }
</style>
</head>
<body>

<h1>STORE VISIT REPORT</h1>

<!-- Header Info -->
<table class="no-border">
    <tr>
        <td><b>Restaurant:</b> {{ $visit->restaurant_name ?? '_____________________' }}</td>
        <td><b>Date:</b> {{ \Carbon\Carbon::parse($visit->visit_date ?? $visit->created_at)->format('M d, Y') }}</td>
    </tr>
    <tr>
        <td><b>MIC:</b> {{ $visit->mic ?? '_____________________' }}</td>
        <td><b>Score:</b> {{ $visit->score ?? '_____________________' }}%</td>
    </tr>
</table>

<!-- Legend & Purpose -->
<div class="legend">
    <b>Legend for Scoring:</b><br>
    A = Store maintain 100% QSC for all aspects.<br>
    B = Store has 2 Minor issues from QSC.<br>
    C = Having Critical Issues From QSC.<br><br>
    <b>Major Issue:</b> (Day Label, Store not Clean, 1 day they forget to fill QSC/TTF)<br>
    <b>Critical Issue:</b> Any type of expired items or no sanitizer.
</div>

<div class="purpose">
    <b>Purpose of Visit:</b><br>
    @php
        $purposes = $visit->purpose_of_visit;
        if (is_string($purposes)) {
            $decoded = json_decode($purposes, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $purposes = $decoded;
            } else {
                $purposes = [$purposes];
            }
        }
        if (!is_array($purposes)) {
            $purposes = [];
        }
    @endphp
    <label><span class="checkbox{{ in_array('Quality Audit', $purposes) ? ' checked' : '' }}">{{ in_array('Quality Audit', $purposes) ? '✓' : '' }}</span> Quality Audit</label><br>
    <label><span class="checkbox{{ in_array('Operational Assessment', $purposes) ? ' checked' : '' }}">{{ in_array('Operational Assessment', $purposes) ? '✓' : '' }}</span> Operational Assessment</label><br>
    <label><span class="checkbox{{ in_array('Training Audit', $purposes) ? ' checked' : '' }}">{{ in_array('Training Audit', $purposes) ? '✓' : '' }}</span> Training Audit</label><br>
    <label><span class="checkbox{{ in_array('Measurement and Coaching', $purposes) ? ' checked' : '' }}">{{ in_array('Measurement and Coaching', $purposes) ? '✓' : '' }}</span> Measurement and Coaching</label>
</div>

<hr style="margin-top:15px; margin-bottom:10px;">

<!-- Customer / QSC Section -->
<div class="section-title">CUSTOMER / QSC</div>
<table>
    <tr><th>In Charge</th><th>Y/N</th><th>QUALITY</th><th>Y/N</th><th>COMMENTS</th></tr>
    <tr>
        <td>OCA Board is Completely Followed/Communicated</td>
        <td>{{ $visit->oca_board_followed === 1 ? 'Y' : ($visit->oca_board_followed === 0 ? 'N' : '') }}</td>
        <td>TTF followed properly</td>
        <td>{{ $visit->ttf_followed === 1 ? 'Y' : ($visit->ttf_followed === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->oca_board_comments ?? $visit->ttf_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>Staff Know their Side Duty</td>
        <td>{{ $visit->staff_know_duty === 1 ? 'Y' : ($visit->staff_know_duty === 0 ? 'N' : '') }}</td>
        <td>QSC completed and followed</td>
        <td>{{ $visit->qsc_completed === 1 ? 'Y' : ($visit->qsc_completed === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->staff_duty_comments ?? $visit->qsc_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>Coaching and Directing</td>
        <td>{{ $visit->coaching_directing === 1 ? 'Y' : ($visit->coaching_directing === 0 ? 'N' : '') }}</td>
        <td></td>
        <td></td>
        <td>{{ $visit->coaching_comments ?? '' }}</td>
    </tr>
</table>

<!-- Quality/Pathing Section -->
<div class="section-title">QUALITY / PATHING</div>
<table>
    <tr><th>QUALITY</th><th>Y/N</th><th>COMMENTS</th></tr>
    <tr>
        <td>Oil/Shortening Meets Standards</td>
        <td>{{ $visit->oil_standards === 1 ? 'Y' : ($visit->oil_standards === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->oil_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>Day Labels updated</td>
        <td>{{ $visit->day_labels === 1 ? 'Y' : ($visit->day_labels === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->labels_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>Cashier Equipment working properly</td>
        <td>{{ $visit->equipment_working === 1 ? 'Y' : ($visit->equipment_working === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->equipment_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>Smile and Friendly Greetings</td>
        <td>{{ $visit->smile_greetings === 1 ? 'Y' : ($visit->smile_greetings === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->smile_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>Suggestive Selling</td>
        <td>{{ $visit->suggestive_selling === 1 ? 'Y' : ($visit->suggestive_selling === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->suggestive_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>Offer new Promotion</td>
        <td>{{ $visit->offer_promotion === 1 ? 'Y' : ($visit->offer_promotion === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->promotion_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>Saying Thank you & Provides Direction</td>
        <td>{{ $visit->thank_direction === 1 ? 'Y' : ($visit->thank_direction === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->thank_comments ?? '' }}</td>
    </tr>
</table>

<!-- Cleanliness & Standards -->
<div class="section-title">CLEANLINESS / SERVICE STANDARDS</div>
<table>
    <tr><th>STANDARDS</th><th>Y/N</th><th>COMMENTS</th></tr>
    <tr>
        <td>Equipment are wrapped and hang</td>
        <td>{{ $visit->equipment_wrapped === 1 ? 'Y' : ($visit->equipment_wrapped === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->wrapped_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>Compartment sink set-up properly</td>
        <td>{{ $visit->sink_setup === 1 ? 'Y' : ($visit->sink_setup === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->sink_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>Sanitizer meets standard</td>
        <td>{{ $visit->sanitizer_standard === 1 ? 'Y' : ($visit->sanitizer_standard === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->sanitizer_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>Dining Area/Family area clean, no busting</td>
        <td>{{ $visit->dining_area_clean === 1 ? 'Y' : ($visit->dining_area_clean === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->dining_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>Dine IN CR/handwash have tissue and clean</td>
        <td>{{ $visit->restroom_clean === 1 ? 'Y' : ($visit->restroom_clean === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->restroom_comments ?? '' }}</td>
    </tr>
</table>

<!-- Follow-Up -->
<div class="section-title">FOLLOW-UP</div>
<b>UPDATE FOR THE LAST VISIT:</b><br>
<div style="border: 1px solid #000; min-height: 40px; padding: 5px;">{{ $visit->last_visit_update ?? '' }}</div>
<br><br>
<b>FINANCIALS / ANY OTHER FOLLOW UP:</b><br>
<div style="border: 1px solid #000; min-height: 40px; padding: 5px;">{{ $visit->other_follow_up ?? '' }}</div>

<!-- Financial & Operations -->
<table style="margin-top: 10px;">
    <tr><th>FINANCIAL / OPERATIONS</th><th>Y/N</th><th>COMMENTS</th></tr>
    <tr>
        <td>Weekly Schedule and Overtime</td>
        <td>{{ $visit->weekly_schedule === 1 ? 'Y' : ($visit->weekly_schedule === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->schedule_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>MOD aware of Financial Goal</td>
        <td>{{ $visit->mod_financial_goal === 1 ? 'Y' : ($visit->mod_financial_goal === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->financial_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>Sales (Cashier Objectives)</td>
        <td>{{ $visit->sales_objectives === 1 ? 'Y' : ($visit->sales_objectives === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->sales_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>Cash Policies followed, Spot Check</td>
        <td>{{ $visit->cash_policies === 1 ? 'Y' : ($visit->cash_policies === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->cash_comments ?? '' }}</td>
    </tr>
    <tr>
        <td>Daily Waste Followed Properly (Daily)</td>
        <td>{{ $visit->daily_waste === 1 ? 'Y' : ($visit->daily_waste === 0 ? 'N' : '') }}</td>
        <td>{{ $visit->waste_comments ?? '' }}</td>
    </tr>
</table>

<!-- Observation -->
<div class="section-title">OBSERVATION / IMPROVEMENT</div>
<table>
    <tr>
        <th>WHAT DID YOU SEE TODAY</th>
        <th>WHY WE HAD ISSUE</th>
        <th>HOW TO IMPROVE</th>
        <th>WHO / WHEN</th>
    </tr>
    <tr>
        <td style="height: 50px;">{{ $visit->what_did_you_see ?? '' }}</td>
        <td style="height: 50px;">{{ $visit->why_had_issue ?? '' }}</td>
        <td style="height: 50px;">{{ $visit->how_to_improve ?? '' }}</td>
        <td style="height: 50px;">{{ $visit->who_when_responsible ?? '' }}</td>
    </tr>
    <tr>
        <td style="height: 50px;"></td>
        <td style="height: 50px;"></td>
        <td style="height: 50px;"></td>
        <td style="height: 50px;"></td>
    </tr>
</table>

@if($visit->general_comments)
<!-- General Comments -->
<div class="section-title">GENERAL COMMENTS</div>
<div style="border: 1px solid #000; min-height: 40px; padding: 5px;">{{ $visit->general_comments }}</div>
@endif

<!-- Signatures -->
<div style="margin-top:20px;">
    <div class="signature">
        @if($visit->digital_signature_reviewer)
            {{ $visit->digital_signature_reviewer }}<br>
        @else
            ___________________________<br>
        @endif
        <b>AREA MANAGER SIGNATURE</b>
    </div>
    <div class="signature" style="float:right;">
        @if($visit->digital_signature_mic)
            {{ $visit->digital_signature_mic }}<br>
        @else
            ___________________________<br>
        @endif
        <b>MIC SIGNATURE</b>
    </div>
</div>

</body>
</html>
        
        .info-label {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 3px;
        }
        
        .info-value {
            font-size: 1rem;
            color: #1f2937;
            font-weight: bold;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 1rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .inspection-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .inspection-table th,
        .inspection-table td {
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #e5e7eb;
            font-size: 11px;
        }
        
        .inspection-table th {
            background: #f3f4f6;
            font-weight: 600;
            color: #374151;
        }
        
        .inspection-table tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .status-pass {
            color: #059669;
            font-weight: bold;
            background: #d1fae5;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            min-width: 40px;
            text-align: center;
        }
        
        .status-fail {
            color: #dc2626;
            font-weight: bold;
            background: #fee2e2;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            min-width: 40px;
            text-align: center;
        }
        
        .status-na {
            color: #6b7280;
            font-style: italic;
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            min-width: 40px;
            text-align: center;
        }
        
        .comments-section {
            background: #f9fafb;
            padding: 12px;
            border-radius: 6px;
            margin-top: 15px;
        }
        
        .comments-title {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        @media print {
            body {
                font-size: 10px;
                padding: 5px;
                line-height: 1.2;
            }
            
            .header {
                margin-bottom: 15px;
                padding-bottom: 10px;
            }
            
            .visit-info {
                margin-bottom: 15px;
                padding: 10px;
            }
            
            .section {
                margin-bottom: 15px;
                break-inside: avoid;
            }
            
            .inspection-table th,
            .inspection-table td {
                padding: 4px 6px;
                font-size: 9px;
            }
            
            .comments-section {
                padding: 8px;
                margin-top: 10px;
            }
        }
        
        @page {
            size: A4;
            margin: 10mm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Store Visit Report</h1>
    </div>
    
    <!-- Visit Information -->
    <div class="visit-info">
        <div class="info-item">
            <div class="info-label">Restaurant</div>
            <div class="info-value">
                @php
                    $restaurantName = $visit->restaurant_id ? 
                        \App\Models\Restaurant::find($visit->restaurant_id)?->name ?? $visit->restaurant_name ?? 'Not Specified' : 
                        ($visit->restaurant_name ?? 'Not Specified');
                @endphp
                {{ $restaurantName }}
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-label">Date</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($visit->visit_date ?? $visit->created_at)->format('M d, Y') }}</div>
        </div>
        
        <div class="info-item">
            <div class="info-label">Shift</div>
            <div class="info-value">{{ $visit->mic ?? 'Not Specified' }}</div>
        </div>
    </div>
    
    <!-- Customer Service Section -->
    <div class="section">
        <div class="section-title">Customer Service</div>
        <table class="inspection-table">
            <thead>
                <tr>
                    <th style="width: 43%;">Item</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 43%;">Comments</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Smile & Greetings</td>
                    <td>
                        @if($visit->smile_greetings === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->smile_greetings === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->smile_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Staff Know Their Duty</td>
                    <td>
                        @if($visit->staff_know_duty === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->staff_know_duty === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->staff_duty_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Coaching & Directing</td>
                    <td>
                        @if($visit->coaching_directing === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->coaching_directing === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->coaching_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Suggestive Selling</td>
                    <td>
                        @if($visit->suggestive_selling === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->suggestive_selling === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->suggestive_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Offer Promotion</td>
                    <td>
                        @if($visit->offer_promotion === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->offer_promotion === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->promotion_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Thank & Direction</td>
                    <td>
                        @if($visit->thank_direction === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->thank_direction === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->thank_comments ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Operations Section -->
    <div class="section">
        <div class="section-title">Operations & Teamwork</div>
        <table class="inspection-table">
            <thead>
                <tr>
                    <th style="width: 43%;">Item</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 43%;">Comments</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>OCA Board Followed</td>
                    <td>
                        @if($visit->oca_board_followed === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->oca_board_followed === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->oca_board_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Team Work & Hustle</td>
                    <td>
                        @if($visit->team_work_hustle === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->team_work_hustle === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->teamwork_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Order Accuracy</td>
                    <td>
                        @if($visit->order_accuracy === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->order_accuracy === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->accuracy_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Service Time</td>
                    <td>
                        @if($visit->service_time === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->service_time === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->service_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Weekly Schedule</td>
                    <td>
                        @if($visit->weekly_schedule === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->weekly_schedule === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->schedule_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>MOD Financial Goal</td>
                    <td>
                        @if($visit->mod_financial_goal === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->mod_financial_goal === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->financial_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Sales Objectives</td>
                    <td>
                        @if($visit->sales_objectives === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->sales_objectives === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->sales_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Cash Policies</td>
                    <td>
                        @if($visit->cash_policies === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->cash_policies === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->cash_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Daily Waste</td>
                    <td>
                        @if($visit->daily_waste === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->daily_waste === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->waste_comments ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Service Areas Section -->
    <div class="section">
        <div class="section-title">Service Areas</div>
        <table class="inspection-table">
            <thead>
                <tr>
                    <th style="width: 43%;">Item</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 43%;">Comments</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Dine In</td>
                    <td>
                        @if($visit->dine_in === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->dine_in === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->dine_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Take Out</td>
                    <td>
                        @if($visit->take_out === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->take_out === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->takeout_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Family</td>
                    <td>
                        @if($visit->family === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->family === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->family_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Delivery</td>
                    <td>
                        @if($visit->delivery === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->delivery === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->delivery_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Drive Thru</td>
                    <td>
                        @if($visit->drive_thru === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->drive_thru === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->drive_comments ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Food Safety Section -->
    <div class="section">
        <div class="section-title">Food Safety & Quality</div>
        <table class="inspection-table">
            <thead>
                <tr>
                    <th style="width: 43%;">Item</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 43%;">Comments</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>TTF Followed</td>
                    <td>
                        @if($visit->ttf_followed === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->ttf_followed === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->ttf_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Sandwich Assembly</td>
                    <td>
                        @if($visit->sandwich_assembly === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->sandwich_assembly === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->assembly_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>QSC Completed</td>
                    <td>
                        @if($visit->qsc_completed === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->qsc_completed === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->qsc_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Oil Standards</td>
                    <td>
                        @if($visit->oil_standards === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->oil_standards === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->oil_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Day Labels</td>
                    <td>
                        @if($visit->day_labels === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->day_labels === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->labels_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Equipment Working</td>
                    <td>
                        @if($visit->equipment_working === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->equipment_working === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->equipment_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Fryer Condition</td>
                    <td>
                        @if($visit->fryer_condition === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->fryer_condition === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->fryer_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Vegetable Prep</td>
                    <td>
                        @if($visit->vegetable_prep === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->vegetable_prep === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->vegetable_comments ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Cleanliness Section -->
    <div class="section">
        <div class="section-title">Cleanliness & Appearance</div>
        <table class="inspection-table">
            <thead>
                <tr>
                    <th style="width: 43%;">Item</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 43%;">Comments</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Employee Appearance</td>
                    <td>
                        @if($visit->employee_appearance === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->employee_appearance === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->appearance_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Equipment Wrapped</td>
                    <td>
                        @if($visit->equipment_wrapped === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->equipment_wrapped === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->wrapped_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Sink Setup</td>
                    <td>
                        @if($visit->sink_setup === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->sink_setup === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->sink_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Sanitizer Standard</td>
                    <td>
                        @if($visit->sanitizer_standard === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->sanitizer_standard === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->sanitizer_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Dining Area Clean</td>
                    <td>
                        @if($visit->dining_area_clean === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->dining_area_clean === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->dining_comments ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Restroom Clean</td>
                    <td>
                        @if($visit->restroom_clean === 1)
                            <span class="status-pass">✓ YES</span>
                        @elseif($visit->restroom_clean === 0)
                            <span class="status-fail">✗ NO</span>
                        @else
                            <span class="status-na">N/A</span>
                        @endif
                    </td>
                    <td>{{ $visit->restroom_comments ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    @if($visit->general_comments)
    <!-- General Comments -->
    <div class="comments-section">
        <div class="comments-title">General Comments</div>
        <p>{{ $visit->general_comments }}</p>
    </div>
    @endif
    
    @if($visit->purpose_of_visit)
    <!-- Purpose of Visit -->
    <div class="comments-section">
        <div class="comments-title">Purpose of Visit</div>
        <p>
            @php
                $purpose = $visit->purpose_of_visit;
                if (is_string($purpose)) {
                    $decoded = json_decode($purpose, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $purpose = $decoded;
                    }
                }
            @endphp
            @if(is_array($purpose))
                {{ implode(', ', $purpose) }}
            @else
                {{ $purpose }}
            @endif
        </p>
    </div>
    @endif
    
    <!-- Footer with signature if available -->
    @if($visit->digital_signature_mic || $visit->digital_signature_reviewer)
    <div style="margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
            @if($visit->digital_signature_mic)
            <div>
                <strong>Inspector Signature:</strong><br>
                {{ $visit->digital_signature_mic }}
            </div>
            @endif
            
            @if($visit->digital_signature_reviewer)
            <div>
                <strong>Reviewer Signature:</strong><br>
                {{ $visit->digital_signature_reviewer }}
            </div>
            @endif
        </div>
    </div>
    @endif
    
</body>
</html>