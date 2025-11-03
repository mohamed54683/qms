<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QSC Checklists Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 4px; }
        th { background: #f5f5f5; }
        h1 { font-size: 18px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Q.S.C Checklists Export</h1>
    <p>Generated at: {{ $generated_at }}</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Store</th>
                <th>Date</th>
                <th>MOD</th>
                <th>Time</th>
                <th>Status</th>
                <th>Manager</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
        @foreach($checklists as $i => $c)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $c->store_name }}</td>
                <td>{{ $c->date }}</td>
                <td>{{ $c->mod }}</td>
                <td>{{ $c->time_option }}</td>
                <td>{{ $c->status }}</td>
                <td>{{ $c->manager_signature }}</td>
                <td>{{ $c->compliance_score }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>