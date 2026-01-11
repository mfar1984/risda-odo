<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Unlocked Notification</title>
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
            background-color: #16a34a;
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
            background-color: #f0fdf4;
            border-left: 4px solid #16a34a;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 11px;
        }
        .info-box strong {
            color: #166534;
        }
        .success-box {
            background-color: #dcfce7;
            border: 2px solid #22c55e;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            border-radius: 2px;
        }
        .success-box p {
            margin: 5px 0;
            font-size: 13px;
            color: #166534;
            font-weight: 600;
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
            <h1>🔓 Account Unlocked Notification</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            
            <p>Good news! Your account has been unlocked by an administrator and you can now access the system again.</p>
            
            <div class="success-box">
                <p>✓ Your account is now active and accessible</p>
            </div>
            
            <div class="info-box">
                <p><strong>Action Performed:</strong> Account Unlocked</p>
                <p><strong>Performed By:</strong> {{ $admin->name }} (Administrator)</p>
                <p><strong>Date & Time:</strong> {{ $timestamp }}</p>
                <p><strong>Your Account:</strong> {{ $user->email }}</p>
                <p><strong>Account Status:</strong> Active (Aktif)</p>
            </div>
            
            <p><strong>What you can do now:</strong></p>
            <ul style="font-size: 11px; line-height: 1.8;">
                <li>Log in to the system using your existing credentials</li>
                <li>Access all features and functions as before</li>
                <li>Resume your normal activities</li>
            </ul>
            
            <p><strong>Important reminders:</strong></p>
            <ul style="font-size: 11px; line-height: 1.8;">
                <li>Use your existing username and password to log in</li>
                <li>If you've forgotten your password, contact your administrator</li>
                <li>Follow all system policies and guidelines</li>
                <li>Report any suspicious activity immediately</li>
            </ul>
            
            <p style="margin-top: 20px;">If you experience any issues logging in or accessing the system, please contact your system administrator or support team.</p>
            
            <p style="margin-top: 20px;"><strong>Support Contact Information:</strong></p>
            <p style="font-size: 11px; line-height: 1.8;">
                For assistance, please contact:<br>
                - Your system administrator<br>
                - RISDA IT Support Team<br>
                - Email: support@risda.gov.my
            </p>
            
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
