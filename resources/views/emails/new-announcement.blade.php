<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f59e0b; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border-radius: 0 0 8px 8px; }
        .priority-badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .priority-high { background: #fee2e2; color: #991b1b; }
        .priority-normal { background: #fef3c7; color: #92400e; }
        .priority-low { background: #d1fae5; color: #065f46; }
        .announcement-content { background: white; padding: 15px; border-radius: 6px; margin: 15px 0; border-left: 4px solid #f59e0b; }
        .footer { margin-top: 20px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Announcement</h2>
        </div>
        <div class="content">
            <p>Dear {{ $name }},</p>
            <p>A new announcement has been posted in the NSRC Attendance Management System.</p>

            <div class="info-row">
                <span class="info-label">Title:</span>
                <span>{{ $title }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Priority:</span>
                <span class="priority-badge priority-{{ $priority }}">{{ ucfirst($priority) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Posted:</span>
                <span>{{ $createdAt }}</span>
            </div>

            <div class="announcement-content">
                <p>{{ $content }}</p>
            </div>

            <div class="footer">
                <p>Log in to the system to view more details and respond if required.</p>
                <p>This is an automated notification from the NSRC Attendance Management System.</p>
            </div>
        </div>
    </div>
</body>
</html>
