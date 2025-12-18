<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Email</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #000;">Test Email</h1>
        <p>This is a test email from your Laravel application.</p>
        <p>If you received this email, your SMTP configuration is working correctly!</p>
        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="color: #666; font-size: 12px;">
            Sent from: {{ config('app.name') }}<br>
            Time: {{ now()->format('Y-m-d H:i:s') }}
        </p>
    </div>
</body>
</html>

