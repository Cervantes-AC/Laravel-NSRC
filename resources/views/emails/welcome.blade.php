<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3b82f6; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border-radius: 0 0 8px 8px; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .info-label { font-weight: bold; color: #666; }
        .button { display: inline-block; padding: 12px 24px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; margin-top: 15px; }
        .footer { margin-top: 20px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Welcome to NSRC AMS!</h2>
        </div>
        <div class="content">
            <p>Dear {{ $name }},</p>
            <p>Welcome to the NSRC Attendance Management System! Your account has been successfully created.</p>

            <div class="info-row">
                <span class="info-label">Email:</span>
                <span>{{ $email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Role:</span>
                <span>{{ $role }}</span>
            </div>
            @if($password)
            <div class="info-row">
                <span class="info-label">Temporary Password:</span>
                <span>{{ $password }}</span>
            </div>
            <p><strong>Please change your password after your first login for security.</strong></p>
            @endif

            <p>You can now access the system using the button below:</p>
            <a href="{{ $loginUrl }}" class="button">Login to NSRC AMS</a>

            <div class="footer">
                <p>If you did not create this account, please contact the system administrator immediately.</p>
                <p>This is an automated notification from the NSRC Attendance Management System.</p>
            </div>
        </div>
    </div>
</body>
</html>
