<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Store Visit Report - {{ $visit->restaurant_name }}</title>
    <style>
        @page {
            margin: 1.5cm 1cm;
            size: A4;
        }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #2d3748;
            margin: 0;
            padding: 0;
        }
        
        /* Enhanced Header Styling */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding: 20px;
            background: linear-gradient(135deg, #1e40af 0%, #7c3aed 100%);
            color: white;
            border-radius: 10px;
            position: relative;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #fbbf24, #ef4444, #06b6d4);
        }
        .header h1 {
            margin: 0 0 8px 0;
            font-size: 24px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            letter-spacing: 0.5px;
        }
        .header h2 {
            margin: 0 0 12px 0;
            font-size: 18px;
            font-weight: 600;
            opacity: 0.95;
        }
        .header p {
            margin: 3px 0;
            font-size: 11px;
            opacity: 0.9;
        }
        
        /* Company Branding */
        .logo-section {
            position: absolute;
            top: 15px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            border: 3px solid rgba(255,255,255,0.3);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        /* Enhanced Info Grid */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .info-row {
            display: table-row;
        }
        .info-box {
            display: table-cell;
            background: #ffffff;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            width: 33.33%;
            position: relative;
        }
        .info-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        }
        .info-label {
            font-weight: 700;
            color: #64748b;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            display: block;
        }
        .info-value {
            font-size: 12px;
            font-weight: 600;
            color: #1e293b;
        }
        
        /* Section Containers */
        .section {
            margin-bottom: 20px;
            break-inside: avoid;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
        }
        .section-header {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            padding: 10px 15px;
            font-weight: 700;
            font-size: 11px;
            display: flex;
            align-items: center;
        }
        .section-header .emoji {
            margin-right: 8px;
            font-size: 14px;
        }
        .section-content {
            padding: 15px;
            background: #fafafa;
        }
        
        /* Question Items */
        .question-item {
            margin-bottom: 12px;
            padding: 12px;
            background: #ffffff;
            border-radius: 6px;
            border-left: 3px solid #cbd5e1;
        }
        .question-item:last-child {
            margin-bottom: 0;
        }
        .question-item.has-issue {
            border-left-color: #ef4444;
            background: #fef2f2;
        }
        .question-item.no-issue {
            border-left-color: #10b981;
            background: #f0fdf4;
        }
        .question-text {
            font-weight: 600;
            margin-bottom: 6px;
            color: #1e293b;
            font-size: 10px;
        }
        
        /* Answer Badges */
        .answer {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .answer-yes {
            background: #10b981;
            color: white;
        }
        .answer-no {
            background: #ef4444;
            color: white;
        }
        .answer-null {
            background: #94a3b8;
            color: white;
        }
        
        /* Comment Boxes */
        .comment {
            margin-top: 8px;
            padding: 10px;
            background: #fffbeb;
            border-left: 3px solid #fbbf24;
            border-radius: 4px;
            font-size: 9px;
        }
        .comment-text {
            color: #78350f;
            line-height: 1.5;
        }
        
        /* Score Display */
        .score-display {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 2px solid #e5e7eb;
        }
        .score-number {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .score-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .score-excellent { 
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46; 
        }
        .score-good { 
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
        }
        .score-poor { 
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
        }
        
        /* Text Areas */
        .text-area {
            background: #ffffff;
            padding: 12px;
            border-radius: 6px;
            border-left: 3px solid #3b82f6;
            margin-bottom: 12px;
            font-size: 10px;
            line-height: 1.6;
        }
        .text-area strong {
            color: #1e293b;
            display: block;
            margin-bottom: 6px;
            font-size: 10px;
            font-weight: 700;
        }
        
        /* Action Items */
        .action-items {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border: 2px solid #ef4444;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .action-items h3 {
            margin: 0 0 12px 0;
            color: #991b1b;
            font-size: 13px;
            font-weight: 700;
        }
        .action-item {
            background: #ffffff;
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 6px;
            border-left: 3px solid #ef4444;
            font-size: 9px;
        }
        .action-item:last-child {
            margin-bottom: 0;
        }
        
        /* Success Message */
        .success-message {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #10b981;
            font-weight: 600;
        }
        
        /* Inspection Table Styles */
        .inspection-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .inspection-table thead {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
        }
        .inspection-table th {
            padding: 12px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 3px solid #1e40af;
        }
        .inspection-table th:nth-child(1) { width: 50%; } /* Issue column */
        .inspection-table th:nth-child(2) { width: 15%; text-align: center; } /* Yes/No column */
        .inspection-table th:nth-child(3) { width: 35%; } /* Comment column */
        
        .inspection-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: background 0.2s;
        }
        .inspection-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .inspection-table tbody tr:hover {
            background: #f1f5f9;
        }
        .inspection-table tbody tr.has-issue {
            background: #fef2f2 !important;
            border-left: 4px solid #ef4444;
        }
        .inspection-table tbody tr.no-issue {
            background: #f0fdf4 !important;
            border-left: 4px solid #10b981;
        }
        
        .inspection-table td {
            padding: 10px;
            font-size: 9px;
            vertical-align: top;
            line-height: 1.5;
        }
        .inspection-table td:nth-child(2) {
            text-align: center;
            font-weight: 700;
            font-size: 10px;
        }
        
        .table-answer-yes {
            color: #059669;
            background: #d1fae5;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
            font-weight: 700;
        }
        .table-answer-no {
            color: #dc2626;
            background: #fee2e2;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
            font-weight: 700;
        }
        .table-answer-na {
            color: #64748b;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
            font-weight: 700;
        }
        
        .table-comment {
            color: #475569;
            font-style: italic;
            margin-top: 5px;
            padding: 6px;
            background: #f8fafc;
            border-left: 3px solid #94a3b8;
            border-radius: 3px;
            font-size: 8px;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #64748b;
            border-top: 2px solid #e2e8f0;
            padding-top: 15px;
            background: #f8fafc;
            border-radius: 8px;
            padding: 12px;
        }
        .footer p {
            margin: 3px 0;
        }
        
        /* Page Number */
        .page-number {
            position: fixed;
            bottom: 10px;
            right: 20px;
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">🏪</div>
        <h1>Store Visit Report</h1>
        <h2>{{ $visit->restaurant_name }}</h2>
        <p>Visit Date: {{ $visit->visit_date->format('F j, Y') }} | MIC Shift: {{ $visit->mic }}</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <!-- General Information -->
    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Restaurant</div>
            <div class="info-value">{{ $visit->restaurant_name }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">MIC Shift</div>
            <div class="info-value">{{ $visit->mic }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Visit Date</div>
            <div class="info-value">{{ $visit->visit_date->format('F j, Y') }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Status</div>
            <div class="info-value">{{ $visit->status }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Created By</div>
            <div class="info-value">{{ $visit->user->name ?? 'N/A' }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Created At</div>
            <div class="info-value">{{ $visit->created_at->format('F j, Y g:i A') }}</div>
        </div>
    </div>

    <!-- Purpose of Visit -->
    <div class="section">
        <div class="section-header">
            <span class="emoji">📋</span>
            Purpose of Visit
        </div>
        <div class="section-content">
            <div class="text-area">
                @if(is_array($visit->purpose_of_visit))
                    {{ implode(', ', $visit->purpose_of_visit) }}
                @else
                    {{ $visit->purpose_of_visit }}
                @endif
            </div>
        </div>
    </div>

    <!-- Overall Score -->
    @php $score = $visit->score ?? $visit->calculateScore(); @endphp
    <div class="score-display {{ $score >= 90 ? 'score-excellent' : ($score >= 70 ? 'score-good' : 'score-poor') }}">
        <div class="score-number">{{ $score }}%</div>
        <div class="score-label">Overall Performance Score</div>
    </div>

    <!-- Inspection Checklist Table -->
    <div class="section">
        <div class="section-header">
            <span class="emoji">�</span>
            Store Inspection Checklist
        </div>
        <table class="inspection-table">
            <thead>
                <tr>
                    <th>Inspection Item</th>
                    <th>Status</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                <!-- Section A: Customer / QSC -->
                <tr class="{{ $visit->oca_board_followed === false ? 'has-issue' : ($visit->oca_board_followed === true ? 'no-issue' : '') }}">
                    <td><strong>👥 Section A – Customer / QSC</strong><br>OCA Board is Completely Followed/Communicated</td>
                    <td>
                        <span class="{{ $visit->oca_board_followed === true ? 'table-answer-yes' : ($visit->oca_board_followed === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->oca_board_followed === true ? '✓ YES' : ($visit->oca_board_followed === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->oca_board_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->staff_know_duty === false ? 'has-issue' : ($visit->staff_know_duty === true ? 'no-issue' : '') }}">
                    <td>Staff Know their Side Duty</td>
                    <td>
                        <span class="{{ $visit->staff_know_duty === true ? 'table-answer-yes' : ($visit->staff_know_duty === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->staff_know_duty === true ? '✓ YES' : ($visit->staff_know_duty === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->staff_duty_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->coaching_directing === false ? 'has-issue' : ($visit->coaching_directing === true ? 'no-issue' : '') }}">
                    <td>Coaching and Directing</td>
                    <td>
                        <span class="{{ $visit->coaching_directing === true ? 'table-answer-yes' : ($visit->coaching_directing === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->coaching_directing === true ? '✓ YES' : ($visit->coaching_directing === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->coaching_comments ?? '-' }}</td>
                </tr>

                <!-- Section B: Cashier -->
                <tr class="{{ $visit->smile_greetings === false ? 'has-issue' : ($visit->smile_greetings === true ? 'no-issue' : '') }}">
                    <td><strong>💰 Section B – Cashier</strong><br>Smile & Greetings</td>
                    <td>
                        <span class="{{ $visit->smile_greetings === true ? 'table-answer-yes' : ($visit->smile_greetings === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->smile_greetings === true ? '✓ YES' : ($visit->smile_greetings === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->smile_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->suggestive_selling === false ? 'has-issue' : ($visit->suggestive_selling === true ? 'no-issue' : '') }}">
                    <td>Suggestive Selling</td>
                    <td>
                        <span class="{{ $visit->suggestive_selling === true ? 'table-answer-yes' : ($visit->suggestive_selling === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->suggestive_selling === true ? '✓ YES' : ($visit->suggestive_selling === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->suggestive_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->offer_promotion === false ? 'has-issue' : ($visit->offer_promotion === true ? 'no-issue' : '') }}">
                    <td>Offer Promotion</td>
                    <td>
                        <span class="{{ $visit->offer_promotion === true ? 'table-answer-yes' : ($visit->offer_promotion === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->offer_promotion === true ? '✓ YES' : ($visit->offer_promotion === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->promotion_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->thank_direction === false ? 'has-issue' : ($visit->thank_direction === true ? 'no-issue' : '') }}">
                    <td>Thank & Direction</td>
                    <td>
                        <span class="{{ $visit->thank_direction === true ? 'table-answer-yes' : ($visit->thank_direction === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->thank_direction === true ? '✓ YES' : ($visit->thank_direction === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->thank_comments ?? '-' }}</td>
                </tr>

                <!-- Section C: Service Standards -->
                <tr class="{{ $visit->team_work_hustle === false ? 'has-issue' : ($visit->team_work_hustle === true ? 'no-issue' : '') }}">
                    <td><strong>🚀 Section C – Service Standards</strong><br>Team Work & Hustle</td>
                    <td>
                        <span class="{{ $visit->team_work_hustle === true ? 'table-answer-yes' : ($visit->team_work_hustle === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->team_work_hustle === true ? '✓ YES' : ($visit->team_work_hustle === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->teamwork_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->order_accuracy === false ? 'has-issue' : ($visit->order_accuracy === true ? 'no-issue' : '') }}">
                    <td>Order Accuracy</td>
                    <td>
                        <span class="{{ $visit->order_accuracy === true ? 'table-answer-yes' : ($visit->order_accuracy === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->order_accuracy === true ? '✓ YES' : ($visit->order_accuracy === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->accuracy_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->service_time === false ? 'has-issue' : ($visit->service_time === true ? 'no-issue' : '') }}">
                    <td>Service Time</td>
                    <td>
                        <span class="{{ $visit->service_time === true ? 'table-answer-yes' : ($visit->service_time === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->service_time === true ? '✓ YES' : ($visit->service_time === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->service_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->dine_in === false ? 'has-issue' : ($visit->dine_in === true ? 'no-issue' : '') }}">
                    <td>Dine-In Experience</td>
                    <td>
                        <span class="{{ $visit->dine_in === true ? 'table-answer-yes' : ($visit->dine_in === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->dine_in === true ? '✓ YES' : ($visit->dine_in === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->dine_comments ?? '-' }}</td>
                </tr>

                <!-- Section D: Financials -->
                <tr class="{{ $visit->weekly_schedule === false ? 'has-issue' : ($visit->weekly_schedule === true ? 'no-issue' : '') }}">
                    <td><strong>💵 Section D – Financials</strong><br>Weekly Schedule</td>
                    <td>
                        <span class="{{ $visit->weekly_schedule === true ? 'table-answer-yes' : ($visit->weekly_schedule === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->weekly_schedule === true ? '✓ YES' : ($visit->weekly_schedule === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->schedule_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->mod_financial_goal === false ? 'has-issue' : ($visit->mod_financial_goal === true ? 'no-issue' : '') }}">
                    <td>MOD Financial Goal</td>
                    <td>
                        <span class="{{ $visit->mod_financial_goal === true ? 'table-answer-yes' : ($visit->mod_financial_goal === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->mod_financial_goal === true ? '✓ YES' : ($visit->mod_financial_goal === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->financial_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->cash_policies === false ? 'has-issue' : ($visit->cash_policies === true ? 'no-issue' : '') }}">
                    <td>Cash Policies</td>
                    <td>
                        <span class="{{ $visit->cash_policies === true ? 'table-answer-yes' : ($visit->cash_policies === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->cash_policies === true ? '✓ YES' : ($visit->cash_policies === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->cash_comments ?? '-' }}</td>
                </tr>

                <!-- Section E: Quality / Pathing -->
                <tr class="{{ $visit->ttf_followed === false ? 'has-issue' : ($visit->ttf_followed === true ? 'no-issue' : '') }}">
                    <td><strong>⭐ Section E – Quality / Pathing</strong><br>TTF Followed</td>
                    <td>
                        <span class="{{ $visit->ttf_followed === true ? 'table-answer-yes' : ($visit->ttf_followed === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->ttf_followed === true ? '✓ YES' : ($visit->ttf_followed === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->ttf_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->sandwich_assembly === false ? 'has-issue' : ($visit->sandwich_assembly === true ? 'no-issue' : '') }}">
                    <td>Sandwich Assembly</td>
                    <td>
                        <span class="{{ $visit->sandwich_assembly === true ? 'table-answer-yes' : ($visit->sandwich_assembly === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->sandwich_assembly === true ? '✓ YES' : ($visit->sandwich_assembly === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->assembly_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->qsc_completed === false ? 'has-issue' : ($visit->qsc_completed === true ? 'no-issue' : '') }}">
                    <td>QSC Completed</td>
                    <td>
                        <span class="{{ $visit->qsc_completed === true ? 'table-answer-yes' : ($visit->qsc_completed === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->qsc_completed === true ? '✓ YES' : ($visit->qsc_completed === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->qsc_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->employee_appearance === false ? 'has-issue' : ($visit->employee_appearance === true ? 'no-issue' : '') }}">
                    <td>Employee Appearance</td>
                    <td>
                        <span class="{{ $visit->employee_appearance === true ? 'table-answer-yes' : ($visit->employee_appearance === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->employee_appearance === true ? '✓ YES' : ($visit->employee_appearance === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->appearance_comments ?? '-' }}</td>
                </tr>

                <!-- Section F: Cleanliness -->
                <tr class="{{ $visit->equipment_wrapped === false ? 'has-issue' : ($visit->equipment_wrapped === true ? 'no-issue' : '') }}">
                    <td><strong>🧹 Section F – Cleanliness</strong><br>Equipment Wrapped</td>
                    <td>
                        <span class="{{ $visit->equipment_wrapped === true ? 'table-answer-yes' : ($visit->equipment_wrapped === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->equipment_wrapped === true ? '✓ YES' : ($visit->equipment_wrapped === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->wrapped_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->sink_setup === false ? 'has-issue' : ($visit->sink_setup === true ? 'no-issue' : '') }}">
                    <td>Sink Setup</td>
                    <td>
                        <span class="{{ $visit->sink_setup === true ? 'table-answer-yes' : ($visit->sink_setup === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->sink_setup === true ? '✓ YES' : ($visit->sink_setup === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->sink_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->dining_area_clean === false ? 'has-issue' : ($visit->dining_area_clean === true ? 'no-issue' : '') }}">
                    <td>Dining Area Clean</td>
                    <td>
                        <span class="{{ $visit->dining_area_clean === true ? 'table-answer-yes' : ($visit->dining_area_clean === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->dining_area_clean === true ? '✓ YES' : ($visit->dining_area_clean === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->dining_comments ?? '-' }}</td>
                </tr>
                <tr class="{{ $visit->restroom_clean === false ? 'has-issue' : ($visit->restroom_clean === true ? 'no-issue' : '') }}">
                    <td>Restroom Clean</td>
                    <td>
                        <span class="{{ $visit->restroom_clean === true ? 'table-answer-yes' : ($visit->restroom_clean === false ? 'table-answer-no' : 'table-answer-na') }}">
                            {{ $visit->restroom_clean === true ? '✓ YES' : ($visit->restroom_clean === false ? '✗ NO' : 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $visit->restroom_comments ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Follow-Up Section -->
    <div class="section">
        <div class="section-header">
            <span class="emoji">📅</span>
            Section 8 – Follow-Up
        </div>
        <div class="section-content">
            @if($visit->last_visit_date)
                <p><strong>Last Visit Date:</strong> {{ \Carbon\Carbon::parse($visit->last_visit_date)->format('F j, Y') }}</p>
            @endif
            
            @if($visit->last_visit_summary)
                <div class="text-area">
                    <strong>Last Visit Summary:</strong><br>
                    {{ $visit->last_visit_summary }}
                </div>
            @endif
            
            @if($visit->last_visit_update)
                <div class="text-area">
                    <strong>Updates on Previous Issues:</strong><br>
                    {{ $visit->last_visit_update }}
                </div>
            @endif
            
            @if($visit->other_follow_up)
                <div class="text-area">
                    <strong>Other Follow-up Items:</strong><br>
                    {{ $visit->other_follow_up }}
                </div>
            @endif
        </div>
    </div>

    <!-- Observation Summary -->
    <div class="section">
        <div class="section-header">
            <span class="emoji">📊</span>
            Section 9 – Observation Summary
        </div>
        <div class="section-content">
            @if($visit->what_did_you_see)
                <div class="text-area">
                    <strong>What did you see?</strong><br>
                    {{ $visit->what_did_you_see }}
                </div>
            @endif
            
            @if($visit->why_had_issue)
                <div class="text-area">
                    <strong>Why did it happen?</strong><br>
                    {{ $visit->why_had_issue }}
                </div>
            @endif
            
            @if($visit->how_to_improve)
                <div class="text-area">
                    <strong>How to improve?</strong><br>
                    {{ $visit->how_to_improve }}
                </div>
            @endif
            
            @if($visit->who_when_responsible)
                <div class="text-area">
                    <strong>Who & When?</strong><br>
                    {{ $visit->who_when_responsible }}
                </div>
            @endif
        </div>
    </div>

    <!-- General Comments -->
    @if($visit->general_comments)
    <div class="section">
        <div class="section-header">
            <span class="emoji">💬</span>
            Additional Comments
        </div>
        <div class="section-content">
            <div class="text-area">
                <strong>General Comments:</strong>
                {{ $visit->general_comments }}
            </div>
        </div>
    </div>
    @endif

    <!-- Action Items -->
    @php $actionItems = $visit->getActionItems(); @endphp
    @if(count($actionItems) > 0)
    <div class="action-items">
        <h3 style="margin-top: 0; color: #856404;">⚠️ Action Items Required ({{ count($actionItems) }})</h3>
        <p style="margin-bottom: 15px; color: #856404;">The following items require immediate attention:</p>
        @foreach($actionItems as $item)
            <div class="action-item">
                <strong>{{ $item['question'] }}</strong>
                @if($item['comment'])
                    <div style="margin-top: 5px; color: #666;">Comment: {{ $item['comment'] }}</div>
                @endif
            </div>
        @endforeach
    </div>
    @else
    <div class="success-message">
        <strong>✅ Excellent Performance!</strong><br>
        No action items required - all standards met.
    </div>
    @endif

    <div class="footer">
        <div style="border-top: 3px solid; border-image: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899) 1; margin-bottom: 10px;"></div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <div>
                <strong style="color: #1e40af;">📊 QMS Store Visit System</strong>
            </div>
            <div style="text-align: right; font-size: 9px; color: #64748b;">
                <div><strong>Report ID:</strong> #{{ $visit->id }}</div>
                <div><strong>Generated:</strong> {{ now()->format('Y-m-d H:i:s') }}</div>
            </div>
        </div>
        <p style="margin: 0; font-size: 9px; color: #94a3b8; text-align: center;">
            This is an automated report. For questions or support, contact your system administrator.
        </p>
        <div style="text-align: center; margin-top: 8px; padding-top: 8px; border-top: 1px solid #e2e8f0;">
            <span style="font-size: 8px; color: #cbd5e1;">© {{ date('Y') }} Quality Management System. All rights reserved.</span>
        </div>
    </div>
</body>
</html>