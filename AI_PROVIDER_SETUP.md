# AI Provider Setup Guide

This document explains how to use the AI Provider system for generating report insights with support for multiple API providers and keys.

## Overview

The AI Provider system allows you to:
- Switch between multiple AI providers (Groq, OpenRouter)
- Use multiple API keys per provider for rate limit management
- Generate AI-powered insights for reports
- Manage providers and keys through a user-friendly interface

## Configuration

### Environment Variables

Add the following to your `.env` file:

```env
# AI Provider Configuration
AI_PROVIDER=groq
GROQ_API_KEY_1=your_groq_key_1
GROQ_API_KEY_2=your_groq_key_2
OPENROUTER_API_KEY_1=your_openrouter_key_1
OPENROUTER_API_KEY_2=your_openrouter_key_2
```

**Important:** Never commit `.env` to version control. The `.env.example` file contains placeholders.

### Configuration File

The system uses `config/ai.php` for provider settings. You can customize:
- Default provider
- API endpoints
- Models
- Temperature and token limits

## Usage

### 1. Access the Insights Page

Navigate to `/reports/insights` to access the AI insights interface.

### 2. Generate Insights

1. Select a report type (User Activity, Transaction Summary, Audit Trail, System Usage, or Custom)
2. Optionally set date filters
3. Click "Generate Insights"
4. The system will generate AI-powered insights using the current provider

### 3. Switch Providers

Use the provider switcher in the sidebar to:
- Switch between Groq and OpenRouter
- Switch to an alternate API key if rate limits are reached

## API Endpoints

### Get Report Insights

**POST** `/reports/insights`

Request:
```json
{
  "type": "user_activity",
  "date_from": "2024-01-01",
  "date_to": "2024-12-31",
  "user_id": null,
  "status": null
}
```

Response:
```json
{
  "success": true,
  "insights": "Key findings...",
  "provider": "groq",
  "generated_at": "2024-05-18T10:30:00Z"
}
```

### Switch Provider

**POST** `/reports/provider/switch`

Request:
```json
{
  "provider": "openrouter"
}
```

Response:
```json
{
  "success": true,
  "message": "Provider switched successfully",
  "current_provider": "openrouter"
}
```

### Switch API Key

**POST** `/reports/api-key/switch`

Response:
```json
{
  "success": true,
  "message": "API key switched successfully",
  "current_provider": "groq"
}
```

## Services

### AIProviderService

Located in `app/Services/AIProviderService.php`

**Methods:**
- `switchProvider(string $provider)` - Switch to a different provider
- `switchApiKey()` - Switch to alternate API key
- `generateReportInsights(array $reportData, string $reportType)` - Generate insights
- `getProvider()` - Get current provider
- `getKeyIndex()` - Get current API key index

**Example Usage:**
```php
$aiProvider = app(AIProviderService::class);
$aiProvider->switchProvider('openrouter');
$insights = $aiProvider->generateReportInsights($reportData, 'user_activity');
```

### ReportService

Updated to include AI capabilities:

**New Methods:**
- `getReportInsights(array $reportData, string $reportType)` - Get AI insights
- `switchAIProvider(string $provider)` - Switch provider
- `switchAPIKey()` - Switch API key
- `getCurrentProvider()` - Get current provider

**Example Usage:**
```php
$reportService = app(ReportService::class);
$report = $reportService->generateUserActivityReport($filters);
$insights = $reportService->getReportInsights($report['data'], 'user_activity');
```

## Components

### AIProviderSwitcher Livewire Component

Located in `app/Livewire/AIProviderSwitcher.php`

Provides a UI for:
- Switching between providers
- Switching API keys
- Displaying current provider status

**Usage in Blade:**
```blade
<livewire:ai-provider-switcher />
```

## Error Handling

The system includes automatic fallback:
1. If the primary API key fails, it automatically tries the alternate key
2. If both keys fail, an error is returned with details

## Security Considerations

1. **Never commit API keys** - Use `.env` file (in `.gitignore`)
2. **Rotate keys regularly** - Use the alternate key feature for rotation
3. **Monitor usage** - Check provider dashboards for unusual activity
4. **Rate limiting** - Use the key switcher when approaching rate limits

## Troubleshooting

### "API key not configured" Error

Ensure all required environment variables are set in `.env`:
```bash
php artisan config:cache
```

### API Request Failures

1. Check that API keys are valid
2. Verify internet connectivity
3. Check provider status pages
4. Try switching to alternate API key

### Rate Limit Errors

Use the "Switch to Alternate API Key" button in the UI to use the second key.

## Supported Providers

### Groq
- **Model:** mixtral-8x7b-32768
- **Endpoint:** https://api.groq.com/openai/v1/chat/completions
- **Rate Limits:** Check Groq dashboard

### OpenRouter
- **Model:** mistralai/mistral-7b-instruct
- **Endpoint:** https://openrouter.ai/api/v1/chat/completions
- **Rate Limits:** Check OpenRouter dashboard

## Future Enhancements

Potential improvements:
- Add more providers (Claude, GPT-4, etc.)
- Implement caching for insights
- Add scheduled report generation
- Create custom prompt templates
- Add usage analytics and monitoring

## Support

For issues or questions:
1. Check the error messages in the UI
2. Review application logs: `storage/logs/laravel.log`
3. Verify environment configuration
4. Test API keys directly with provider tools
