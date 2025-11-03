<!DOCTYPE html><!DOCTYPE html><!DOCTYPE html><!DOCTYPE html>

<html lang="en">

<head><html lang="en">

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0"><head><html lang="en"><html lang="en">

    <title>Store Visit Report - Print</title>

    <style>    <meta charset="UTF-8">

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { font-family: Arial, sans-serif; font-size: 8px; padding: 5px; background: white; line-height: 1.2; }    <meta name="viewport" content="width=device-width, initial-scale=1.0"><head><head>

        .container { max-width: 100%; margin: 0 auto; }

        .header { margin-bottom: 5px; padding-bottom: 3px; border-bottom: 1px solid #333; }    <title>Store Visit Report - Print</title>

        .header h1 { font-size: 13px; font-weight: 700; color: #111; margin-bottom: 2px; }

        .header p { font-size: 8px; color: #555; }    <style>    <meta charset="UTF-8">    <meta charset="UTF-8">

        .sections-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 4px; }

        .section { padding: 4px; border-radius: 2px; border: 1px solid; page-break-inside: avoid; }        * { margin: 0; padding: 0; box-sizing: border-box; }

        .section-title { font-size: 9px; font-weight: 700; margin-bottom: 3px; padding-bottom: 2px; border-bottom: 1px solid rgba(0,0,0,0.1); }

        .field-row { margin-bottom: 2px; padding: 2px 4px; background: white; border-radius: 2px; }        body { font-family: Arial, sans-serif; font-size: 8px; padding: 5px; background: white; line-height: 1.2; }    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <meta name="viewport" content="width=device-width, initial-scale=1.0">

        .field-row:last-child { margin-bottom: 0; }

        .field-row strong { font-size: 7px; color: #111; display: inline-block; min-width: 80px; }        .container { max-width: 100%; margin: 0 auto; }

        .status-yes { color: #16a34a; font-weight: 700; font-size: 7px; }

        .status-no { color: #dc2626; font-weight: 700; font-size: 7px; }        .header { margin-bottom: 5px; padding-bottom: 3px; border-bottom: 1px solid #333; }    <title>Store Visit Report - Print</title>    <title>Store Visit Report - Print</title>

        .comment { margin-top: 1px; padding: 2px 3px; background: #f5f5f5; border-left: 2px solid #999; font-size: 6px; color: #333; font-style: italic; line-height: 1.2; }

        .text-field { padding: 2px 3px; background: white; margin-bottom: 2px; }        .header h1 { font-size: 13px; font-weight: 700; color: #111; margin-bottom: 2px; }

        .text-field:last-child { margin-bottom: 0; }

        .text-label { display: block; font-size: 6px; font-weight: 700; color: #555; text-transform: uppercase; margin-bottom: 1px; }        .header p { font-size: 8px; color: #555; }    <style>    <style>

        .text-value { font-size: 7px; color: #111; line-height: 1.3; }

        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 3px; }        .sections-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 4px; }

        .full-width { grid-column: 1 / -1; }

        .bg-gray { background: #f9fafb; border-color: #ccc; }        .section { padding: 4px; border-radius: 2px; border: 1px solid; page-break-inside: avoid; }        * { margin: 0; padding: 0; box-sizing: border-box; }        * { margin: 0; padding: 0; box-sizing: border-box; }

        .bg-green { background: #f0fdf4; border-color: #86efac; }

        .bg-yellow { background: #fefce8; border-color: #fde047; }        .section-title { font-size: 9px; font-weight: 700; margin-bottom: 3px; padding-bottom: 2px; border-bottom: 1px solid rgba(0,0,0,0.1); }

        .bg-blue { background: #eff6ff; border-color: #93c5fd; }

        .bg-purple { background: #faf5ff; border-color: #d8b4fe; }        .field-row { margin-bottom: 2px; padding: 2px 4px; background: white; border-radius: 2px; }        body { font-family: Arial, sans-serif; font-size: 10px; padding: 10px; background: white; }        body { font-family: Arial, sans-serif; font-size: 9px; padding: 8px; background: white; }

        .bg-orange { background: #fff7ed; border-color: #fdba74; }

        .bg-teal { background: #f0fdfa; border-color: #5eead4; }        .field-row:last-child { margin-bottom: 0; }

        .bg-indigo { background: #eef2ff; border-color: #a5b4fc; }

        @media print {         .field-row strong { font-size: 7px; color: #111; display: inline-block; min-width: 80px; }        .container { max-width: 100%; margin: 0 auto; }        .container { max-width: 100%; margin: 0 auto; }

            @page { size: A4 landscape; margin: 5mm; }

            body { padding: 0; font-size: 7px; }        .status-yes { color: #16a34a; font-weight: 700; font-size: 7px; }

            .section { page-break-inside: avoid; }

            .header { margin-bottom: 3px; }        .status-no { color: #dc2626; font-weight: 700; font-size: 7px; }        .header { margin-bottom: 10px; padding-bottom: 8px; border-bottom: 2px solid #e5e7eb; }        .header { margin-bottom: 8px; padding-bottom: 6px; border-bottom: 2px solid #e5e7eb; }

            .sections-grid { gap: 3px; }

        }        .comment { margin-top: 1px; padding: 2px 3px; background: #f5f5f5; border-left: 2px solid #999; font-size: 6px; color: #333; font-style: italic; line-height: 1.2; }

    </style>

</head>        .text-field { padding: 2px 3px; background: white; margin-bottom: 2px; }        .header h1 { font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 4px; }        .header h1 { font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 3px; }

<body>

    <div class="container">        .text-field:last-child { margin-bottom: 0; }

        <div class="header">

            <h1>🏪 Store Visit Report</h1>        .text-label { display: block; font-size: 6px; font-weight: 700; color: #555; text-transform: uppercase; margin-bottom: 1px; }        .header p { font-size: 11px; color: #6b7280; }        .header p { font-size: 10px; color: #6b7280; }

            <p><strong>Restaurant:</strong> {{ $visit->restaurant_name ?? 'Unknown' }} | <strong>MIC:</strong> {{ $visit->mic ?? 'N/A' }} | <strong>Date:</strong> {{ $visit->visit_date ?? 'N/A' }} | <strong>Purpose:</strong> {{ is_array($visit->purpose_of_visit) ? implode(', ', $visit->purpose_of_visit) : ($visit->purpose_of_visit ?? 'N/A') }}</p>

        </div>        .text-value { font-size: 7px; color: #111; line-height: 1.3; }

        

        <div class="sections-grid">        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 3px; }        .sections-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }        .sections-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px; }

            <!-- Section 1: Customer / QSC -->

            <div class="section bg-green">        .full-width { grid-column: 1 / -1; }

                <div class="section-title">👥 Customer / QSC</div>

                <div class="field-row">        .bg-gray { background: #f9fafb; border-color: #ccc; }        .section { margin-bottom: 8px; padding: 10px; border-radius: 4px; border: 1px solid; page-break-inside: avoid; }        .section { margin-bottom: 6px; padding: 8px; border-radius: 4px; border: 1px solid; page-break-inside: avoid; }

                    <strong>OCA Board:</strong> 

                    <span class="{{ $visit->oca_board_followed ? 'status-yes' : 'status-no' }}">{{ $visit->oca_board_followed ? '✓' : '✗' }}</span>        .bg-green { background: #f0fdf4; border-color: #86efac; }

                    @if($visit->oca_board_comments)<div class="comment">{{ $visit->oca_board_comments }}</div>@endif

                </div>        .bg-yellow { background: #fefce8; border-color: #fde047; }        .section-title { font-size: 12px; font-weight: 600; margin-bottom: 8px; }        .section-title { font-size: 11px; font-weight: 600; margin-bottom: 6px; }

                <div class="field-row">

                    <strong>Staff Duty:</strong>         .bg-blue { background: #eff6ff; border-color: #93c5fd; }

                    <span class="{{ $visit->staff_know_duty ? 'status-yes' : 'status-no' }}">{{ $visit->staff_know_duty ? '✓' : '✗' }}</span>

                    @if($visit->staff_duty_comments)<div class="comment">{{ $visit->staff_duty_comments }}</div>@endif        .bg-purple { background: #faf5ff; border-color: #d8b4fe; }        .field-row { margin-bottom: 5px; padding: 6px 8px; background: white; border-radius: 3px; border: 1px solid #e5e7eb; }        .field-row { margin-bottom: 4px; padding: 5px 6px; background: white; border-radius: 3px; border: 1px solid #e5e7eb; }

                </div>

                <div class="field-row">        .bg-orange { background: #fff7ed; border-color: #fdba74; }

                    <strong>Coaching:</strong> 

                    <span class="{{ $visit->coaching_directing ? 'status-yes' : 'status-no' }}">{{ $visit->coaching_directing ? '✓' : '✗' }}</span>        .bg-teal { background: #f0fdfa; border-color: #5eead4; }        .field-row strong { font-size: 10px; color: #111827; }        .field-row strong { font-size: 9px; color: #111827; }

                    @if($visit->coaching_comments)<div class="comment">{{ $visit->coaching_comments }}</div>@endif

                </div>        .bg-indigo { background: #eef2ff; border-color: #a5b4fc; }

            </div>

        @media print {         .status-yes { color: #16a34a; font-weight: 600; font-size: 10px; }        .status-yes { color: #16a34a; font-weight: 600; font-size: 9px; }

            <!-- Section 2: Cashier -->

            <div class="section bg-yellow">            @page { size: A4 landscape; margin: 5mm; }

                <div class="section-title">💰 Cashier</div>

                <div class="field-row">            body { padding: 0; font-size: 7px; }        .status-no { color: #dc2626; font-weight: 600; font-size: 10px; }        .status-no { color: #dc2626; font-weight: 600; font-size: 9px; }

                    <strong>Smile/Greetings:</strong> 

                    <span class="{{ $visit->smile_greetings ? 'status-yes' : 'status-no' }}">{{ $visit->smile_greetings ? '✓' : '✗' }}</span>            .section { page-break-inside: avoid; }

                    @if($visit->smile_comments)<div class="comment">{{ $visit->smile_comments }}</div>@endif

                </div>            .header { margin-bottom: 3px; }        .comment { margin-top: 4px; padding: 4px 6px; background: #f9fafb; border-left: 2px solid #d1d5db; font-size: 9px; color: #4b5563; font-style: italic; line-height: 1.3; }        .comment { margin-top: 3px; padding: 3px 5px; background: #f9fafb; border-left: 2px solid #d1d5db; font-size: 8px; color: #4b5563; font-style: italic; line-height: 1.2; min-height: 18px; display: block; }

                <div class="field-row">

                    <strong>Suggestive Selling:</strong>             .sections-grid { gap: 3px; }

                    <span class="{{ $visit->suggestive_selling ? 'status-yes' : 'status-no' }}">{{ $visit->suggestive_selling ? '✓' : '✗' }}</span>

                    @if($visit->selling_comments)<div class="comment">{{ $visit->selling_comments }}</div>@endif        }        .text-field { padding: 6px; background: white; border-radius: 3px; border: 1px solid #e5e7eb; margin-bottom: 5px; }        .comment:before { content: 'Comment: '; font-weight: 600; color: #6b7280; }

                </div>

            </div>    </style>



            <!-- Section 3: Service Standards --></head>        .text-label { display: block; font-size: 9px; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 3px; }        .text-field { padding: 5px; background: white; border-radius: 3px; border: 1px solid #e5e7eb; margin-bottom: 4px; }

            <div class="section bg-blue">

                <div class="section-title">⚡ Service Standards</div><body onload="window.print();">

                <div class="field-row">

                    <strong>Team Work:</strong>     <div class="container">        .text-value { font-size: 10px; color: #111827; white-space: pre-wrap; line-height: 1.4; }        .text-label { display: block; font-size: 8px; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 2px; }

                    <span class="{{ $visit->team_work_hustle ? 'status-yes' : 'status-no' }}">{{ $visit->team_work_hustle ? '✓' : '✗' }}</span>

                    @if($visit->hustle_comments)<div class="comment">{{ $visit->hustle_comments }}</div>@endif        <div class="header">

                </div>

                <div class="field-row">            <h1>🏪 Store Visit Report</h1>        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }        .text-value { font-size: 9px; color: #111827; white-space: pre-wrap; line-height: 1.3; }

                    <strong>Fast Service:</strong> 

                    <span class="{{ $visit->fast_service ? 'status-yes' : 'status-no' }}">{{ $visit->fast_service ? '✓' : '✗' }}</span>            <p><strong>Restaurant:</strong> {{ $visit->restaurant_name ?? 'Unknown' }} | <strong>MIC:</strong> {{ $visit->mic ?? 'N/A' }} | <strong>Date:</strong> {{ $visit->visit_date ?? 'N/A' }} | <strong>Purpose:</strong> {{ is_array($visit->purpose_of_visit) ? implode(', ', $visit->purpose_of_visit) : ($visit->purpose_of_visit ?? 'N/A') }}</p>

                    @if($visit->service_comments)<div class="comment">{{ $visit->service_comments }}</div>@endif

                </div>        </div>        .full-width { grid-column: 1 / -1; }        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px; }

                <div class="field-row">

                    <strong>Product Quality:</strong>         

                    <span class="{{ $visit->product_quality ? 'status-yes' : 'status-no' }}">{{ $visit->product_quality ? '✓' : '✗' }}</span>

                    @if($visit->quality_comments)<div class="comment">{{ $visit->quality_comments }}</div>@endif        <div class="sections-grid">        .bg-gray { background: #f9fafb; border-color: #e5e7eb; }        .full-width { grid-column: 1 / -1; }

                </div>

            </div>            <!-- Section 1: Customer / QSC -->



            <!-- Section 4: Financials -->            <div class="section bg-green">        .bg-green { background: #f0fdf4; border-color: #bbf7d0; }        .bg-gray { background: #f9fafb; border-color: #e5e7eb; }

            <div class="section bg-purple">

                <div class="section-title">💼 Financials</div>                <div class="section-title">👥 Customer / QSC</div>

                <div class="field-row">

                    <strong>Weekly Schedule:</strong>                 <div class="field-row">        .bg-yellow { background: #fefce8; border-color: #fef08a; }        .bg-green { background: #f0fdf4; border-color: #bbf7d0; }

                    <span class="{{ $visit->weekly_schedule ? 'status-yes' : 'status-no' }}">{{ $visit->weekly_schedule ? '✓' : '✗' }}</span>

                    @if($visit->schedule_comments)<div class="comment">{{ $visit->schedule_comments }}</div>@endif                    <strong>OCA Board:</strong> 

                </div>

                <div class="field-row">                    <span class="{{ $visit->oca_board_followed ? 'status-yes' : 'status-no' }}">{{ $visit->oca_board_followed ? '✓' : '✗' }}</span>        .bg-blue { background: #eff6ff; border-color: #bfdbfe; }        .bg-yellow { background: #fefce8; border-color: #fef08a; }

                    <strong>MOD Financial Goal:</strong> 

                    <span class="{{ $visit->mod_financial_goal ? 'status-yes' : 'status-no' }}">{{ $visit->mod_financial_goal ? '✓' : '✗' }}</span>                    @if($visit->oca_board_comments)<div class="comment">{{ $visit->oca_board_comments }}</div>@endif

                    @if($visit->financial_comments)<div class="comment">{{ $visit->financial_comments }}</div>@endif

                </div>                </div>        .bg-purple { background: #faf5ff; border-color: #e9d5ff; }        .bg-blue { background: #eff6ff; border-color: #bfdbfe; }

            </div>

                <div class="field-row">

            <!-- Section 5: Quality / Pathing -->

            <div class="section bg-orange">                    <strong>Staff Duty:</strong>         .bg-orange { background: #fff7ed; border-color: #fed7aa; }        .bg-purple { background: #faf5ff; border-color: #e9d5ff; }

                <div class="section-title">🔧 Quality / Pathing</div>

                <div class="field-row">                    <span class="{{ $visit->staff_know_duty ? 'status-yes' : 'status-no' }}">{{ $visit->staff_know_duty ? '✓' : '✗' }}</span>

                    <strong>TTF Followed:</strong> 

                    <span class="{{ $visit->ttf_followed ? 'status-yes' : 'status-no' }}">{{ $visit->ttf_followed ? '✓' : '✗' }}</span>                    @if($visit->staff_duty_comments)<div class="comment">{{ $visit->staff_duty_comments }}</div>@endif        .bg-teal { background: #f0fdfa; border-color: #99f6e4; }        .bg-orange { background: #fff7ed; border-color: #fed7aa; }

                    @if($visit->ttf_comments)<div class="comment">{{ $visit->ttf_comments }}</div>@endif

                </div>                </div>

                <div class="field-row">

                    <strong>Equipment Working:</strong>                 <div class="field-row">        .bg-indigo { background: #eef2ff; border-color: #c7d2fe; }        .bg-teal { background: #f0fdfa; border-color: #99f6e4; }

                    <span class="{{ $visit->equipment_working ? 'status-yes' : 'status-no' }}">{{ $visit->equipment_working ? '✓' : '✗' }}</span>

                    @if($visit->equipment_comments)<div class="comment">{{ $visit->equipment_comments }}</div>@endif                    <strong>Coaching:</strong> 

                </div>

            </div>                    <span class="{{ $visit->coaching_directing ? 'status-yes' : 'status-no' }}">{{ $visit->coaching_directing ? '✓' : '✗' }}</span>        @media print {         .bg-indigo { background: #eef2ff; border-color: #c7d2fe; }



            <!-- Section 6: Cleanliness -->                    @if($visit->coaching_comments)<div class="comment">{{ $visit->coaching_comments }}</div>@endif

            <div class="section bg-teal">

                <div class="section-title">🧽 Cleanliness</div>                </div>            @page { size: A4; margin: 10mm; }        @media print { 

                <div class="field-row">

                    <strong>Dining Area:</strong>             </div>

                    <span class="{{ $visit->dining_area_clean ? 'status-yes' : 'status-no' }}">{{ $visit->dining_area_clean ? '✓' : '✗' }}</span>

                    @if($visit->dining_comments)<div class="comment">{{ $visit->dining_comments }}</div>@endif            body { padding: 0; font-size: 9px; }            @page { size: A4; margin: 8mm; }

                </div>

                <div class="field-row">            <!-- Section 2: Cashier -->

                    <strong>Restroom:</strong> 

                    <span class="{{ $visit->restroom_clean ? 'status-yes' : 'status-no' }}">{{ $visit->restroom_clean ? '✓' : '✗' }}</span>            <div class="section bg-yellow">            .section { page-break-inside: avoid; margin-bottom: 6px; }            body { padding: 0; font-size: 8px; }

                    @if($visit->restroom_comments)<div class="comment">{{ $visit->restroom_comments }}</div>@endif

                </div>                <div class="section-title">💰 Cashier</div>

            </div>

                <div class="field-row">            .header { margin-bottom: 8px; }            .section { page-break-inside: avoid; margin-bottom: 4px; }

            <!-- Section 7: Follow-Up -->

            @if($visit->last_visit_date || $visit->last_visit_summary || $visit->last_visit_update || $visit->other_follow_up)                    <strong>Smile/Greetings:</strong> 

            <div class="section bg-gray">

                <div class="section-title">📅 Follow-Up</div>                    <span class="{{ $visit->smile_greetings ? 'status-yes' : 'status-no' }}">{{ $visit->smile_greetings ? '✓' : '✗' }}</span>            .header h1 { font-size: 16px; }            .header { margin-bottom: 6px; }

                @if($visit->last_visit_date)

                <div class="text-field">                    @if($visit->smile_comments)<div class="comment">{{ $visit->smile_comments }}</div>@endif

                    <span class="text-label">Last Visit Date</span>

                    <div class="text-value">{{ $visit->last_visit_date }}</div>                </div>        }            .header h1 { font-size: 14px; }

                </div>

                @endif                <div class="field-row">

                @if($visit->last_visit_summary)

                <div class="text-field">                    <strong>Suggestive Selling:</strong>     </style>        }

                    <span class="text-label">Summary</span>

                    <div class="text-value">{{ $visit->last_visit_summary }}</div>                    <span class="{{ $visit->suggestive_selling ? 'status-yes' : 'status-no' }}">{{ $visit->suggestive_selling ? '✓' : '✗' }}</span>

                </div>

                @endif                    @if($visit->selling_comments)<div class="comment">{{ $visit->selling_comments }}</div>@endif</head>    </style>

                @if($visit->last_visit_update)

                <div class="text-field">                </div>

                    <span class="text-label">Update</span>

                    <div class="text-value">{{ $visit->last_visit_update }}</div>            </div><body onload="window.print();"></head>

                </div>

                @endif

                @if($visit->other_follow_up)

                <div class="text-field">            <!-- Section 3: Service Standards -->    <div class="container"><body onload="window.print();">

                    <span class="text-label">Other</span>

                    <div class="text-value">{{ $visit->other_follow_up }}</div>            <div class="section bg-blue">

                </div>

                @endif                <div class="section-title">⚡ Service Standards</div>        <div class="header">    <div class="container">

            </div>

            @endif                <div class="field-row">



            <!-- Section 8: Observation Summary -->                    <strong>Team Work:</strong>             <h1>Store Visit Report</h1>        <div class="header">

            @if($visit->what_did_you_see || $visit->why_had_issue || $visit->how_to_improve || $visit->who_when_responsible)

            <div class="section bg-indigo">                    <span class="{{ $visit->team_work_hustle ? 'status-yes' : 'status-no' }}">{{ $visit->team_work_hustle ? '✓' : '✗' }}</span>

                <div class="section-title">📊 Observations</div>

                @if($visit->what_did_you_see)                    @if($visit->hustle_comments)<div class="comment">{{ $visit->hustle_comments }}</div>@endif            <p>Restaurant: {{ $visit->restaurant_name ?? 'Unknown' }} | Date: {{ $visit->visit_date ?? 'N/A' }}</p>            <h1>Store Visit Report</h1>

                <div class="text-field">

                    <span class="text-label">What?</span>                </div>

                    <div class="text-value">{{ $visit->what_did_you_see }}</div>

                </div>                <div class="field-row">        </div>            <p>Restaurant: {{ $visit->restaurant_name ?? 'Unknown' }} | Date: {{ $visit->visit_date ?? 'N/A' }}</p>

                @endif

                @if($visit->why_had_issue)                    <strong>Fast Service:</strong> 

                <div class="text-field">

                    <span class="text-label">Why?</span>                    <span class="{{ $visit->fast_service ? 'status-yes' : 'status-no' }}">{{ $visit->fast_service ? '✓' : '✗' }}</span>                </div>

                    <div class="text-value">{{ $visit->why_had_issue }}</div>

                </div>                    @if($visit->service_comments)<div class="comment">{{ $visit->service_comments }}</div>@endif

                @endif

                @if($visit->how_to_improve)                </div>        <div class="sections-grid">        

                <div class="text-field">

                    <span class="text-label">How?</span>                <div class="field-row">

                    <div class="text-value">{{ $visit->how_to_improve }}</div>

                </div>                    <strong>Product Quality:</strong>             <!-- Section 1: Basic Information -->        <div class="sections-grid">

                @endif

                @if($visit->who_when_responsible)                    <span class="{{ $visit->product_quality ? 'status-yes' : 'status-no' }}">{{ $visit->product_quality ? '✓' : '✗' }}</span>

                <div class="text-field">

                    <span class="text-label">Who/When?</span>                    @if($visit->quality_comments)<div class="comment">{{ $visit->quality_comments }}</div>@endif            <div class="section bg-gray">        <div class="section bg-gray">

                    <div class="text-value">{{ $visit->who_when_responsible }}</div>

                </div>                </div>

                @endif

            </div>            </div>                <div class="section-title">📋 Basic Information</div>            <div class="section-title">📋 Basic Information</div>

            @endif



            <!-- Section 9: General Comments -->

            @if($visit->general_comments)            <!-- Section 4: Financials -->                <div class="grid-2">            <div class="grid-2">

            <div class="section bg-gray full-width">

                <div class="section-title">💬 General Comments</div>            <div class="section bg-purple">

                <div class="text-value" style="padding: 3px;">{{ $visit->general_comments }}</div>

            </div>                <div class="section-title">💼 Financials</div>                    <div class="field-row"><strong>Restaurant:</strong> {{ $visit->restaurant_name ?? 'N/A' }}</div>                <div class="field-row"><strong>Restaurant:</strong> {{ $visit->restaurant_name ?? 'N/A' }}</div>

            @endif

        </div>                <div class="field-row">

    </div>

</body>                    <strong>Weekly Schedule:</strong>                     <div class="field-row"><strong>MIC:</strong> {{ $visit->mic ?? 'N/A' }}</div>                <div class="field-row"><strong>MIC:</strong> {{ $visit->mic ?? 'N/A' }}</div>

</html>

                    <span class="{{ $visit->weekly_schedule ? 'status-yes' : 'status-no' }}">{{ $visit->weekly_schedule ? '✓' : '✗' }}</span>

                    @if($visit->schedule_comments)<div class="comment">{{ $visit->schedule_comments }}</div>@endif                    <div class="field-row"><strong>Visit Date:</strong> {{ $visit->visit_date ?? 'N/A' }}</div>                <div class="field-row"><strong>Visit Date:</strong> {{ $visit->visit_date ?? 'N/A' }}</div>

                </div>

                <div class="field-row">                    <div class="field-row"><strong>Purpose:</strong> {{ is_array($visit->purpose_of_visit) ? implode(', ', $visit->purpose_of_visit) : ($visit->purpose_of_visit ?? 'N/A') }}</div>                <div class="field-row"><strong>Auto Score:</strong> {{ $visit->auto_score ?? '0' }}%</div>

                    <strong>MOD Financial Goal:</strong> 

                    <span class="{{ $visit->mod_financial_goal ? 'status-yes' : 'status-no' }}">{{ $visit->mod_financial_goal ? '✓' : '✗' }}</span>                </div>                <div class="field-row full-width"><strong>Purpose:</strong> {{ is_array($visit->purpose_of_visit) ? implode(', ', $visit->purpose_of_visit) : ($visit->purpose_of_visit ?? 'N/A') }}</div>

                    @if($visit->financial_comments)<div class="comment">{{ $visit->financial_comments }}</div>@endif

                </div>            </div>            </div>

            </div>

        </div>

            <!-- Section 5: Quality / Pathing -->

            <div class="section bg-orange">            <!-- Section 2: Customer / QSC -->

                <div class="section-title">🔧 Quality / Pathing</div>

                <div class="field-row">            <div class="section bg-green">        <div class="section bg-green">

                    <strong>TTF Followed:</strong> 

                    <span class="{{ $visit->ttf_followed ? 'status-yes' : 'status-no' }}">{{ $visit->ttf_followed ? '✓' : '✗' }}</span>                <div class="section-title">👥 Customer / QSC</div>            <div class="section-title"> Customer / QSC</div>

                    @if($visit->ttf_comments)<div class="comment">{{ $visit->ttf_comments }}</div>@endif

                </div>                <div class="field-row">            <div class="field-row">

                <div class="field-row">

                    <strong>Equipment Working:</strong>                     <strong>OCA Board:</strong>                 <strong>OCA Board Followed:</strong> 

                    <span class="{{ $visit->equipment_working ? 'status-yes' : 'status-no' }}">{{ $visit->equipment_working ? '✓' : '✗' }}</span>

                    @if($visit->equipment_comments)<div class="comment">{{ $visit->equipment_comments }}</div>@endif                    <span class="{{ $visit->oca_board_followed ? 'status-yes' : 'status-no' }}">                <span class="{{ $visit->oca_board_followed ? 'status-yes' : 'status-no' }}">

                </div>

            </div>                        {{ $visit->oca_board_followed ? '✓ Yes' : '✗ No' }}                    {{ $visit->oca_board_followed ? ' Yes' : ' No' }}



            <!-- Section 6: Cleanliness -->                    </span>                </span>

            <div class="section bg-teal">

                <div class="section-title">🧽 Cleanliness</div>                    @if($visit->oca_board_comments)                <div class="comment">{{ $visit->oca_board_comments ?? '' }}</div>

                <div class="field-row">

                    <strong>Dining Area:</strong>                     <div class="comment">{{ $visit->oca_board_comments }}</div>            </div>

                    <span class="{{ $visit->dining_area_clean ? 'status-yes' : 'status-no' }}">{{ $visit->dining_area_clean ? '✓' : '✗' }}</span>

                    @if($visit->dining_comments)<div class="comment">{{ $visit->dining_comments }}</div>@endif                    @endif            <div class="field-row">

                </div>

                <div class="field-row">                </div>                <strong>Staff Know Duty:</strong> 

                    <strong>Restroom:</strong> 

                    <span class="{{ $visit->restroom_clean ? 'status-yes' : 'status-no' }}">{{ $visit->restroom_clean ? '✓' : '✗' }}</span>                <div class="field-row">                <span class="{{ $visit->staff_know_duty ? 'status-yes' : 'status-no' }}">

                    @if($visit->restroom_comments)<div class="comment">{{ $visit->restroom_comments }}</div>@endif

                </div>                    <strong>Staff Duty:</strong>                     {{ $visit->staff_know_duty ? ' Yes' : ' No' }}

            </div>

                    <span class="{{ $visit->staff_know_duty ? 'status-yes' : 'status-no' }}">                </span>

            <!-- Section 7: Follow-Up -->

            @if($visit->last_visit_date || $visit->last_visit_summary || $visit->last_visit_update || $visit->other_follow_up)                        {{ $visit->staff_know_duty ? '✓ Yes' : '✗ No' }}                <div class="comment">{{ $visit->staff_duty_comments ?? '' }}</div>

            <div class="section bg-gray">

                <div class="section-title">📅 Follow-Up</div>                    </span>            </div>

                @if($visit->last_visit_date)

                <div class="text-field">                    @if($visit->staff_duty_comments)            <div class="field-row">

                    <span class="text-label">Last Visit Date</span>

                    <div class="text-value">{{ $visit->last_visit_date }}</div>                    <div class="comment">{{ $visit->staff_duty_comments }}</div>                <strong>Coaching / Directing:</strong> 

                </div>

                @endif                    @endif                <span class="{{ $visit->coaching_directing ? 'status-yes' : 'status-no' }}">

                @if($visit->last_visit_summary)

                <div class="text-field">                </div>                    {{ $visit->coaching_directing ? ' Yes' : ' No' }}

                    <span class="text-label">Summary</span>

                    <div class="text-value">{{ $visit->last_visit_summary }}</div>                <div class="field-row">                </span>

                </div>

                @endif                    <strong>Coaching:</strong>                 <div class="comment">{{ $visit->coaching_comments ?? '' }}</div>

                @if($visit->last_visit_update)

                <div class="text-field">                    <span class="{{ $visit->coaching_directing ? 'status-yes' : 'status-no' }}">            </div>

                    <span class="text-label">Update</span>

                    <div class="text-value">{{ $visit->last_visit_update }}</div>                        {{ $visit->coaching_directing ? '✓ Yes' : '✗ No' }}        </div>

                </div>

                @endif                    </span>

                @if($visit->other_follow_up)

                <div class="text-field">                    @if($visit->coaching_comments)        <div class="section bg-yellow">

                    <span class="text-label">Other</span>

                    <div class="text-value">{{ $visit->other_follow_up }}</div>                    <div class="comment">{{ $visit->coaching_comments }}</div>            <div class="section-title">💰 Cashier</div>

                </div>

                @endif                    @endif            <div class="grid-2">

            </div>

            @endif                </div>                <div class="field-row">



            <!-- Section 8: Observation Summary -->            </div>                    <strong>Smile & Greetings:</strong> 

            @if($visit->what_did_you_see || $visit->why_had_issue || $visit->how_to_improve || $visit->who_when_responsible)

            <div class="section bg-indigo">                    <span class="{{ $visit->smile_greetings ? 'status-yes' : 'status-no' }}">

                <div class="section-title">📊 Observations</div>

                @if($visit->what_did_you_see)            <!-- Section 3: Cashier -->                        {{ $visit->smile_greetings ? '✓ Yes' : '✗ No' }}

                <div class="text-field">

                    <span class="text-label">What?</span>            <div class="section bg-yellow">                    </span>

                    <div class="text-value">{{ $visit->what_did_you_see }}</div>

                </div>                <div class="section-title">💰 Cashier</div>                    <div class="comment">{{ $visit->smile_comments ?? '' }}</div>

                @endif

                @if($visit->why_had_issue)                <div class="grid-2">                </div>

                <div class="text-field">

                    <span class="text-label">Why?</span>                    <div class="field-row">                <div class="field-row">

                    <div class="text-value">{{ $visit->why_had_issue }}</div>

                </div>                        <strong>Smile/Greetings:</strong>                     <strong>Suggestive Selling:</strong> 

                @endif

                @if($visit->how_to_improve)                        <span class="{{ $visit->smile_greetings ? 'status-yes' : 'status-no' }}">                    <span class="{{ $visit->suggestive_selling ? 'status-yes' : 'status-no' }}">

                <div class="text-field">

                    <span class="text-label">How?</span>                            {{ $visit->smile_greetings ? '✓ Yes' : '✗ No' }}                        {{ $visit->suggestive_selling ? '✓ Yes' : '✗ No' }}

                    <div class="text-value">{{ $visit->how_to_improve }}</div>

                </div>                        </span>                    </span>

                @endif

                @if($visit->who_when_responsible)                        @if($visit->smile_comments)                    <div class="comment">{{ $visit->selling_comments ?? '' }}</div>

                <div class="text-field">

                    <span class="text-label">Who/When?</span>                        <div class="comment">{{ $visit->smile_comments }}</div>                </div>

                    <div class="text-value">{{ $visit->who_when_responsible }}</div>

                </div>                        @endif                <div class="field-row">

                @endif

            </div>                    </div>                    <strong>Offer New Promotion:</strong> 

            @endif

                    <div class="field-row">                    <span class="{{ $visit->offer_promotion ? 'status-yes' : 'status-no' }}">

            <!-- Section 9: General Comments -->

            @if($visit->general_comments)                        <strong>Suggestive Selling:</strong>                         {{ $visit->offer_promotion ? '✓ Yes' : '✗ No' }}

            <div class="section bg-gray full-width">

                <div class="section-title">💬 General Comments</div>                        <span class="{{ $visit->suggestive_selling ? 'status-yes' : 'status-no' }}">                    </span>

                <div class="text-value" style="padding: 3px;">{{ $visit->general_comments }}</div>

            </div>                            {{ $visit->suggestive_selling ? '✓ Yes' : '✗ No' }}                    <div class="comment">{{ $visit->promotion_comments ?? '' }}</div>

            @endif

        </div>                        </span>                </div>

    </div>

</body>                        @if($visit->selling_comments)                <div class="field-row">

</html>

                        <div class="comment">{{ $visit->selling_comments }}</div>                    <strong>Thank You & Direction:</strong> 

                        @endif                    <span class="{{ $visit->thank_direction ? 'status-yes' : 'status-no' }}">

                    </div>                        {{ $visit->thank_direction ? '✓ Yes' : '✗ No' }}

                </div>                    </span>

            </div>                    <div class="comment">{{ $visit->direction_comments ?? '' }}</div>

                </div>

            <!-- Section 4: Service Standards -->            </div>

            <div class="section bg-blue">        </div>

                <div class="section-title">⚡ Service Standards</div>

                <div class="grid-2">        <div class="section bg-blue">

                    <div class="field-row">            <div class="section-title">🚀 Service Standards</div>

                        <strong>Team Work & Hustle:</strong>             <div class="grid-2">

                        <span class="{{ $visit->team_work_hustle ? 'status-yes' : 'status-no' }}">                <div class="field-row">

                            {{ $visit->team_work_hustle ? '✓ Yes' : '✗ No' }}                    <strong>Team Work & Hustle:</strong> 

                        </span>                    <span class="{{ $visit->team_work_hustle ? 'status-yes' : 'status-no' }}">

                        @if($visit->hustle_comments)                        {{ $visit->team_work_hustle ? '✓ Yes' : '✗ No' }}

                        <div class="comment">{{ $visit->hustle_comments }}</div>                    </span>

                        @endif                    <div class="comment">{{ $visit->hustle_comments ?? '' }}</div>

                    </div>                </div>

                    <div class="field-row">                <div class="field-row">

                        <strong>Fast Service:</strong>                     <strong>Order Accuracy:</strong> 

                        <span class="{{ $visit->fast_service ? 'status-yes' : 'status-no' }}">                    <span class="{{ $visit->order_accuracy ? 'status-yes' : 'status-no' }}">

                            {{ $visit->fast_service ? '✓ Yes' : '✗ No' }}                        {{ $visit->order_accuracy ? '✓ Yes' : '✗ No' }}

                        </span>                    </span>

                        @if($visit->service_comments)                    <div class="comment">{{ $visit->accuracy_comments ?? '' }}</div>

                        <div class="comment">{{ $visit->service_comments }}</div>                </div>

                        @endif                <div class="field-row">

                    </div>                    <strong>Service Time:</strong> 

                    <div class="field-row">                    <span class="{{ $visit->service_time ? 'status-yes' : 'status-no' }}">

                        <strong>Product Quality:</strong>                         {{ $visit->service_time ? '✓ Yes' : '✗ No' }}

                        <span class="{{ $visit->product_quality ? 'status-yes' : 'status-no' }}">                    </span>

                            {{ $visit->product_quality ? '✓ Yes' : '✗ No' }}                    <div class="comment">{{ $visit->service_time_comments ?? '' }}</div>

                        </span>                </div>

                        @if($visit->quality_comments)                <div class="field-row">

                        <div class="comment">{{ $visit->quality_comments }}</div>                    <strong>Dine In:</strong> 

                        @endif                    <span class="{{ $visit->dine_in ? 'status-yes' : 'status-no' }}">

                    </div>                        {{ $visit->dine_in ? '✓ Yes' : '✗ No' }}

                </div>                    </span>

            </div>                    <div class="comment">{{ $visit->dine_in_comments ?? '' }}</div>

                </div>

            <!-- Section 5: Financials -->                <div class="field-row">

            <div class="section bg-purple">                    <strong>Take Out:</strong> 

                <div class="section-title">💼 Financials</div>                    <span class="{{ $visit->take_out ? 'status-yes' : 'status-no' }}">

                <div class="grid-2">                        {{ $visit->take_out ? '✓ Yes' : '✗ No' }}

                    <div class="field-row">                    </span>

                        <strong>Weekly Schedule:</strong>                     <div class="comment">{{ $visit->take_out_comments ?? '' }}</div>

                        <span class="{{ $visit->weekly_schedule ? 'status-yes' : 'status-no' }}">                </div>

                            {{ $visit->weekly_schedule ? '✓ Yes' : '✗ No' }}                <div class="field-row">

                        </span>                    <strong>Family:</strong> 

                        @if($visit->schedule_comments)                    <span class="{{ $visit->family ? 'status-yes' : 'status-no' }}">

                        <div class="comment">{{ $visit->schedule_comments }}</div>                        {{ $visit->family ? '✓ Yes' : '✗ No' }}

                        @endif                    </span>

                    </div>                    <div class="comment">{{ $visit->family_comments ?? '' }}</div>

                    <div class="field-row">                </div>

                        <strong>MOD Financial Goal:</strong>                 <div class="field-row">

                        <span class="{{ $visit->mod_financial_goal ? 'status-yes' : 'status-no' }}">                    <strong>Delivery:</strong> 

                            {{ $visit->mod_financial_goal ? '✓ Yes' : '✗ No' }}                    <span class="{{ $visit->delivery ? 'status-yes' : 'status-no' }}">

                        </span>                        {{ $visit->delivery ? '✓ Yes' : '✗ No' }}

                        @if($visit->financial_comments)                    </span>

                        <div class="comment">{{ $visit->financial_comments }}</div>                    <div class="comment">{{ $visit->delivery_comments ?? '' }}</div>

                        @endif                </div>

                    </div>                <div class="field-row">

                </div>                    <strong>Drive Thru:</strong> 

            </div>                    <span class="{{ $visit->drive_thru ? 'status-yes' : 'status-no' }}">

                        {{ $visit->drive_thru ? '✓ Yes' : '✗ No' }}

            <!-- Section 6: Quality / Pathing -->                    </span>

            <div class="section bg-orange">                    <div class="comment">{{ $visit->drive_thru_comments ?? '' }}</div>

                <div class="section-title">🔧 Quality / Pathing</div>                </div>

                <div class="grid-2">            </div>

                    <div class="field-row">        </div>

                        <strong>TTF Followed:</strong> 

                        <span class="{{ $visit->ttf_followed ? 'status-yes' : 'status-no' }}">        <div class="section bg-purple">

                            {{ $visit->ttf_followed ? '✓ Yes' : '✗ No' }}            <div class="section-title">💼 Financials</div>

                        </span>            <div class="grid-2">

                        @if($visit->ttf_comments)                <div class="field-row">

                        <div class="comment">{{ $visit->ttf_comments }}</div>                    <strong>Weekly Schedule & Overtime:</strong> 

                        @endif                    <span class="{{ $visit->weekly_schedule ? 'status-yes' : 'status-no' }}">

                    </div>                        {{ $visit->weekly_schedule ? '✓ Yes' : '✗ No' }}

                    <div class="field-row">                    </span>

                        <strong>Equipment Working:</strong>                     <div class="comment">{{ $visit->schedule_comments ?? '' }}</div>

                        <span class="{{ $visit->equipment_working ? 'status-yes' : 'status-no' }}">                </div>

                            {{ $visit->equipment_working ? '✓ Yes' : '✗ No' }}                <div class="field-row">

                        </span>                    <strong>MOD Financial Goal:</strong> 

                        @if($visit->equipment_comments)                    <span class="{{ $visit->mod_financial_goal ? 'status-yes' : 'status-no' }}">

                        <div class="comment">{{ $visit->equipment_comments }}</div>                        {{ $visit->mod_financial_goal ? '✓ Yes' : '✗ No' }}

                        @endif                    </span>

                    </div>                    <div class="comment">{{ $visit->financial_comments ?? '' }}</div>

                </div>                </div>

            </div>                <div class="field-row">

                    <strong>Sales (Cashier Objectives):</strong> 

            <!-- Section 7: Cleanliness -->                    <span class="{{ $visit->sales_objectives ? 'status-yes' : 'status-no' }}">

            <div class="section bg-teal">                        {{ $visit->sales_objectives ? '✓ Yes' : '✗ No' }}

                <div class="section-title">🧽 Cleanliness</div>                    </span>

                <div class="grid-2">                    <div class="comment">{{ $visit->sales_comments ?? '' }}</div>

                    <div class="field-row">                </div>

                        <strong>Dining Area Clean:</strong>                 <div class="field-row">

                        <span class="{{ $visit->dining_area_clean ? 'status-yes' : 'status-no' }}">                    <strong>Cash Policies Spot Check:</strong> 

                            {{ $visit->dining_area_clean ? '✓ Yes' : '✗ No' }}                    <span class="{{ $visit->cash_policies ? 'status-yes' : 'status-no' }}">

                        </span>                        {{ $visit->cash_policies ? '✓ Yes' : '✗ No' }}

                        @if($visit->dining_comments)                    </span>

                        <div class="comment">{{ $visit->dining_comments }}</div>                    <div class="comment">{{ $visit->cash_comments ?? '' }}</div>

                        @endif                </div>

                    </div>                <div class="field-row">

                    <div class="field-row">                    <strong>Daily Waste Followed:</strong> 

                        <strong>Restroom Clean:</strong>                     <span class="{{ $visit->daily_waste ? 'status-yes' : 'status-no' }}">

                        <span class="{{ $visit->restroom_clean ? 'status-yes' : 'status-no' }}">                        {{ $visit->daily_waste ? '✓ Yes' : '✗ No' }}

                            {{ $visit->restroom_clean ? '✓ Yes' : '✗ No' }}                    </span>

                        </span>                    <div class="comment">{{ $visit->waste_comments ?? '' }}</div>

                        @if($visit->restroom_comments)                </div>

                        <div class="comment">{{ $visit->restroom_comments }}</div>            </div>

                        @endif        </div>

                    </div>

                </div>        <div class="section bg-orange">

            </div>            <div class="section-title">🔧 Quality / Pathing</div>

            <div class="grid-2">

            <!-- Section 8: Follow-Up from Last Visit -->                <div class="field-row">

            @if($visit->last_visit_date || $visit->last_visit_summary || $visit->last_visit_update || $visit->other_follow_up)                    <strong>TTF Followed:</strong> 

            <div class="section bg-gray">                    <span class="{{ $visit->ttf_followed ? 'status-yes' : 'status-no' }}">

                <div class="section-title">📅 Follow-Up from Last Visit</div>                        {{ $visit->ttf_followed ? '✓ Yes' : '✗ No' }}

                @if($visit->last_visit_date)                    </span>

                <div class="text-field">                    <div class="comment">{{ $visit->ttf_comments ?? '' }}</div>

                    <span class="text-label">Last Visit Date</span>                </div>

                    <div class="text-value">{{ $visit->last_visit_date }}</div>                <div class="field-row">

                </div>                    <strong>Sandwich Assembly:</strong> 

                @endif                    <span class="{{ $visit->sandwich_assembly ? 'status-yes' : 'status-no' }}">

                @if($visit->last_visit_summary)                        {{ $visit->sandwich_assembly ? '✓ Yes' : '✗ No' }}

                <div class="text-field">                    </span>

                    <span class="text-label">Summary of Last Visit</span>                    <div class="comment">{{ $visit->sandwich_comments ?? '' }}</div>

                    <div class="text-value">{{ $visit->last_visit_summary }}</div>                </div>

                </div>                <div class="field-row">

                @endif                    <strong>QSC Completed:</strong> 

                @if($visit->last_visit_update)                    <span class="{{ $visit->qsc_completed ? 'status-yes' : 'status-no' }}">

                <div class="text-field">                        {{ $visit->qsc_completed ? '✓ Yes' : '✗ No' }}

                    <span class="text-label">Update on Last Visit</span>                    </span>

                    <div class="text-value">{{ $visit->last_visit_update }}</div>                    <div class="comment">{{ $visit->qsc_comments ?? '' }}</div>

                </div>                </div>

                @endif                <div class="field-row">

                @if($visit->other_follow_up)                    <strong>Oil/Shortening Standards:</strong> 

                <div class="text-field">                    <span class="{{ $visit->oil_standards ? 'status-yes' : 'status-no' }}">

                    <span class="text-label">Other Follow-Up</span>                        {{ $visit->oil_standards ? '✓ Yes' : '✗ No' }}

                    <div class="text-value">{{ $visit->other_follow_up }}</div>                    </span>

                </div>                    <div class="comment">{{ $visit->oil_comments ?? '' }}</div>

                @endif                </div>

            </div>                <div class="field-row">

            @endif                    <strong>Day Labels Updated:</strong> 

                    <span class="{{ $visit->day_labels ? 'status-yes' : 'status-no' }}">

            <!-- Section 9: Observation Summary -->                        {{ $visit->day_labels ? '✓ Yes' : '✗ No' }}

            @if($visit->what_did_you_see || $visit->why_had_issue || $visit->how_to_improve || $visit->who_when_responsible)                    </span>

            <div class="section bg-indigo">                    <div class="comment">{{ $visit->labels_comments ?? '' }}</div>

                <div class="section-title">📊 Observation Summary</div>                </div>

                @if($visit->what_did_you_see)                <div class="field-row">

                <div class="text-field">                    <strong>Equipment Working:</strong> 

                    <span class="text-label">What did you see?</span>                    <span class="{{ $visit->equipment_working ? 'status-yes' : 'status-no' }}">

                    <div class="text-value">{{ $visit->what_did_you_see }}</div>                        {{ $visit->equipment_working ? '✓ Yes' : '✗ No' }}

                </div>                    </span>

                @endif                    <div class="comment">{{ $visit->equipment_comments ?? '' }}</div>

                @if($visit->why_had_issue)                </div>

                <div class="text-field">                <div class="field-row">

                    <span class="text-label">Why had an issue?</span>                    <strong>Fryer Basket Condition:</strong> 

                    <div class="text-value">{{ $visit->why_had_issue }}</div>                    <span class="{{ $visit->fryer_basket ? 'status-yes' : 'status-no' }}">

                </div>                        {{ $visit->fryer_basket ? '✓ Yes' : '✗ No' }}

                @endif                    </span>

                @if($visit->how_to_improve)                    <div class="comment">{{ $visit->fryer_comments ?? '' }}</div>

                <div class="text-field">                </div>

                    <span class="text-label">How to improve?</span>                <div class="field-row">

                    <div class="text-value">{{ $visit->how_to_improve }}</div>                    <strong>Vegetable Prep Standards:</strong> 

                </div>                    <span class="{{ $visit->vegetable_prep ? 'status-yes' : 'status-no' }}">

                @endif                        {{ $visit->vegetable_prep ? '✓ Yes' : '✗ No' }}

                @if($visit->who_when_responsible)                    </span>

                <div class="text-field">                    <div class="comment">{{ $visit->vegetable_comments ?? '' }}</div>

                    <span class="text-label">Who & When Responsible?</span>                </div>

                    <div class="text-value">{{ $visit->who_when_responsible }}</div>                <div class="field-row">

                </div>                    <strong>Employee Appearance:</strong> 

                @endif                    <span class="{{ $visit->employee_appearance ? 'status-yes' : 'status-no' }}">

            </div>                        {{ $visit->employee_appearance ? '✓ Yes' : '✗ No' }}

            @endif                    </span>

                    <div class="comment">{{ $visit->appearance_comments ?? '' }}</div>

            <!-- Section 10: General Comments -->                </div>

            @if($visit->general_comments)            </div>

            <div class="section bg-gray full-width">        </div>

                <div class="section-title">💬 General Comments</div>

                <div class="text-value" style="padding: 10px; background: white; border: 1px solid #e5e7eb; border-radius: 6px;">        <div class="section bg-teal">

                    {{ $visit->general_comments }}            <div class="section-title">🧹 Cleanliness</div>

                </div>            <div class="grid-2">

            </div>                <div class="field-row">

            @endif                    <strong>Equipment Wrapped & Hang:</strong> 

        </div><!-- End sections-grid -->                    <span class="{{ $visit->equipment_wrapped ? 'status-yes' : 'status-no' }}">

    </div>                        {{ $visit->equipment_wrapped ? '✓ Yes' : '✗ No' }}

</body>                    </span>

</html>                    <div class="comment">{{ $visit->wrapped_comments ?? '' }}</div>

                </div>
                <div class="field-row">
                    <strong>Compartment Sink Setup:</strong> 
                    <span class="{{ $visit->compartment_sink ? 'status-yes' : 'status-no' }}">
                        {{ $visit->compartment_sink ? '✓ Yes' : '✗ No' }}
                    </span>
                    <div class="comment">{{ $visit->sink_comments ?? '' }}</div>
                </div>
                <div class="field-row">
                    <strong>Sanitizer Standards:</strong> 
                    <span class="{{ $visit->sanitizer_standards ? 'status-yes' : 'status-no' }}">
                        {{ $visit->sanitizer_standards ? '✓ Yes' : '✗ No' }}
                    </span>
                    <div class="comment">{{ $visit->sanitizer_comments ?? '' }}</div>
                </div>
                <div class="field-row">
                    <strong>Dining Area Clean:</strong> 
                    <span class="{{ $visit->dining_area_clean ? 'status-yes' : 'status-no' }}">
                        {{ $visit->dining_area_clean ? '✓ Yes' : '✗ No' }}
                    </span>
                    <div class="comment">{{ $visit->dining_comments ?? '' }}</div>
                </div>
                <div class="field-row">
                    <strong>Restroom Clean:</strong> 
                    <span class="{{ $visit->restroom_clean ? 'status-yes' : 'status-no' }}">
                        {{ $visit->restroom_clean ? '✓ Yes' : '✗ No' }}
                    </span>
                    <div class="comment">{{ $visit->restroom_comments ?? '' }}</div>
                </div>
            </div>
        </div>

        <div class="section bg-gray">
            <div class="section-title">📅 Follow-Up from Last Visit</div>
            <div class="text-field">
                <span class="text-label">Last Visit Date</span>
                <div class="text-value">{{ $visit->last_visit_date ?? 'N/A' }}</div>
            </div>
            <div class="text-field">
                <span class="text-label">Summary of Last Visit</span>
                <div class="text-value">{{ $visit->last_visit_summary ?? 'N/A' }}</div>
            </div>
            <div class="text-field">
                <span class="text-label">Update on Last Visit</span>
                <div class="text-value">{{ $visit->last_visit_update ?? 'N/A' }}</div>
            </div>
            <div class="text-field">
                <span class="text-label">Other Follow-Up</span>
                <div class="text-value">{{ $visit->other_follow_up ?? 'N/A' }}</div>
            </div>
        </div>

        <div class="section bg-indigo">
            <div class="section-title">📊 Observation Summary</div>
            <div class="text-field">
                <span class="text-label">What did you see?</span>
                <div class="text-value">{{ $visit->what_did_you_see ?? 'N/A' }}</div>
            </div>
            <div class="text-field">
                <span class="text-label">Why had an issue?</span>
                <div class="text-value">{{ $visit->why_had_issue ?? 'N/A' }}</div>
            </div>
            <div class="text-field">
                <span class="text-label">How to improve?</span>
                <div class="text-value">{{ $visit->how_to_improve ?? 'N/A' }}</div>
            </div>
            <div class="text-field">
                <span class="text-label">Who & When Responsible?</span>
                <div class="text-value">{{ $visit->who_when_responsible ?? 'N/A' }}</div>
            </div>
        </div>

        <div class="section bg-gray full-width">
            <div class="section-title">💬 General Comments</div>
            <div class="text-value" style="padding: 10px; background: white; border: 1px solid #e5e7eb; border-radius: 6px;">
                {{ $visit->general_comments ?? 'N/A' }}
            </div>
        </div>
        </div><!-- End sections-grid -->
    </div>
</body>
</html>


