<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Force Logout Notification</title>
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 2px;
        }
        .header {
            background-color: #f97316;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-radius: 2px 2px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .content p {
            margin: 0 0 15px 0;
            font-size: 12px;
        }
        .info-box {
            background-color: #fff7ed;
            border-left: 4px solid #f97316;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 11px;
        }
        .info-box strong {
            color: #c2410c;
        }
        .warning-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-box p {
            margin: 5px 0;
            font-size: 11px;
            color: #92400e;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚪 Session Logout Notification</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            
            <p>This is to inform you that all your active sessions have been logged out by an administrator.</p>
            
            <div class="info-box">
                <p><strong>Action Performed:</strong> Force Logout All Sessions</p>
                <p><strong>Performed By:</strong> {{ $admin->name }} (Administrator)</p>
                <p><strong>Date & Time:</strong> {{ $timestamp }}</p>
                <p><strong>Sessions Logged Out:</strong> {{ $sessionCount }} {{ $sessionCount === 1 ? 'session' : 'sessions' }}</p>
                <p><strong>Your Account:</strong> {{ $user->email }}</p>
            </div>
            
            <p>All your active sessions across all devices have been terminated. You will need to log in again to access the system.</p>
            
            <div class="warning-box">
                <p><strong>⚠️ Important Security Notice:</strong></p>
                <p>If you did not request this action or believe this was done in error, please contact our support team immediately and change your password.</p>
            </div>
            
            <p>This action is typically performed for security reasons, such as:</p>
            <ul style="font-size: 11px; line-height: 1.8;">
                <li>Suspected unauthorized access</li>
                <li>Password reset or security update</li>
                <li>User request for security purposes</li>
                <li>Administrative security measures</li>
            </ul>
            
            <p style="margin-top: 20px;">You can log in again using your existing credentials. If you experience any issues, please contact our support team.</p>
            
            <p style="margin-top: 30px; font-size: 11px; color: #6b7280;">
                Best regards,<br>
                RISDA Odometer System<br>
                Security Team
            </p>
        </div>
        
        <div class="footer">
            <p>This is an automated security notification from RISDA Odometer System.</p>
            <p>Please do not reply to this email. For support, contact your system administrator.</p>
            <p>&copy; {{ date('Y') }} RISDA. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
