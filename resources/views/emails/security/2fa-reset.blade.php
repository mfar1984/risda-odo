<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Reset Notification</title>
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
            background-color: #2563eb;
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
            background-color: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 11px;
        }
        .info-box strong {
            color: #1e40af;
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
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            border-radius: 2px;
            font-size: 12px;
            font-weight: 500;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 2FA Reset Notification</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            
            <p>This is to inform you that your Two-Factor Authentication (2FA) has been reset by an administrator.</p>
            
            <div class="info-box">
                <p><strong>Action Performed:</strong> 2FA Reset</p>
                <p><strong>Performed By:</strong> {{ $admin->name }} (Administrator)</p>
                <p><strong>Date & Time:</strong> {{ $timestamp }}</p>
                <p><strong>Your Account:</strong> {{ $user->email }}</p>
            </div>
            
            <p>Your Two-Factor Authentication has been disabled. You can set up 2FA again from your account settings if you wish to re-enable this security feature.</p>
            
            <div class="warning-box">
                <p><strong>⚠️ Important Security Notice:</strong></p>
                <p>If you did not request this action or believe this was done in error, please contact our support team immediately.</p>
            </div>
            
            <p>For security reasons, we recommend:</p>
            <ul style="font-size: 11px; line-height: 1.8;">
                <li>Review your recent account activity</li>
                <li>Consider re-enabling 2FA for enhanced security</li>
                <li>Contact support if you have any concerns</li>
            </ul>
            
            <p style="margin-top: 20px;">If you have any questions or concerns, please don't hesitate to contact our support team.</p>
            
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
