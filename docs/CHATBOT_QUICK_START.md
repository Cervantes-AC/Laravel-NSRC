# AI ChatBot - Quick Start

## What Was Added

A complete AI ChatBot feature has been integrated into your NSRC AMS system. Here's what was created:

### Backend Components

1. **ChatBotService** (`app/Services/ChatBotService.php`)
   - Handles communication with AI providers (Groq, OpenRouter)
   - Manages conversation history
   - Provides error handling and logging

2. **ChatBotController** (`app/Http/Controllers/ChatBotController.php`)
   - API endpoints for sending messages
   - Model listing endpoint
   - Stream support for real-time responses

### Frontend Components

1. **ChatBot View** (`resources/views/chatbot/index.blade.php`)
   - Beautiful, responsive chat interface
   - Real-time message display
   - Quick action buttons
   - Loading states and error handling

### Routes

- `GET /chatbot` - View the chatbot interface
- `POST /api/chatbot/send` - Send a message to the AI
- `GET /api/chatbot/models` - Get available AI models
- `POST /api/chatbot/stream` - Stream responses (optional)

## Getting Started

### 1. Verify Your AI Configuration

Check your `.env` file has one of these setups:

**Option A: Groq (Recommended)**
```env
AI_PROVIDER=groq
GROQ_API_KEY_1=your_api_key_here
GROQ_MODEL=llama-3.3-70b-versatile
```

**Option B: OpenRouter**
```env
AI_PROVIDER=openrouter
OPENROUTER_API_KEY_1=your_api_key_here
OPENROUTER_MODEL=mistralai/mistral-7b-instruct
```

### 2. Get an API Key

- **Groq**: Sign up at https://console.groq.com (free tier available)
- **OpenRouter**: Sign up at https://openrouter.ai

### 3. Access the ChatBot

1. Log in to your NSRC AMS system
2. Navigate to `/chatbot` or look for the ChatBot link in your navigation
3. Start chatting!

## Features

✅ Real-time AI responses
✅ Conversation history support
✅ Multiple AI provider support
✅ Quick action buttons
✅ Responsive design
✅ Error handling
✅ Rate limiting
✅ Authentication required

## File Locations

```
app/Services/ChatBotService.php
app/Http/Controllers/ChatBotController.php
resources/views/chatbot/index.blade.php
routes/web.php (updated)
config/ai.php (uses existing config)
```

## API Usage

### Send a Message

```bash
curl -X POST http://localhost/api/chatbot/send \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your_csrf_token" \
  -d '{
    "message": "How do I log my time?",
    "conversation_history": []
  }'
```

Response:
```json
{
  "success": true,
  "message": "To log your time, you can...",
  "tokens_used": 150
}
```

## Customization

### Change the System Prompt

Edit `app/Services/ChatBotService.php`, find the system message:

```php
'content' => 'You are a helpful assistant for the NSRC AMS...'
```

### Adjust Response Behavior

In `config/ai.php`:

```php
'temperature' => 0.7,      // 0-1: Lower = more focused, Higher = more creative
'max_tokens' => 1024,      // Maximum response length
```

### Add More Quick Actions

In `resources/views/chatbot/index.blade.php`, add buttons in the "Quick Actions" section:

```html
<button @click="quickAction('Your question here')" class="...">
    <p class="font-semibold text-gray-900 text-sm">Button Title</p>
    <p class="text-gray-600 text-xs mt-1">Description</p>
</button>
```

## Troubleshooting

### ChatBot not responding?
1. Check `.env` has correct API key
2. Verify `AI_PROVIDER` is set correctly
3. Run `php artisan config:cache`
4. Check `storage/logs/laravel.log` for errors

### "API key not configured"
- Ensure your `.env` file has the API key
- Run `php artisan config:cache` to refresh

### Slow responses?
- Try a faster model
- Reduce `max_tokens` in `config/ai.php`
- Check your internet connection

## Next Steps

1. ✅ Verify AI configuration in `.env`
2. ✅ Get an API key from Groq or OpenRouter
3. ✅ Test the chatbot at `/chatbot`
4. ✅ Customize the system prompt if needed
5. ✅ Add to your navigation menu (optional)

## Support

For detailed setup and troubleshooting, see `CHATBOT_SETUP.md`

---

**That's it!** Your AI ChatBot is ready to use. 🚀
