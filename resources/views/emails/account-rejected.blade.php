<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #ef4444; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border-radius: 0 0 8px 8px; }
        .reason-box { background: #fee2e2; border-left: 4px solid #ef4444; padding: 15px; margin: 15px 0; }
        .footer { margin-top: 20px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Account Registration Update</h2>
        </div>
        <div class="content">
            <p>Dear {{ $name }},</p>
            <p>We regret to inform you that your NSRC AMS account registration has not been approved at this time.</p>

            @if($reason)
            <div class="reason-box">
                <strong>Reason:</strong> {{ $reason }}
            </div>
            @endif

            <p>If you believe this is an error or would like to reapply, please contact the system administrator for further assistance.</p>

            <div class="footer">
                <p>This is an automated notification from the NSRC Attendance Management System.</p>
            </div>
        </div>
    </div>
</body>
</html>
