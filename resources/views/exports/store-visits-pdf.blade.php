<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Store Visits Report - Detailed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 11px;
        }
        .statistics {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 12px;
            border-radius: 5px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
        }
        
        /* Visit Detail Sections */
        .visit-detail {
            page-break-before: always;
            margin-bottom: 30px;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: #fff;
        }
        .visit-detail:first-of-type {
            page-break-before: auto;
        }
        .visit-header {
            background: linear-gradient(to right, #007bff, #0056b3);
            color: white;
            padding: 12px 15px;
            margin: -15px -15px 15px -15px;
            border-radius: 6px 6px 0 0;
        }
        .visit-header h2 {
            margin: 0;
            font-size: 18px;
        }
        .visit-header .meta {
            font-size: 10px;
            margin-top: 5px;
            opacity: 0.9;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 4px 10px;
            width: 30%;
            color: #555;
        }
        .info-value {
            display: table-cell;
            padding: 4px 10px;
            color: #333;
        }
        
        .section {
            margin-bottom: 15px;
            border-left: 4px solid #007bff;
            padding-left: 12px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .checklist-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }
        .checklist-table th,
        .checklist-table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }
        .checklist-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .check-yes {
            color: #28a745;
            font-weight: bold;
        }
        .check-no {
            color: #dc3545;
            font-weight: bold;
            background-color: #fff3cd;
        }
        
        .comment-box {
            background: #fffbeb;
            border-left: 3px solid #fbbf24;
            padding: 8px 10px;
            margin-top: 5px;
            font-size: 10px;
            font-style: italic;
        }
        
        .score-display {
            text-align: center;
            background: #f0f9ff;
            border: 2px solid #3b82f6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .score-number {
            font-size: 36px;
            font-weight: bold;
            color: #1e40af;
        }
        .score-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
        }
        
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
        }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-info { background-color: #dbeafe; color: #1e40af; }
        
        .action-items-box {
            background: #fef2f2;
            border: 2px solid #dc3545;
            border-radius: 8px;
            padding: 12px;
            margin-top: 15px;
        }
        .action-items-box h3 {
            color: #dc3545;
            margin: 0 0 10px 0;
            font-size: 13px;
        }
        .action-item {
            background: white;
            padding: 8px;
            margin-bottom: 6px;
            border-left: 3px solid #dc3545;
            font-size: 10px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 2px solid #ddd;
            padding-top: 10px;
        }
        
        @media print {
            .visit-detail {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏪 Store Visit Reports - Detailed View</h1>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p><strong>Showing {{ $statistics['total'] }} of {{ $statistics['total'] }} visits</strong></p>
        <p>Completed: {{ $statistics['completed'] }} | 
           Pending: {{ $statistics['pending'] }} | 
           Average Score: {{ $statistics['average_score'] }}%</p>
        @if($isAdmin)
            <p><strong>Administrator View - All Reports</strong></p>
        @else
            <p>Reports by: {{ $user->name }}</p>
        @endif
    </div>

    <div class="statistics">
        <div class="stat-item">
            <div class="stat-number">{{ $statistics['total'] }}</div>
            <div class="stat-label">Total Visits</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $statistics['completed'] }}</div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $statistics['pending'] }}</div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $statistics['average_score'] }}%</div>
            <div class="stat-label">Avg Score</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $statistics['with_action_items'] }}</div>
            <div class="stat-label">With Action Items</div>
        </div>
    </div>

    {{-- Detailed Visit Reports --}}
    @foreach($visits as $index => $visit)
    <div class="visit-detail">
        <div class="visit-header">
            <h2>Visit #{{ $index + 1 }}: {{ $visit->restaurant_name }}</h2>
            <div class="meta">
                <span>📅 {{ $visit->visit_date->format('F j, Y') }}</span> | 
                <span>🕐 {{ $visit->mic }}</span> | 
                <span>👤 Created by: {{ $visit->user->name ?? 'N/A' }}</span> | 
                <span>📊 Status: {{ $visit->status }}</span>
            </div>
        </div>

        {{-- Score Display --}}
        <div class="score-display">
            <div class="score-number">{{ $visit->score ?? $visit->calculateScore() }}%</div>
            <div class="score-label">Overall Performance Score</div>
        </div>

        {{-- Basic Information --}}
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Restaurant:</div>
                <div class="info-value">{{ $visit->restaurant_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">MIC Shift:</div>
                <div class="info-value">{{ $visit->mic }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Visit Date:</div>
                <div class="info-value">{{ $visit->visit_date->format('l, F j, Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Purpose of Visit:</div>
                <div class="info-value">
                    @if(is_array($visit->purpose_of_visit))
                        {{ implode(', ', $visit->purpose_of_visit) }}
                    @else
                        {{ $visit->purpose_of_visit ?? 'N/A' }}
                    @endif
                </div>
            </div>
            @if($isAdmin)
            <div class="info-row">
                <div class="info-label">Created By:</div>
                <div class="info-value">{{ $visit->user->name ?? 'N/A' }} ({{ $visit->created_at->format('M j, Y g:i A') }})</div>
            </div>
            @endif
        </div>

        {{-- Section A: Customer / QSC --}}
        <div class="section">
            <div class="section-title">📋 Section A: Customer / QSC</div>
            <table class="checklist-table">
                <tr>
                    <td width="50%">OCA Board Followed</td>
                    <td width="15%" class="{{ $visit->oca_board_followed ? 'check-yes' : 'check-no' }}">
                        {{ $visit->oca_board_followed ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->oca_board_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->oca_board_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>Staff Know Their Duty</td>
                    <td class="{{ $visit->staff_know_duty ? 'check-yes' : 'check-no' }}">
                        {{ $visit->staff_know_duty ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->staff_duty_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->staff_duty_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>Coaching & Directing</td>
                    <td class="{{ $visit->coaching_directing ? 'check-yes' : 'check-no' }}">
                        {{ $visit->coaching_directing ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->coaching_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->coaching_comments }}</div></td></tr>
                @endif
            </table>
        </div>

        {{-- Section B: Cashier --}}
        <div class="section">
            <div class="section-title">💰 Section B: Cashier</div>
            <table class="checklist-table">
                <tr>
                    <td width="50%">Smile & Greetings</td>
                    <td width="15%" class="{{ $visit->smile_greetings ? 'check-yes' : 'check-no' }}">
                        {{ $visit->smile_greetings ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->smile_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->smile_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>Suggestive Selling</td>
                    <td class="{{ $visit->suggestive_selling ? 'check-yes' : 'check-no' }}">
                        {{ $visit->suggestive_selling ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->suggestive_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->suggestive_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>Offer Promotion</td>
                    <td class="{{ $visit->offer_promotion ? 'check-yes' : 'check-no' }}">
                        {{ $visit->offer_promotion ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->promotion_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->promotion_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>Thank & Direction</td>
                    <td class="{{ $visit->thank_direction ? 'check-yes' : 'check-no' }}">
                        {{ $visit->thank_direction ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->thank_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->thank_comments }}</div></td></tr>
                @endif
            </table>
        </div>

        {{-- Section C: Service Standards --}}
        <div class="section">
            <div class="section-title">🚀 Section C: Service Standards</div>
            <table class="checklist-table">
                <tr>
                    <td width="50%">Team Work & Hustle</td>
                    <td width="15%" class="{{ $visit->team_work_hustle ? 'check-yes' : 'check-no' }}">
                        {{ $visit->team_work_hustle ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->teamwork_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->teamwork_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>Order Accuracy</td>
                    <td class="{{ $visit->order_accuracy ? 'check-yes' : 'check-no' }}">
                        {{ $visit->order_accuracy ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->accuracy_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->accuracy_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>Service Time</td>
                    <td class="{{ $visit->service_time ? 'check-yes' : 'check-no' }}">
                        {{ $visit->service_time ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->service_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->service_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>Dine-In Experience</td>
                    <td class="{{ $visit->dine_in ? 'check-yes' : 'check-no' }}">
                        {{ $visit->dine_in ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->dine_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->dine_comments }}</div></td></tr>
                @endif
            </table>
        </div>

        {{-- Section D: Financials --}}
        <div class="section">
            <div class="section-title">💵 Section D: Financials</div>
            <table class="checklist-table">
                <tr>
                    <td width="50%">Weekly Schedule</td>
                    <td width="15%" class="{{ $visit->weekly_schedule ? 'check-yes' : 'check-no' }}">
                        {{ $visit->weekly_schedule ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->schedule_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->schedule_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>MOD Financial Goal</td>
                    <td class="{{ $visit->mod_financial_goal ? 'check-yes' : 'check-no' }}">
                        {{ $visit->mod_financial_goal ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->financial_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->financial_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>Cash Policies</td>
                    <td class="{{ $visit->cash_policies ? 'check-yes' : 'check-no' }}">
                        {{ $visit->cash_policies ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->cash_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->cash_comments }}</div></td></tr>
                @endif
            </table>
        </div>

        {{-- Section E: Quality / Pathing --}}
        <div class="section">
            <div class="section-title">⭐ Section E: Quality / Pathing</div>
            <table class="checklist-table">
                <tr>
                    <td width="50%">TTF Followed</td>
                    <td width="15%" class="{{ $visit->ttf_followed ? 'check-yes' : 'check-no' }}">
                        {{ $visit->ttf_followed ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->ttf_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->ttf_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>Sandwich Assembly</td>
                    <td class="{{ $visit->sandwich_assembly ? 'check-yes' : 'check-no' }}">
                        {{ $visit->sandwich_assembly ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->assembly_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->assembly_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>QSC Completed</td>
                    <td class="{{ $visit->qsc_completed ? 'check-yes' : 'check-no' }}">
                        {{ $visit->qsc_completed ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->qsc_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->qsc_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>Employee Appearance</td>
                    <td class="{{ $visit->employee_appearance ? 'check-yes' : 'check-no' }}">
                        {{ $visit->employee_appearance ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->appearance_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->appearance_comments }}</div></td></tr>
                @endif
            </table>
        </div>

        {{-- Section F: Cleanliness --}}
        <div class="section">
            <div class="section-title">🧹 Section F: Cleanliness</div>
            <table class="checklist-table">
                <tr>
                    <td width="50%">Equipment Wrapped</td>
                    <td width="15%" class="{{ $visit->equipment_wrapped ? 'check-yes' : 'check-no' }}">
                        {{ $visit->equipment_wrapped ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->wrapped_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->wrapped_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>Sink Setup</td>
                    <td class="{{ $visit->sink_setup ? 'check-yes' : 'check-no' }}">
                        {{ $visit->sink_setup ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->sink_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->sink_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>Dining Area Clean</td>
                    <td class="{{ $visit->dining_area_clean ? 'check-yes' : 'check-no' }}">
                        {{ $visit->dining_area_clean ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->dining_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->dining_comments }}</div></td></tr>
                @endif
                
                <tr>
                    <td>Restroom Clean</td>
                    <td class="{{ $visit->restroom_clean ? 'check-yes' : 'check-no' }}">
                        {{ $visit->restroom_clean ? '✓ YES' : '✗ NO' }}
                    </td>
                </tr>
                @if($visit->restroom_comments)
                <tr><td colspan="2"><div class="comment-box">💬 {{ $visit->restroom_comments }}</div></td></tr>
                @endif
            </table>
        </div>

        {{-- Observation Summary --}}
        @if($visit->what_did_you_see || $visit->why_had_issue || $visit->how_to_improve)
        <div class="section">
            <div class="section-title">🔍 Observation Summary</div>
            @if($visit->what_did_you_see)
            <div class="comment-box">
                <strong>What did you see?</strong><br>
                {{ $visit->what_did_you_see }}
            </div>
            @endif
            @if($visit->why_had_issue)
            <div class="comment-box">
                <strong>Why was there an issue?</strong><br>
                {{ $visit->why_had_issue }}
            </div>
            @endif
            @if($visit->how_to_improve)
            <div class="comment-box">
                <strong>How to improve?</strong><br>
                {{ $visit->how_to_improve }}
            </div>
            @endif
        </div>
        @endif

        {{-- Action Items --}}
        @php $actionItems = $visit->getActionItems(); @endphp
        @if(count($actionItems) > 0)
        <div class="action-items-box">
            <h3>⚠️ Action Items Required ({{ count($actionItems) }} items)</h3>
            @foreach($actionItems as $item)
            <div class="action-item">
                <strong>{{ $item['question'] }}</strong>
                @if(isset($item['comment']) && $item['comment'])
                <div style="margin-top: 4px; color: #666;">Comment: {{ $item['comment'] }}</div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- General Comments --}}
        @if($visit->general_comments)
        <div class="section">
            <div class="section-title">📝 General Comments</div>
            <div class="comment-box">{{ $visit->general_comments }}</div>
        </div>
        @endif
    </div>
    @endforeach

    <div class="footer">
        <p>This report was generated automatically by the QMS Store Visit System.</p>
        <p>For questions or issues, please contact your system administrator.</p>
    </div>
</body>
</html>