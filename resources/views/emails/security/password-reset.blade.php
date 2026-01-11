<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Notification</title>
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
            background-color: #3b82f6;
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
            border-left: 4px solid #3b82f6;
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
        .password-box {
            background-color: #f0fdf4;
            border: 2px solid #22c55e;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            border-radius: 2px;
        }
        .password-box p {
            margin: 5px 0;
            font-size: 11px;
            color: #166534;
        }
        .password-box .password {
            font-size: 18px;
            font-weight: 700;
            color: #15803d;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            margin: 10px 0;
        }
        .warning-box {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-box p {
            margin: 5px 0;
            font-size: 11px;
            color: #991b1b;
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
            <h1>🔑 Password Reset Notification</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            
            <p>This is to inform you that your password has been changed by an administrator.</p>
            
            <div class="info-box">
                <p><strong>Action Performed:</strong> Password Reset</p>
                <p><strong>Performed By:</strong> {{ $admin->name }} (Administrator)</p>
                <p><strong>Date & Time:</strong> {{ $timestamp }}</p>
                <p><strong>Your Account:</strong> {{ $user->email }}</p>
            </div>
            
            <p><strong>Your new temporary password is:</strong></p>
            
            <div class="password-box">
                <p>New Password:</p>
                <div class="password">{{ $newPassword }}</div>
                <p style="margin-top: 10px; font-size: 10px;">Please copy this password and keep it secure</p>
            </div>
            
            <div class="warning-box">
                <p><strong>🔒 Important Security Actions Required:</strong></p>
                <p>1. All your active sessions have been logged out for security</p>
                <p>2. Please log in using the new password above</p>
                <p>3. <strong>Change this password immediately</strong> after logging in</p>
                <p>4. If you did not request this change, contact support immediately</p>
            </div>
            
            <p><strong>How to change your password:</strong></p>
            <ol style="font-size: 11px; line-height: 1.8;">
                <li>Log in using the new password provided above</li>
                <li>Go to your Profile settings</li>
                <li>Click on "Change Password"</li>
                <li>Enter a strong, unique password</li>
            </ol>
            
            <p style="margin-top: 20px;"><strong>Password Security Tips:</strong></p>
            <ul style="font-size: 11px; line-height: 1.8;">
                <li>Use at least 8 characters</li>
                <li>Include uppercase and lowercase letters</li>
                <li>Include numbers and special characters</li>
                <li>Don't reuse passwords from other accounts</li>
                <li>Don't share your password with anyone</li>
            </ul>
            
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
