<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Locked Notification</title>
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
            background-color: #dc2626;
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
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 11px;
        }
        .info-box strong {
            color: #991b1b;
        }
        .reason-box {
            background-color: #fff7ed;
            border: 2px solid #f97316;
            padding: 15px;
            margin: 20px 0;
            border-radius: 2px;
        }
        .reason-box p {
            margin: 5px 0;
            font-size: 11px;
            color: #9a3412;
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
            <h1>🔒 Account Locked Notification</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            
            <p>This is to inform you that your account has been locked by an administrator and you will not be able to access the system until it is unlocked.</p>
            
            <div class="info-box">
                <p><strong>Action Performed:</strong> Account Locked</p>
                <p><strong>Performed By:</strong> {{ $admin->name }} (Administrator)</p>
                <p><strong>Date & Time:</strong> {{ $timestamp }}</p>
                <p><strong>Your Account:</strong> {{ $user->email }}</p>
                <p><strong>Account Status:</strong> Locked (Tidak Aktif)</p>
            </div>
            
            @if($reason)
            <div class="reason-box">
                <p><strong>Reason for Account Lock:</strong></p>
                <p>{{ $reason }}</p>
            </div>
            @endif
            
            <p><strong>What this means:</strong></p>
            <ul style="font-size: 11px; line-height: 1.8;">
                <li>You cannot log in to the system</li>
                <li>All your active sessions have been terminated</li>
                <li>Your account access is suspended</li>
                <li>You need administrator approval to regain access</li>
            </ul>
            
            <div class="warning-box">
                <p><strong>⚠️ Important Notice:</strong></p>
                <p>If you believe this action was taken in error or if you have questions about why your account was locked, please contact your system administrator or support team immediately.</p>
            </div>
            
            <p><strong>To regain access to your account:</strong></p>
            <ol style="font-size: 11px; line-height: 1.8;">
                <li>Contact your system administrator</li>
                <li>Provide your account details ({{ $user->email }})</li>
                <li>Discuss the reason for the lock</li>
                <li>Request account unlock if appropriate</li>
            </ol>
            
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
