# Configuration Reference

## Environment Variables (.env)

### AI Provider Selection
```env
# Current provider: groq or openrouter
AI_PROVIDER=groq
```

### Groq API Keys
```env
# Primary Groq API key
GROQ_API_KEY_1=your_groq_key_1

# Backup Groq API key (for rate limit fallback)
GROQ_API_KEY_2=your_groq_key_2
```

### OpenRouter API Keys
```env
# Primary OpenRouter API key
OPENROUTER_API_KEY_1=your_openrouter_key_1

# Backup OpenRouter API key (for rate limit fallback)
OPENROUTER_API_KEY_2=your_openrouter_key_2
```

## Configuration File (config/ai.php)

### Provider Configuration
```php
'provider' => env('AI_PROVIDER', 'groq'),
```

### Groq Settings
```php
'groq' => [
    'api_key_1' => env('GROQ_API_KEY_1'),
    'api_key_2' => env('GROQ_API_KEY_2'),
    'endpoint' => 'https://api.groq.com/openai/v1/chat/completions',
    'model' => 'mixtral-8x7b-32768',
],
```

### OpenRouter Settings
```php
'openrouter' => [
    'api_key_1' => env('OPENROUTER_API_KEY_1'),
    'api_key_2' => env('OPENROUTER_API_KEY_2'),
    'endpoint' => 'https://openrouter.ai/api/v1/chat/completions',
    'model' => 'mistralai/mistral-7b-instruct',
],
```

### AI Generation Settings
```php
'temperature' => 0.7,      // Creativity level (0-1)
'max_tokens' => 1024,      // Maximum response length
```

## Service Configuration

### AIProviderService

**Location:** `app/Services/AIProviderService.php`

**Constructor:**
```php
public function __construct()
{
    $this->provider = config('app.ai_provider', 'groq');
    $this->setApiKey();
}
```

**Key Methods:**
```php
// Switch provider
switchProvider(string $provider): self

// Switch API key
switchApiKey(): self

// Generate insights
generateReportInsights(array $reportData, string $reportType): string

// Get current provider
getProvider(): string

// Get current key index
getKeyIndex(): int
```

### ReportService

**Location:** `app/Services/ReportService.php`

**Constructor:**
```php
public function __construct(AIProviderService $aiProvider)
{
    $this->aiProvider = $aiProvider;
}
```

**AI Methods:**
```php
// Get AI insights for report
getReportInsights(array $reportData, string $reportType): array

// Switch AI provider
switchAIProvider(string $provider): self

// Switch API key
switchAPIKey(): self

// Get current provider
getCurrentProvider(): string
```

## Routes Configuration

### New Routes (routes/web.php)

```php
// View insights page
GET /reports/insights

// Generate insights
POST /reports/insights

// Switch provider
POST /reports/provider/switch

// Switch API key
POST /reports/api-key/switch
```

## Livewire Component Configuration

### AIProviderSwitcher

**Location:** `app/Livewire/AIProviderSwitcher.php`

**Public Properties:**
```php
public string $currentProvider = 'groq';
public array $providers = ['groq', 'openrouter'];
public bool $showMessage = false;
public string $message = '';
public string $messageType = 'success';
```

**Public Methods:**
```php
// Switch provider
switchProvider(string $provider)

// Switch API key
switchApiKey()
```

**Events Dispatched:**
```php
// When provider is switched
$this->dispatch('provider-switched', provider: $provider);

// When API key is switched
$this->dispatch('api-key-switched', provider: $this->currentProvider);

// Reset message after delay
$this->dispatch('reset-message-after-delay');
```

## API Response Formats

### Get Insights Response

**Success:**
```json
{
  "success": true,
  "insights": "Key findings...",
  "provider": "groq",
  "generated_at": "2024-05-18T10:30:00Z"
}
```

**Error:**
```json
{
  "success": false,
  "error": "API key not configured: GROQ_API_KEY_1",
  "provider": "groq"
}
```

### Switch Provider Response

**Success:**
```json
{
  "success": true,
  "message": "Provider switched successfully",
  "current_provider": "openrouter"
}
```

**Error:**
```json
{
  "success": false,
  "error": "Invalid provider: invalid_provider. Valid options: groq, openrouter"
}
```

### Switch API Key Response

**Success:**
```json
{
  "success": true,
  "message": "API key switched successfully",
  "current_provider": "groq"
}
```

**Error:**
```json
{
  "success": false,
  "error": "API key not configured: GROQ_API_KEY_2"
}
```

## Customization

### Change Default Provider

Edit `.env`:
```env
AI_PROVIDER=openrouter
```

### Change Model

Edit `config/ai.php`:
```php
'groq' => [
    'model' => 'mixtral-8x7b-32768', // Change this
],
```

### Adjust Temperature

Edit `config/ai.php`:
```php
'temperature' => 0.5, // Lower = more focused, Higher = more creative
```

### Adjust Max Tokens

Edit `config/ai.php`:
```php
'max_tokens' => 2048, // Increase for longer responses
```

## Environment-Specific Configuration

### Development
```env
AI_PROVIDER=groq
APP_DEBUG=true
```

### Production
```env
AI_PROVIDER=groq
APP_DEBUG=false
```

### Testing
```env
AI_PROVIDER=groq
GROQ_API_KEY_1=test_key_1
GROQ_API_KEY_2=test_key_2
```

## Caching Configuration

To cache configuration:
```bash
php artisan config:cache
```

To clear cache:
```bash
php artisan config:clear
```

## Logging Configuration

Logs are stored in: `storage/logs/laravel.log`

To view recent logs:
```bash
tail -f storage/logs/laravel.log
```

## Security Configuration

### API Key Rotation

1. Generate new keys from provider dashboard
2. Update `.env` with new keys
3. Clear cache: `php artisan config:cache`
4. Test with new keys
5. Revoke old keys from provider dashboard

### Rate Limiting

The system automatically:
1. Tries primary API key
2. Falls back to secondary key on failure
3. Returns error if both fail

Manual switching:
- Use "Switch to Alternate API Key" button in UI
- Or call `POST /reports/api-key/switch`

## Monitoring

### Check Current Configuration

```php
// In tinker or controller
config('ai.provider')           // Current provider
config('ai.groq.model')         // Groq model
config('ai.openrouter.model')   // OpenRouter model
config('ai.temperature')        // Temperature setting
config('ai.max_tokens')         // Max tokens
```

### Monitor API Usage

1. Groq Dashboard: https://console.groq.com
2. OpenRouter Dashboard: https://openrouter.ai/account/usage

## Troubleshooting Configuration

### Config Not Updating

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Recache
php artisan config:cache
```

### API Keys Not Found

```bash
# Check .env file exists
ls -la .env

# Verify keys are set
grep GROQ_API_KEY .env

# Check config is readable
php artisan config:show ai
```

### Provider Not Switching

```bash
# Verify provider name
php artisan tinker
> config('ai.provider')

# Check valid providers
> ['groq', 'openrouter']
```

---

**Last Updated:** May 18, 2026
