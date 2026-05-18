<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: {{ $overallSuccess ? '#10b981' : '#f59e0b' }}; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border-radius: 0 0 8px 8px; }
        .stat { display: inline-block; margin: 10px 20px 10px 0; }
        .stat-value { font-size: 24px; font-weight: bold; }
        .stat-label { font-size: 12px; color: #666; }
        .footer { margin-top: 20px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Import Complete</h2>
        </div>
        <div class="content">
            <p>The import process for <strong>{{ $filename }}</strong> has completed.</p>

            <div>
                <div class="stat">
                    <div class="stat-value">{{ $total }}</div>
                    <div class="stat-label">Total Rows</div>
                </div>
                <div class="stat">
                    <div class="stat-value" style="color: #10b981">{{ $success }}</div>
                    <div class="stat-label">Imported</div>
                </div>
                <div class="stat">
                    <div class="stat-value" style="color: #ef4444">{{ $failed }}</div>
                    <div class="stat-label">Failed</div>
                </div>
                <div class="stat">
                    <div class="stat-value" style="color: #6b7280">{{ $skipped }}</div>
                    <div class="stat-label">Skipped (Duplicates)</div>
                </div>
            </div>

            @if($failed > 0)
            <p style="color: #ef4444; font-weight: bold;">Some records failed to import. Check the application logs for details.</p>
            @endif

            <div class="footer">
                <p>This is an automated notification from the NSRC Attendance Management System.</p>
            </div>
        </div>
    </div>
</body>
</html>
