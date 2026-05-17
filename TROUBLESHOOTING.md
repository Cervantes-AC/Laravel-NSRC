# Troubleshooting Guide

## Common Issues and Solutions

### 1. "API key not configured" Error

**Problem:** Getting error message about missing API key

**Solutions:**

```bash
# Clear configuration cache
php artisan config:cache

# Clear all caches
php artisan cache:clear

# Verify .env file exists
ls -la .env

# Check API keys are set
grep GROQ_API_KEY .env
```

**Verify in code:**
```php
php artisan tinker
> env('GROQ_API_KEY_1')
> config('ai.provider')
```

---

### 2. Insights Not Generating

**Problem:** Clicking "Generate Insights" does nothing or shows error

**Checklist:**

1. **Check Internet Connection**
   - Verify you can access external websites
   - Test: `ping api.groq.com`

2. **Verify API Keys**
   - Check `.env` file has valid keys
   - Test keys directly with provider

3. **Check Application Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Verify Provider is Active**
   - Check `AI_PROVIDER` in `.env`
   - Verify provider dashboard shows active account

5. **Test with API**
   ```bash
   curl -X POST http://localhost/reports/insights \
     -H "Content-Type: application/json" \
     -H "X-CSRF-TOKEN: your_token" \
     -d '{"type": "user_activity"}'
   ```

---

### 3. Rate Limit Errors

**Problem:** Getting "rate limit exceeded" or similar errors

**Solutions:**

1. **Use UI Button**
   - Click "Switch to Alternate API Key" in sidebar
   - This uses the second API key for the provider

2. **Use API Endpoint**
   ```bash
   POST /reports/api-key/switch
   ```

3. **Switch Provider**
   - Click different provider button
   - Or use endpoint:
   ```bash
   POST /reports/provider/switch
   {"provider": "openrouter"}
   ```

4. **Wait and Retry**
   - Rate limits reset after time period
   - Check provider dashboard for reset time

---

### 4. Provider Not Switching

**Problem:** Provider button doesn't change or stays same

**Solutions:**

```bash
# Clear cache
php artisan config:cache
php artisan cache:clear

# Verify provider in config
php artisan tinker
> config('ai.provider')

# Check Livewire component
# Verify AIProviderSwitcher.php exists
ls -la app/Livewire/AIProviderSwitcher.php
```

**Check browser console:**
- Open DevTools (F12)
- Check Console tab for JavaScript errors
- Check Network tab for failed requests

---

### 5. "Invalid provider" Error

**Problem:** Error saying provider is invalid

**Solutions:**

1. **Check Valid Providers**
   - Only `groq` and `openrouter` are supported
   - Verify spelling in request

2. **Verify Request Format**
   ```json
   {
     "provider": "groq"
   }
   ```

3. **Check Configuration**
   ```php
   php artisan tinker
   > config('ai')
   ```

---

### 6. CSRF Token Errors

**Problem:** Getting "CSRF token mismatch" error

**Solutions:**

1. **Verify CSRF Token in HTML**
   ```blade
   <meta name="csrf-token" content="{{ csrf_token() }}">
   ```

2. **Include Token in Requests**
   ```javascript
   headers: {
     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
   }
   ```

3. **Check Session Configuration**
   - Verify `SESSION_DRIVER=database` in `.env`
   - Run migrations: `php artisan migrate`

---

### 7. Livewire Component Not Loading

**Problem:** Provider switcher not showing on page

**Solutions:**

1. **Verify Component Exists**
   ```bash
   ls -la app/Livewire/AIProviderSwitcher.php
   ```

2. **Check View Exists**
   ```bash
   ls -la resources/views/livewire/ai-provider-switcher.blade.php
   ```

3. **Verify Component Registration**
   - Livewire auto-discovers components
   - Clear cache: `php artisan cache:clear`

4. **Check Blade Syntax**
   ```blade
   <livewire:ai-provider-switcher />
   ```

5. **Verify Livewire is Installed**
   ```bash
   composer show | grep livewire
   ```

---

### 8. Database Session Errors

**Problem:** Session-related errors

**Solutions:**

```bash
# Create sessions table
php artisan session:table
php artisan migrate

# Clear sessions
php artisan session:clear

# Verify database connection
php artisan tinker
> DB::connection()->getPdo()
```

---

### 9. API Response Errors

**Problem:** Getting error responses from API

**Check Response:**
```bash
# Enable debug mode
APP_DEBUG=true

# Check logs
tail -f storage/logs/laravel.log

# Test API directly
curl -X POST http://localhost/reports/insights \
  -H "Content-Type: application/json" \
  -d '{"type": "user_activity"}' -v
```

**Common API Errors:**
- `401 Unauthorized` - Invalid API key
- `429 Too Many Requests` - Rate limit exceeded
- `500 Internal Server Error` - Provider issue
- `503 Service Unavailable` - Provider down

---

### 10. Configuration Not Updating

**Problem:** Changes to `.env` or `config/ai.php` not taking effect

**Solutions:**

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Recache configuration
php artisan config:cache

# Restart application (if using artisan serve)
# Stop and restart: php artisan serve
```

---

### 11. Insights Showing Old Data

**Problem:** Getting same insights repeatedly

**Solutions:**

1. **Clear Cache**
   ```bash
   php artisan cache:clear
   ```

2. **Check Timestamps**
   - Verify report data is current
   - Check date filters

3. **Verify New Data**
   - Generate new report
   - Check database for new records

---

### 12. Performance Issues

**Problem:** Insights generation is slow

**Solutions:**

1. **Check Network**
   - Verify internet speed
   - Check latency to API

2. **Monitor API**
   - Check provider dashboard
   - Verify no rate limiting

3. **Optimize Queries**
   - Check report generation time
   - Review database indexes

4. **Consider Caching**
   - Implement result caching
   - Cache for 1 hour

---

## Debugging Steps

### Step 1: Enable Debug Mode
```env
APP_DEBUG=true
```

### Step 2: Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Step 3: Test Configuration
```php
php artisan tinker
> config('ai')
> env('GROQ_API_KEY_1')
```

### Step 4: Test Service
```php
php artisan tinker
> $service = app(\App\Services\AIProviderService::class)
> $service->getProvider()
```

### Step 5: Test API
```bash
curl -X POST http://localhost/reports/insights \
  -H "Content-Type: application/json" \
  -d '{"type": "user_activity"}' -v
```

---

## Log Analysis

### Check Application Logs
```bash
# View recent logs
tail -n 50 storage/logs/laravel.log

# Follow logs in real-time
tail -f storage/logs/laravel.log

# Search for errors
grep -i error storage/logs/laravel.log

# Search for specific service
grep AIProviderService storage/logs/laravel.log
```

### Log Levels
- `DEBUG` - Detailed information
- `INFO` - General information
- `WARNING` - Warning messages
- `ERROR` - Error messages
- `CRITICAL` - Critical errors

---

## Testing Checklist

- [ ] `.env` file exists and has API keys
- [ ] `config/ai.php` exists
- [ ] `AIProviderService.php` exists
- [ ] `ReportService.php` updated
- [ ] `ReportsController.php` updated
- [ ] Routes added to `web.php`
- [ ] Livewire component exists
- [ ] Views exist
- [ ] Database migrations run
- [ ] Cache cleared
- [ ] Configuration cached

---

## Provider-Specific Issues

### Groq Issues

**Check Groq Status:**
- Dashboard: https://console.groq.com
- Status: https://status.groq.com

**Common Errors:**
- `401 Unauthorized` - Invalid API key
- `429 Too Many Requests` - Rate limit
- `500 Internal Server Error` - Groq issue

**Solutions:**
- Verify API key in `.env`
- Check rate limits in dashboard
- Try alternate API key
- Switch to OpenRouter

### OpenRouter Issues

**Check OpenRouter Status:**
- Dashboard: https://openrouter.ai
- Status: Check account page

**Common Errors:**
- `401 Unauthorized` - Invalid API key
- `429 Too Many Requests` - Rate limit
- `400 Bad Request` - Invalid request

**Solutions:**
- Verify API key in `.env`
- Check rate limits in dashboard
- Try alternate API key
- Switch to Groq

---

## Getting Help

### Information to Provide

When reporting issues, include:

1. **Error Message**
   - Full error text
   - Stack trace if available

2. **Configuration**
   - Laravel version
   - PHP version
   - Livewire version

3. **Steps to Reproduce**
   - What you did
   - What happened
   - What you expected

4. **Logs**
   - Recent log entries
   - Error logs

5. **Environment**
   - OS (Windows, Linux, Mac)
   - Browser (if UI issue)
   - Network (if API issue)

### Resources

- **Laravel Docs:** https://laravel.com/docs
- **Livewire Docs:** https://livewire.laravel.com
- **Groq Docs:** https://console.groq.com/docs
- **OpenRouter Docs:** https://openrouter.ai/docs

---

## Quick Reference

### Clear Everything
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan config:cache
```

### Test Configuration
```php
php artisan tinker
> config('ai.provider')
> env('GROQ_API_KEY_1')
> app(\App\Services\AIProviderService::class)->getProvider()
```

### View Logs
```bash
tail -f storage/logs/laravel.log
```

### Restart Application
```bash
# If using artisan serve
# Stop: Ctrl+C
# Start: php artisan serve
```

---

**Last Updated:** May 18, 2026

For more help, see:
- `QUICK_START.md` - Quick reference
- `AI_PROVIDER_SETUP.md` - Detailed setup
- `CONFIG_REFERENCE.md` - Configuration details
- `AI_SYSTEM_OVERVIEW.md` - System overview
