<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Temperature Tracking Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2563eb;
        }
        
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        
        .meta-info {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        
        .meta-info div {
            flex: 1;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        
        td {
            font-size: 9px;
        }
        
        .status {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .status-draft {
            background-color: #f3f4f6;
            color: #374151;
        }
        
        .status-submitted {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-reviewed {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @page {
            margin: 1cm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Temperature Tracking Report</h1>
        <p>Quality Management System - Food Safety Compliance</p>
        <p>Generated on {{ date('F j, Y \a\t g:i A') }}</p>
    </div>
    
    <div class="meta-info">
        <div>
            <strong>Report Summary:</strong><br>
            Total Records: {{ $forms->count() }}<br>
            Draft: {{ $forms->where('status', 'Draft')->count() }}<br>
            Submitted: {{ $forms->where('status', 'Submitted')->count() }}<br>
            Reviewed: {{ $forms->where('status', 'Reviewed')->count() }}
        </div>
        <div style="text-align: right;">
            <strong>Export Details:</strong><br>
            Format: PDF Report<br>
            Date Range: {{ request('date_from', 'All') }} - {{ request('date_to', 'All') }}<br>
            Store Filter: {{ $stores->where('id', request('store_id'))->first()->name ?? 'All Stores' }}
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Date</th>
                <th style="width: 18%;">Store Location</th>
                <th style="width: 8%;">Shift</th>
                <th style="width: 15%;">MIC Morning</th>
                <th style="width: 15%;">MIC Evening</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 12%;">Created By</th>
                <th style="width: 10%;">Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse($forms as $form)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($form->date)->format('M j, Y') }}</td>
                    <td>{{ $form->store->name ?? 'N/A' }}</td>
                    <td style="text-transform: capitalize;">{{ $form->shift ?? 'N/A' }}</td>
                    <td>{{ $form->mic_morning ?? 'N/A' }}</td>
                    <td>{{ $form->mic_evening ?? 'N/A' }}</td>
                    <td>
                        <span class="status status-{{ strtolower($form->status) }}">
                            {{ $form->status }}
                        </span>
                    </td>
                    <td>{{ $form->creator->name ?? 'Unknown' }}</td>
                    <td>{{ \Carbon\Carbon::parse($form->created_at)->format('M j, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #666;">
                        No temperature tracking forms found for the selected criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($forms->count() > 0)
        <div style="margin-top: 30px;">
            <h3 style="color: #2563eb; margin-bottom: 15px;">Compliance Summary</h3>
            <table style="width: 50%;">
                <tr>
                    <td><strong>Compliance Rate:</strong></td>
                    <td>{{ $forms->count() > 0 ? round(($forms->where('status', 'Reviewed')->count() / $forms->count()) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td><strong>Pending Review:</strong></td>
                    <td>{{ $forms->where('status', 'Submitted')->count() }} forms</td>
                </tr>
                <tr>
                    <td><strong>Draft Forms:</strong></td>
                    <td>{{ $forms->where('status', 'Draft')->count() }} forms</td>
                </tr>
                <tr>
                    <td><strong>Date Range:</strong></td>
                    <td>{{ $forms->min('date') }} to {{ $forms->max('date') }}</td>
                </tr>
            </table>
        </div>
    @endif
    
    <div class="footer">
        <p>
            <strong>Temperature Tracking Report</strong> | 
            Quality Management System | 
            Generated {{ date('Y-m-d H:i:s') }} | 
            Page 1 of 1
        </p>
        <p style="margin-top: 10px; font-size: 8px;">
            This report contains sensitive operational data. Please handle confidentially and in accordance with company policies.
        </p>
    </div>
</body>
</html>