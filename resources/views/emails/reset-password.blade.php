<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Reset Password | CourtConnect</title>
    <style>
        body {
            background-color: #05070f;
            color: #f8fafc;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: none;
        }
        .wrapper {
            background-color: #05070f;
            margin: 0;
            padding: 40px 20px;
            width: 100%;
        }
        .card {
            background-color: #0a0e1a;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            margin: 0 auto;
            max-width: 570px;
            padding: 40px;
            text-align: left;
        }
        .logo-wrap {
            margin-bottom: 24px;
            text-align: center;
        }
        .logo {
            display: inline-block;
            font-weight: 800;
            font-size: 20px;
            color: #f8fafc;
            text-decoration: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.02em;
        }
        .logo span {
            color: #10b981;
        }
        h1 {
            color: #f8fafc;
            font-size: 22px;
            font-weight: 800;
            margin-top: 0;
            text-align: left;
        }
        p {
            color: #94a3b8;
            font-size: 15px;
            line-height: 1.6;
            margin-top: 0;
        }
        .action-row {
            margin: 30px 0;
            text-align: center;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #10b981);
            border-radius: 12px;
            color: #ffffff !important;
            display: inline-block;
            font-size: 14px;
            font-weight: 700;
            padding: 14px 28px;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
        }
        .footer {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 12px;
            color: #64748b;
        }
        .footer a {
            color: #6366f1;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="logo-wrap">
                <a href="#" class="logo">Court<span>Connect</span></a>
            </div>
            
            <h1>Reset Your Password</h1>
            <p>Hello {{ $name }},</p>
            <p>You are receiving this email because we received a password reset request for your player account. Please click the button below to set a new password:</p>
            
            <div class="action-row">
                <a href="{{ $url }}" class="btn-primary">Reset Password</a>
            </div>
            
            <p>If you did not request a password reset, no further action is required and your account remains secure.</p>
            
            <div class="footer">
                <p>If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</p>
                <p style="word-break: break-all;"><a href="{{ $url }}">{{ $url }}</a></p>
                <p style="margin-top: 20px; font-size: 11px;">&copy; {{ date('Y') }} CourtConnect. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
