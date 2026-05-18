<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #8b5cf6; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border-radius: 0 0 8px 8px; }
        .info-box { background: #ede9fe; padding: 15px; border-radius: 6px; margin: 15px 0; }
        .info-label { font-weight: bold; color: #666; }
        .footer { margin-top: 20px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>
                @if($alertType === 'time_in')
                    Duty Session Started
                @elseif($alertType === 'time_out')
                    Duty Session Ended
                @elseif($alertType === 'reminder')
                    Duty Session Reminder
                @else
                    Duty Session Update
                @endif
            </h2>
        </div>
        <div class="content">
            <p>Dear {{ $name }},</p>
            <p>{{ $message }}</p>

            @if($sessionDetails)
            <div class="info-box">
                <p><span class="info-label">Session Details:</span></p>
                <p>{{ $sessionDetails }}</p>
            </div>
            @endif

            <div class="footer">
                <p>This is an automated notification from the NSRC Attendance Management System.</p>
                <p>Timestamp: {{ now()->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
