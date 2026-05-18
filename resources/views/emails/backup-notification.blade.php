<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: {{ $success ? '#10b981' : '#ef4444' }}; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border-radius: 0 0 8px 8px; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .info-label { font-weight: bold; color: #666; }
        .summary-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .summary-table th, .summary-table td { padding: 8px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        .summary-table th { background: #f3f4f6; }
        .footer { margin-top: 20px; font-size: 12px; color: #999; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-error { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Backup {{ $success ? 'Completed' : 'Failed' }}</h2>
        </div>
        <div class="content">
            <p>A {{ $type }} backup has {{ $success ? 'completed successfully' : 'failed' }}.</p>

            <div class="info-row">
                <span class="info-label">Type:</span>
                <span>{{ ucfirst($type) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="badge {{ $success ? 'badge-success' : 'badge-error' }}">{{ $success ? 'Success' : 'Failed' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Filename:</span>
                <span>{{ $filename ?: 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Size:</span>
                <span>{{ $size ?: 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Details:</span>
                <span>{{ $details }}</span>
            </div>

            @if(!empty($summary))
            <h3>Backup Summary</h3>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Count</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['count'] }}</td>
                        <td>{{ $item['status'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            <div class="footer">
                <p>This is an automated notification from the NSRC Attendance Management System.</p>
                <p>Timestamp: {{ now()->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
