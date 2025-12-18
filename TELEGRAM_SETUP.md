# Telegram Bot Setup Guide

This guide will help you set up Telegram notifications for new orders.

## Step 1: Create a Telegram Bot

1. Open Telegram and search for **@BotFather**
2. Send `/newbot` command
3. Follow the instructions to create your bot
4. BotFather will give you a **Bot Token** (looks like: `123456789:ABCdefGHIjklMNOpqrsTUVwxyz`)
5. Save this token - you'll need it for your `.env` file

## Step 2: Get Your Chat ID

1. Start a conversation with your new bot
2. Send any message to your bot (e.g., "Hello")
3. Open this URL in your browser (replace `YOUR_BOT_TOKEN` with your actual token):
   ```
   https://api.telegram.org/botYOUR_BOT_TOKEN/getUpdates
   ```
4. Look for `"chat":{"id":123456789}` in the response
5. Copy the chat ID number

Alternatively, you can use **@userinfobot** - it will tell you your chat ID when you message it.

## Step 3: Update .env File

Add these lines to your `.env` file:

```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_CHAT_ID=your_chat_id_here
```

Example:
```env
TELEGRAM_BOT_TOKEN=123456789:ABCdefGHIjklMNOpqrsTUVwxyz
TELEGRAM_CHAT_ID=5050399362
```

## Step 4: Clear Config Cache

After updating your `.env` file, run:

```bash
php artisan config:clear
php artisan config:cache
```

## Step 5: Test the Setup

1. Place a test order with KHQR payment
2. When payment is confirmed, you should receive a Telegram notification
3. Check `storage/logs/laravel.log` if notifications aren't working

## Notification Format

When a payment is received, you'll get a message like:

```
🎉 New Payment Received!

📦 Order #: ORD-20250101-ABC123
💰 Amount: $99.99
💳 Payment Method: KHQR
👤 Customer: John Doe
📧 Email: john@example.com
📱 Phone: +855 12 345 678
📍 Address: 123 Street, Phnom Penh
🏙️ City: Phnom Penh

📋 Items:
• Product Name x2 - $49.99
• Another Product x1 - $50.00

⏰ Order Date: 2025-01-01 12:00:00
✅ Status: Paid
```

## Troubleshooting

### Not receiving notifications?

1. **Check .env file** - Make sure `TELEGRAM_BOT_TOKEN` and `TELEGRAM_CHAT_ID` are set correctly
2. **Check Laravel logs** - Look in `storage/logs/laravel.log` for error messages
3. **Verify bot token** - Make sure you copied the full token from BotFather
4. **Verify chat ID** - Make sure you're using the correct chat ID (not the bot's ID)
5. **Test manually** - Try sending a message to your bot to make sure it's working

### Common Errors

- **"Telegram bot token or chat ID not configured"** - Check your `.env` file
- **"Unauthorized"** - Your bot token is incorrect
- **"Chat not found"** - Your chat ID is incorrect or you haven't started a conversation with the bot

## Webhook (Optional)

If you want to receive commands from Telegram (not just send notifications), you can set up a webhook:

1. Get your webhook URL: `https://yourdomain.com/telegram/webhook`
2. Set webhook using BotFather: `/setwebhook` then paste your URL

Note: This is optional - notifications will work without webhooks.

