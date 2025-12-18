# Deployment Guide for Laravel Application

## ⚠️ Important Note About Vercel

**Vercel does NOT natively support Laravel/PHP applications.** Vercel is designed for:
- Static sites
- Serverless functions (Node.js, Python, Go)
- Frontend frameworks (Next.js, React, Vue, etc.)

Laravel requires:
- PHP runtime
- Web server (Apache/Nginx)
- Persistent file storage
- Database connections
- Artisan commands

## ✅ Recommended Deployment Options for Laravel

### 1. **Railway** (Recommended - Easiest) ⭐

Railway is excellent for Laravel and very easy to set up.

**Steps:**
1. Go to [railway.app](https://railway.app)
2. Sign up with GitHub
3. Click "New Project" → "Deploy from GitHub repo"
4. Select your repository
5. Railway will auto-detect Laravel
6. Add environment variables from your `.env` file
7. Add a PostgreSQL or MySQL database (Railway provides this)
8. Deploy!

**Railway Configuration:**
- Automatically detects Laravel
- Runs `composer install`, `php artisan migrate`, etc.
- Provides database automatically
- Free tier available

---

### 2. **Render** (Great Alternative)

**Steps:**
1. Go to [render.com](https://render.com)
2. Sign up with GitHub
3. Click "New" → "Web Service"
4. Connect your GitHub repository
5. Configure:
   - **Build Command:** `composer install --no-dev && php artisan key:generate && php artisan migrate --force && npm install && npm run build`
   - **Start Command:** `php artisan serve --host=0.0.0.0 --port=$PORT`
   - **Environment:** PHP
6. Add environment variables
7. Add a PostgreSQL database (Render provides this)
8. Deploy!

**Render Configuration File (`render.yaml`):**
```yaml
services:
  - type: web
    name: laravel-app
    env: php
    buildCommand: composer install --no-dev && php artisan key:generate && php artisan migrate --force && npm install && npm run build
    startCommand: php artisan serve --host=0.0.0.0 --port=$PORT
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
      - key: LOG_CHANNEL
        value: stderr
```

---

### 3. **Fly.io** (Excellent for Laravel)

**Steps:**
1. Install Fly CLI: `curl -L https://fly.io/install.sh | sh`
2. Run: `fly launch`
3. Follow the prompts
4. Configure your app
5. Deploy: `fly deploy`

**Fly.io automatically detects Laravel and configures it!**

---

### 4. **Laravel Forge + DigitalOcean/Vultr** (Most Control)

Best for production applications that need full control.

1. Sign up at [forge.laravel.com](https://forge.laravel.com)
2. Connect your server provider (DigitalOcean, Vultr, etc.)
3. Create a server
4. Deploy your application
5. Configure domains, SSL, etc.

---

## 🚫 If You Really Want to Try Vercel (Not Recommended)

While Vercel doesn't support PHP, you could theoretically:

1. **Separate Frontend/Backend:**
   - Deploy Laravel API to Railway/Render
   - Deploy frontend (if you have one) to Vercel
   - Connect them via API

2. **Use Vercel Serverless Functions as Proxy:**
   - This is complex and not recommended
   - You'd need to proxy requests to a PHP server elsewhere
   - Adds latency and complexity

**We strongly recommend using Railway, Render, or Fly.io instead.**

---

## 📋 Pre-Deployment Checklist

Before deploying, make sure:

- [ ] `.env` file is configured for production
- [ ] `APP_DEBUG=false` in production
- [ ] Database credentials are set
- [ ] `APP_URL` is set to your production domain
- [ ] All environment variables are configured
- [ ] `storage` and `bootstrap/cache` directories are writable
- [ ] Run `php artisan config:cache` and `php artisan route:cache` for production
- [ ] Database migrations are ready
- [ ] Assets are built (`npm run build`)

---

## 🔧 Environment Variables to Set

Make sure to set these in your hosting platform:

```env
APP_NAME="Your App Name"
APP_ENV=production
APP_KEY=base64:... (generate with: php artisan key:generate)
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql (or pgsql)
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Add other service configurations (Telegram, KHQR, etc.)
```

---

## 🚀 Quick Start with Railway (Recommended)

1. **Push your code to GitHub** (if not already)
2. **Go to Railway.app** and sign up
3. **Click "New Project"** → **"Deploy from GitHub repo"**
4. **Select your repository**
5. **Add Environment Variables:**
   - Copy all variables from your `.env` file
   - Railway will auto-detect Laravel and run migrations
6. **Add Database:**
   - Click "New" → "Database" → Choose PostgreSQL or MySQL
   - Railway will automatically set `DB_HOST`, `DB_DATABASE`, etc.
7. **Deploy!**

Railway will automatically:
- Install dependencies
- Run migrations
- Build assets
- Start your application

---

## 📞 Need Help?

- **Railway Docs:** https://docs.railway.app
- **Render Docs:** https://render.com/docs
- **Fly.io Docs:** https://fly.io/docs
- **Laravel Deployment:** https://laravel.com/docs/deployment

---

## ⭐ Our Recommendation

**Use Railway** - It's the easiest, most Laravel-friendly option with:
- ✅ Automatic Laravel detection
- ✅ Free tier
- ✅ Easy database setup
- ✅ Simple environment variable management
- ✅ Automatic HTTPS
- ✅ Great documentation

