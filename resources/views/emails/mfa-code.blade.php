<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4f46e5; color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
        .content { background: #f9fafb; padding: 20px; border-radius: 0 0 8px 8px; }
        .code-box { background: #eef2ff; border: 2px dashed #4f46e5; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
        .code { font-size: 36px; font-weight: 700; letter-spacing: 8px; color: #4f46e5; font-family: 'Courier New', monospace; }
        .footer { margin-top: 20px; font-size: 12px; color: #999; text-align: center; }
        .warning { color: #6b7280; font-size: 13px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ config('app.name') }} - Verification Code</h2>
        </div>
        <div class="content">
            <p>Hello <strong>{{ $fullName }}</strong>,</p>
            <p>Your verification code is:</p>
            <div class="code-box">
                <span class="code">{{ $code }}</span>
            </div>
            <p class="warning">This code will expire in 10 minutes.</p>
            <p class="warning">If you did not request this code, please ignore this email.</p>
            <div class="footer">
                <p>{{ config('app.name') }} &mdash; {{ config('app.url') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
