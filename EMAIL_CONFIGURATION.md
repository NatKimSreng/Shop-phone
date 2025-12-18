# Email Configuration - Brevo SMTP Setup

This guide will help you configure Laravel to send emails using Brevo (formerly Sendinblue) SMTP.

## Step 1: Update .env File

Add or update the following lines in your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=8a02eb001@smtp-brevo.com
MAIL_PASSWORD=your_brevo_smtp_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Step 2: Get Your Brevo SMTP Password

1. Log in to your Brevo account: https://app.brevo.com
2. Go to **Settings** → **SMTP & API**
3. Click on **SMTP** tab
4. Find your SMTP password (it's different from your login password)
5. Copy the SMTP password and paste it in your `.env` file as `MAIL_PASSWORD`

## Step 3: Update MAIL_FROM_ADDRESS

Replace `noreply@yourdomain.com` with your actual sender email address. This should be:
- An email address you own
- Verified in your Brevo account
- Or use the email associated with your Brevo account

## Step 4: Clear Config Cache

After updating your `.env` file, run:

```bash
php artisan config:clear
php artisan config:cache
```

## Step 5: Test Email Configuration

You can test your email configuration using Tinker:

```bash
php artisan tinker
```

Then run:

```php
use Illuminate\Support\Facades\Mail;

Mail::raw('This is a test email from Laravel!', function ($message) {
    $message->to('natkimsreng@gmail.com')
            ->subject('Test Email');
});
```

## Current Configuration

- **SMTP Server**: smtp-relay.brevo.com
- **Port**: 587
- **Encryption**: TLS
- **Username**: 8a02eb001@smtp-brevo.com
- **Password**: (Set in .env file)

## Troubleshooting

### Email not sending?

1. **Check your .env file** - Make sure all values are correct
2. **Verify SMTP password** - It's different from your Brevo login password
3. **Check sender email** - Must be verified in Brevo
4. **Clear cache**: `php artisan config:clear`
5. **Check Laravel logs**: `storage/logs/laravel.log`

### Common Errors

- **"Authentication failed"**: Check your SMTP password
- **"Connection timeout"**: Check firewall/network settings
- **"Sender address rejected"**: Verify sender email in Brevo

## Security Note

⚠️ **Never commit your `.env` file to version control!** It contains sensitive credentials.

## Next Steps

After configuration, you can:
- Send order confirmation emails
- Send password reset emails
- Send notification emails
- Create custom email templates

