# Quick Start Guide - AI Provider System

## 🚀 Get Started in 3 Steps

### Step 1: Verify Environment Configuration
Your `.env` file already has the API keys configured:
```env
AI_PROVIDER=groq
GROQ_API_KEY_1=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
GROQ_API_KEY_2=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
OPENROUTER_API_KEY_1=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
OPENROUTER_API_KEY_2=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
```

### Step 2: Clear Cache (if needed)
```bash
php artisan config:cache
php artisan cache:clear
```

### Step 3: Access the Insights Page
Navigate to: `http://localhost/reports/insights`

## 📋 What You Can Do

### Generate Report Insights
1. Select a report type (User Activity, Transaction Summary, etc.)
2. Set optional date filters
3. Click "Generate Insights"
4. View AI-powered analysis

### Switch Providers
Click the provider buttons in the sidebar:
- **Groq** - Fast, efficient model
- **OpenRouter** - Alternative provider

### Switch API Keys
If you hit rate limits, click "Switch to Alternate API Key" to use the second key.

## 🔧 API Endpoints

### Generate Insights
```bash
POST /reports/insights
Content-Type: application/json

{
  "type": "user_activity",
  "date_from": "2024-01-01",
  "date_to": "2024-12-31"
}
```

### Switch Provider
```bash
POST /reports/provider/switch
Content-Type: application/json

{
  "provider": "openrouter"
}
```

### Switch API Key
```bash
POST /reports/api-key/switch
```

## 📁 New Files Created

```
✅ app/Services/AIProviderService.php
✅ app/Livewire/AIProviderSwitcher.php
✅ config/ai.php
✅ resources/views/livewire/ai-provider-switcher.blade.php
✅ resources/views/reports/insights.blade.php
✅ AI_PROVIDER_SETUP.md (detailed guide)
✅ IMPLEMENTATION_SUMMARY.md (what was done)
✅ QUICK_START.md (this file)
```

## 🔄 Updated Files

```
✅ app/Services/ReportService.php (added AI methods)
✅ app/Http/Controllers/ReportsController.php (added endpoints)
✅ routes/web.php (added new routes)
✅ .env (added API keys)
✅ .env.example (added placeholders)
```

## 🛡️ Security Notes

- ✅ API keys are in `.env` (not in code)
- ✅ `.env` is in `.gitignore` (not committed)
- ✅ No secrets exposed in responses
- ✅ Automatic fallback to alternate keys

## ⚠️ Important

**DO NOT:**
- Commit `.env` to version control
- Share API keys in messages or logs
- Expose keys in error messages
- Use keys in client-side code

**DO:**
- Keep `.env` in `.gitignore`
- Rotate keys periodically
- Monitor API usage
- Use the key switcher for rate limits

## 🐛 Troubleshooting

### "API key not configured" Error
```bash
php artisan config:cache
```

### Insights not generating
1. Check internet connection
2. Verify API keys are valid
3. Try switching to alternate key
4. Check `storage/logs/laravel.log`

### Rate limit errors
Click "Switch to Alternate API Key" in the UI

## 📚 Documentation

- **Detailed Setup:** See `AI_PROVIDER_SETUP.md`
- **Implementation Details:** See `IMPLEMENTATION_SUMMARY.md`
- **Configuration:** See `config/ai.php`

## 🎯 Next Steps

1. Test the insights page: `http://localhost/reports/insights`
2. Generate a report and get insights
3. Try switching providers
4. Monitor API usage in provider dashboards

## 💡 Tips

- Start with Groq (faster, more reliable)
- Use OpenRouter as backup
- Switch keys when approaching rate limits
- Check provider dashboards for usage stats
- Review logs for debugging

---

**Ready to go!** 🚀 Visit `/reports/insights` to start generating AI-powered report insights.
