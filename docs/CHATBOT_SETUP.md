# AI ChatBot Setup Guide

This guide will help you set up and configure the AI ChatBot feature in the NSRC AMS system.

## Overview

The AI ChatBot is a simple, integrated assistant that helps users with questions about the system. It uses your existing AI provider configuration (Groq or OpenRouter).

## Features

- Real-time chat interface
- Conversation history support
- Multiple AI provider support (Groq, OpenRouter)
- Quick action buttons for common questions
- Error handling and user feedback
- Responsive design

## Setup Instructions

### 1. Verify AI Configuration

The chatbot uses the existing AI configuration from `config/ai.php`. Make sure you have:

```env
AI_PROVIDER=groq  # or 'openrouter'
GROQ_API_KEY_1=your_groq_api_key
GROQ_MODEL=llama-3.3-70b-versatile
```

Or for OpenRouter:

```env
AI_PROVIDER=openrouter
OPENROUTER_API_KEY_1=your_openrouter_api_key
OPENROUTER_MODEL=mistralai/mistral-7b-instruct
```

### 2. Access the ChatBot

Once configured, authenticated users can access the chatbot at:

```
/chatbot
```

### 3. API Endpoints

The chatbot provides the following API endpoints (all require authentication):

#### Send Message
```
POST /api/chatbot/send
Content-Type: application/json

{
    "message": "Your question here",
    "conversation_history": [
        {
            "role": "user",
            "content": "Previous message"
        },
        {
            "role": "assistant",
            "content": "Previous response"
        }
    ]
}
```

Response:
```json
{
    "success": true,
    "message": "AI response here",
    "tokens_used": 150
}
```

#### Get Available Models
```
GET /api/chatbot/models
```

Response:
```json
{
    "success": true,
    "models": {
        "groq": {
            "llama-3.3-70b-versatile": "Llama 3.3 70B (Fast & Powerful)",
            ...
        }
    }
}
```

## File Structure

```
app/
├── Services/
│   └── ChatBotService.php          # Core chatbot logic
└── Http/
    └── Controllers/
        └── ChatBotController.php    # API endpoints

resources/
└── views/
    └── chatbot/
        └── index.blade.php          # Chat interface

routes/
└── web.php                          # Routes configuration
```

## Configuration

### Supported Providers

1. **Groq** (Recommended - Free tier available)
   - Fast inference
   - Multiple models available
   - Sign up at: https://console.groq.com

2. **OpenRouter**
   - Access to multiple models
   - Pay-as-you-go pricing
   - Sign up at: https://openrouter.ai

### Customization

To customize the chatbot behavior, edit `app/Services/ChatBotService.php`:

- **System Prompt**: Modify the system message in `chatWithOpenAICompatible()`
- **Temperature**: Adjust `config('ai.temperature', 0.7)` for more/less creative responses
- **Max Tokens**: Adjust `config('ai.max_tokens', 1024)` for response length

## Usage Examples

### Basic Chat
```javascript
const response = await fetch('/api/chatbot/send', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({
        message: 'How do I log my time?',
        conversation_history: []
    })
});

const data = await response.json();
console.log(data.message); // AI response
```

### With Conversation History
```javascript
const history = [
    { role: 'user', content: 'What is the attendance policy?' },
    { role: 'assistant', content: 'The attendance policy requires...' }
];

const response = await fetch('/api/chatbot/send', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({
        message: 'Can you explain more about that?',
        conversation_history: history
    })
});
```

## Troubleshooting

### "API key not configured"
- Check your `.env` file has the correct API key
- Verify `AI_PROVIDER` is set to either 'groq' or 'openrouter'
- Run `php artisan config:cache` to refresh configuration

### "Failed to get response"
- Check your API key is valid
- Verify you have API credits/quota remaining
- Check network connectivity
- Review logs in `storage/logs/laravel.log`

### Slow Responses
- Try a faster model (e.g., Llama 3.3 70B for Groq)
- Reduce `max_tokens` in `config/ai.php`
- Check your internet connection

## Security Considerations

1. **Authentication**: ChatBot is protected by Laravel's `auth` middleware
2. **Rate Limiting**: Uses the `throttle.custom` middleware
3. **Input Validation**: Messages are validated (max 1000 characters)
4. **API Keys**: Never commit `.env` file with real API keys

## Future Enhancements

Potential improvements:
- Conversation persistence (save to database)
- User-specific context (personalized responses)
- Admin dashboard for chatbot analytics
- Custom knowledge base integration
- Multi-language support
- Streaming responses for real-time updates

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Review Laravel logs: `storage/logs/laravel.log`
3. Verify API provider status and documentation
4. Check network connectivity and firewall settings

## License

This feature is part of the NSRC AMS system and follows the same license.
