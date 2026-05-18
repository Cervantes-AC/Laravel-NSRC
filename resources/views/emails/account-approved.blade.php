<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10b981; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; padding: 12px 24px; background: #10b981; color: white; text-decoration: none; border-radius: 6px; margin-top: 15px; }
        .footer { margin-top: 20px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Account Approved!</h2>
        </div>
        <div class="content">
            <p>Dear {{ $name }},</p>
            <p>Great news! Your NSRC AMS account has been approved by the administrator.</p>
            <p>You can now log in to the system and start using all the features available to you.</p>

            <a href="{{ $loginUrl }}" class="button">Login Now</a>

            <div class="footer">
                <p>If you have any questions or need assistance, please contact the system administrator.</p>
                <p>This is an automated notification from the NSRC Attendance Management System.</p>
            </div>
        </div>
    </div>
</body>
</html>
