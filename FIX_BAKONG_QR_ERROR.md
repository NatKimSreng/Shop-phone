# Fix Bakong QR "Network Error"

The "Network error. Please check your internet connection and try again." error in Bakong QR can be caused by several issues.

## Common Causes & Solutions

### 1. Missing Bakong API Token

**Problem:** The Bakong API token is not configured in Railway.

**Solution:** Set environment variable in Railway:

```env
BAKONG_API_TOKEN=your-bakong-api-token-here
BAKONG_TEST_MODE=true
```

Go to Railway Dashboard → Your Service → Variables → Add these variables.

### 2. CORS (Cross-Origin Resource Sharing) Issues

**Problem:** The frontend JavaScript can't make requests to your Railway backend.

**Solution:** Check if your Railway URL is accessible and CORS is configured.

**Check:**
- Is `APP_URL` set correctly in Railway?
- Can you access your Railway app in browser?
- Check browser console for CORS errors

### 3. Payment Status Route Not Accessible

**Problem:** The route `/payment/status/{order}` is failing.

**Check:**
1. Open browser DevTools (F12) → Network tab
2. Try to pay with Bakong QR
3. Look for failed requests to `/payment/status/{order}`
4. Check the error message

**Solution:** Verify the route exists and is accessible:
```bash
# Test the route
curl https://your-app.railway.app/payment/status/1
```

### 4. Bakong API Endpoint Unreachable

**Problem:** Railway can't reach Bakong's API servers.

**Possible causes:**
- Bakong API is down
- Network firewall blocking requests
- API endpoint changed

**Solution:**
- Check Bakong API status
- Verify API endpoint in the KHQR package
- Check Railway logs for API errors

### 5. JavaScript Fetch Error

**Problem:** The frontend JavaScript fetch is failing.

**Check the code in `resources/views/frontend/payment/khqr.blade.php`:**

```javascript
function checkPayment() {
    fetch("{{ route('payment.check', $order->id) }}")
        .then(r => r.json())
        .then(res => {
            if (res.paid === true) {
                location.reload();
            }
        })
        .catch(() => {}); // This silently ignores errors!
}
```

**Issue:** The `.catch()` is silently ignoring errors, so you don't see what's wrong.

**Fix:** Update the JavaScript to show errors:

```javascript
function checkPayment() {
    fetch("{{ route('payment.status', $order->id) }}")
        .then(r => {
            if (!r.ok) {
                throw new Error(`HTTP error! status: ${r.status}`);
            }
            return r.json();
        })
        .then(res => {
            if (res.paid === true) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Payment check failed:', error);
            // Optionally show error to user
            // alert('Network error: ' + error.message);
        });
}
```

## Quick Diagnostic Steps

### Step 1: Check Environment Variables

```bash
railway run php artisan tinker
```

Then:
```php
config('services.bakong.token'); // Should return your token
config('services.bakong.test_mode'); // Should return true/false
```

### Step 2: Check Railway Logs

```bash
railway logs
```

Look for:
- "KHQR payment check failed"
- "No API token configured"
- Network errors
- API timeout errors

### Step 3: Test Payment Status Route

Visit in browser (replace `1` with actual order ID):
```
https://your-app.railway.app/payment/status/1
```

Should return JSON:
```json
{"paid": false}
```

If you get an error, that's the problem.

### Step 4: Check Browser Console

1. Open payment page
2. Press F12 → Console tab
3. Look for JavaScript errors
4. Check Network tab for failed requests

## Fix: Update JavaScript Error Handling

I'll update the KHQR blade file to show better error messages.

## Temporary Workaround

If Bakong API is not working, you can use the manual "I Have Paid" button, which will check payment status when clicked.

## Still Not Working?

1. **Check Railway logs:**
   ```bash
   railway logs --tail
   ```

2. **Verify Bakong API token is valid:**
   - Contact Bakong support
   - Check if token has expired
   - Verify token has correct permissions

3. **Test locally first:**
   - Set up Bakong API token locally
   - Test QR code generation
   - If it works locally but not on Railway, it's a Railway configuration issue

4. **Check Bakong API documentation:**
   - Verify API endpoint hasn't changed
   - Check if there are new requirements
   - Verify your account status

