<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>CourtConnect Admin OTP</title>
    <style>
        body {
            background-color: #0F0F0F;
            color: #F5F5F0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: none;
        }
        .wrapper {
            background-color: #0F0F0F;
            padding: 48px 20px;
            width: 100%;
        }
        .card {
            background-color: #1A1A1A;
            border: 1px solid #2A2A2A;
            border-radius: 20px;
            margin: 0 auto;
            max-width: 560px;
            padding: 0;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #1A1A1A 0%, #141414 100%);
            border-bottom: 1px solid #2A2A2A;
            padding: 32px 40px 28px;
            text-align: center;
        }
        .logo-text {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #F5F5F0;
        }
        .logo-text span {
            color: #BFFF00;
        }
        .badge {
            display: inline-block;
            margin-top: 14px;
            background: rgba(191, 255, 0, 0.1);
            border: 1px solid rgba(191, 255, 0, 0.25);
            border-radius: 999px;
            color: #BFFF00;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 5px 14px;
        }
        .card-body {
            padding: 40px 40px 32px;
        }
        .greeting {
            font-size: 13px;
            color: #6B6B6B;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 600;
            margin: 0 0 8px;
        }
        h1 {
            color: #F5F5F0;
            font-size: 24px;
            font-weight: 800;
            margin: 0 0 16px;
            line-height: 1.2;
        }
        .desc {
            color: #9A9A9A;
            font-size: 14px;
            line-height: 1.65;
            margin: 0 0 32px;
        }
        .desc strong {
            color: #F5F5F0;
            font-weight: 600;
        }
        .otp-box {
            background: #0F0F0F;
            border: 1px solid #2A2A2A;
            border-radius: 14px;
            margin: 0 0 28px;
            padding: 28px 24px;
            text-align: center;
        }
        .otp-label {
            color: #6B6B6B;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin: 0 0 12px;
        }
        .otp-code {
            color: #BFFF00;
            font-size: 48px;
            font-weight: 800;
            letter-spacing: 0.18em;
            line-height: 1;
            font-family: 'Courier New', 'Lucida Console', monospace;
            margin: 0 0 12px;
        }
        .otp-expiry {
            color: #6B6B6B;
            font-size: 12px;
            margin: 0;
        }
        .otp-expiry strong {
            color: #FF5C3A;
        }
        .warning-box {
            background: rgba(255, 92, 58, 0.06);
            border: 1px solid rgba(255, 92, 58, 0.18);
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 28px;
        }
        .warning-box p {
            color: #9A9A9A;
            font-size: 13px;
            line-height: 1.55;
            margin: 0;
        }
        .warning-box p strong {
            color: #FF5C3A;
        }
        .divider {
            border: none;
            border-top: 1px solid #2A2A2A;
            margin: 0 0 24px;
        }
        .footer p {
            color: #4A4A4A;
            font-size: 12px;
            line-height: 1.6;
            text-align: center;
            margin: 0 0 6px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">

            <div class="card-header">
                <div class="logo-text">Court<span>Connect</span></div>
                <div class="badge">&#x1F512;&nbsp; Admin Security Code</div>
            </div>

            <div class="card-body">
                <p class="greeting">Admin Access</p>
                <h1>Your one-time verification code</h1>
                <p class="desc">
                    We received a login attempt for your admin account at
                    <strong>{{ $adminEmail }}</strong>.
                    Use the code below to complete sign-in.
                </p>

                <div class="otp-box">
                    <p class="otp-label">Verification Code</p>
                    <div class="otp-code">{{ $otp }}</div>
                    <p class="otp-expiry">Expires in <strong>10 minutes</strong></p>
                </div>

                <div class="warning-box">
                    <p>
                        <strong>Never share this code.</strong>
                        CourtConnect staff will never ask for your OTP by phone, chat, or email.
                        If you did not request this code, please ignore this email — your account remains secure.
                    </p>
                </div>

                <hr class="divider">

                <div class="footer">
                    <p>&copy; {{ date('Y') }} CourtConnect. All rights reserved.</p>
                    <p>This is an automated security email — please do not reply.</p>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
