# AI ChatBot - Architecture & Flow Diagram

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        USER BROWSER                              │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  resources/views/chatbot/index.blade.php                │   │
│  │  ┌────────────────────────────────────────────────────┐ │   │
│  │  │  Chat Interface (Alpine.js)                        │ │   │
│  │  │  - Message display                                 │ │   │
│  │  │  - Input field                                     │ │   │
│  │  │  - Quick action buttons                            │ │   │
│  │  │  - Loading states                                  │ │   │
│  │  └────────────────────────────────────────────────────┘ │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                              ↓ (AJAX)
┌─────────────────────────────────────────────────────────────────┐
│                      LARAVEL APPLICATION                         │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  routes/web.php                                          │   │
│  │  - GET /chatbot                                          │   │
│  │  - POST /api/chatbot/send                               │   │
│  │  - GET /api/chatbot/models                              │   │
│  │  - POST /api/chatbot/stream                             │   │
│  └──────────────────────────────────────────────────────────┘   │
│                              ↓                                    │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  app/Http/Controllers/ChatbotController.php             │   │
│  │  ┌────────────────────────────────────────────────────┐ │   │
│  │  │ index()           - Display chat page              │ │   │
│  │  │ sendMessage()     - Handle message POST            │ │   │
│  │  │ getModels()       - Return available models        │ │   │
│  │  │ streamMessage()   - Stream responses               │ │   │
│  │  └────────────────────────────────────────────────────┘ │   │
│  └──────────────────────────────────────────────────────────┘   │
│                              ↓                                    │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  app/Services/ChatbotService.php                         │   │
│  │  ┌────────────────────────────────────────────────────┐ │   │
│  │  │ chat()                    - Main chat method       │ │   │
│  │  │ chatWithOpenAICompatible() - API communication    │ │   │
│  │  │ getAvailableModels()      - List models           │ │   │
│  │  └────────────────────────────────────────────────────┘ │   │
│  └──────────────────────────────────────────────────────────┘   │
│                              ↓                                    │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  config/ai.php                                           │   │
│  │  - Provider: groq or openrouter                          │   │
│  │  - API keys and endpoints                               │   │
│  │  - Model configuration                                  │   │
│  │  - Temperature and max_tokens                           │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                              ↓ (HTTP)
┌─────────────────────────────────────────────────────────────────┐
│                    AI PROVIDER (External)                        │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  Groq API                                                │   │
│  │  https://api.groq.com/openai/v1/chat/completions       │   │
│  │  Models: Llama 3.3 70B, Mixtral 8x7B, etc.             │   │
│  │                                                          │   │
│  │  OR                                                      │   │
│  │                                                          │   │
│  │  OpenRouter API                                          │   │
│  │  https://openrouter.ai/api/v1/chat/completions         │   │
│  │  Models: Mistral, Llama, GPT-3.5, etc.                 │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

## Request/Response Flow

### 1. User Sends Message

```
User Types Message
        ↓
Click "Send" Button
        ↓
JavaScript validates input
        ↓
AJAX POST to /api/chatbot/send
        ↓
{
  "message": "How do I log my time?",
  "conversation_history": [...]
}
```

### 2. Server Processes Request

```
ChatbotController@sendMessage()
        ↓
Validate input (max 1000 chars)
        ↓
ChatbotService->chat()
        ↓
Build messages array with:
  - System prompt
  - Conversation history
  - Current message
        ↓
Call AI Provider API
```

### 3. AI Provider Responds

```
Send HTTP POST to AI Provider
        ↓
Provider processes request
        ↓
Generate response
        ↓
Return JSON response
        ↓
{
  "choices": [{
    "message": {
      "content": "To log your time, you can..."
    }
  }],
  "usage": {
    "total_tokens": 150
  }
}
```

### 4. Server Returns Response

```
Parse AI response
        ↓
Extract message content
        ↓
Log request (optional)
        ↓
Return JSON to client
        ↓
{
  "success": true,
  "message": "To log your time, you can...",
  "tokens_used": 150
}
```

### 5. Client Displays Response

```
Receive JSON response
        ↓
Add to messages array
        ↓
Update DOM with new message
        ↓
Scroll to bottom
        ↓
Enable input field
        ↓
User sees response
```

## Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                    CONVERSATION FLOW                             │
└─────────────────────────────────────────────────────────────────┘

User Message 1
    ↓
[User: "What is the attendance policy?"]
    ↓
AI Response 1
    ↓
[Assistant: "The attendance policy requires..."]
    ↓
User Message 2 (with history)
    ↓
[User: "Can you explain more?"]
[History: Previous exchange]
    ↓
AI Response 2 (with context)
    ↓
[Assistant: "Sure! The policy also includes..."]
    ↓
User Message 3 (with full history)
    ↓
[User: "What about late arrivals?"]
[History: All previous exchanges]
    ↓
AI Response 3 (understands context)
    ↓
[Assistant: "If you're late, the system..."]
```

## Component Interaction

```
┌──────────────────────────────────────────────────────────────┐
│                    COMPONENT DIAGRAM                          │
└──────────────────────────────────────────────────────────────┘

┌─────────────────┐
│  User Browser   │
│  (Frontend)     │
└────────┬────────┘
         │ AJAX
         ↓
┌─────────────────────────────────────────┐
│  Laravel Routes                         │
│  - /chatbot (GET)                       │
│  - /api/chatbot/send (POST)             │
│  - /api/chatbot/models (GET)            │
│  - /api/chatbot/stream (POST)           │
└────────┬────────────────────────────────┘
         │
         ↓
┌─────────────────────────────────────────┐
│  ChatbotController                      │
│  - index()                              │
│  - sendMessage()                        │
│  - getModels()                          │
│  - streamMessage()                      │
└────────┬────────────────────────────────┘
         │
         ↓
┌─────────────────────────────────────────┐
│  ChatbotService                         │
│  - chat()                               │
│  - chatWithOpenAICompatible()           │
│  - getAvailableModels()                 │
└────────┬────────────────────────────────┘
         │
         ↓
┌─────────────────────────────────────────┐
│  config/ai.php                          │
│  - Provider config                      │
│  - API keys                             │
│  - Model settings                       │
└────────┬────────────────────────────────┘
         │ HTTP
         ↓
┌─────────────────────────────────────────┐
│  External AI Provider                   │
│  - Groq or OpenRouter                   │
│  - Chat Completions API                 │
└─────────────────────────────────────────┘
```

## Authentication & Security Flow

```
┌──────────────────────────────────────────────────────────────┐
│                  SECURITY FLOW                                │
└──────────────────────────────────────────────────────────────┘

User Request
    ↓
Check Authentication (auth middleware)
    ├─ Not logged in? → Redirect to login
    └─ Logged in? → Continue
    ↓
Check CSRF Token (for POST requests)
    ├─ Invalid token? → 419 error
    └─ Valid token? → Continue
    ↓
Validate Input
    ├─ Empty message? → Validation error
    ├─ Too long (>1000)? → Validation error
    └─ Valid? → Continue
    ↓
Check Rate Limit (throttle.custom)
    ├─ Exceeded? → 429 error
    └─ OK? → Continue
    ↓
Process Request
    ↓
Log Request (for audit trail)
    ↓
Return Response
```

## Error Handling Flow

```
┌──────────────────────────────────────────────────────────────┐
│                  ERROR HANDLING                               │
└──────────────────────────────────────────────────────────────┘

Request Received
    ↓
Try to process
    ├─ Validation Error
    │  └─ Return: {"success": false, "message": "..."}
    │
    ├─ API Key Missing
    │  └─ Return: {"success": false, "message": "API key not configured"}
    │
    ├─ API Provider Error
    │  └─ Log error
    │  └─ Return: {"success": false, "message": "Failed to get response"}
    │
    ├─ Network Error
    │  └─ Log error
    │  └─ Return: {"success": false, "message": "Network error"}
    │
    └─ Success
       └─ Return: {"success": true, "message": "..."}
```

## Deployment Architecture

```
┌──────────────────────────────────────────────────────────────┐
│                  PRODUCTION SETUP                             │
└──────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│  Web Server (Nginx/Apache)              │
│  - HTTPS enabled                        │
│  - Rate limiting                        │
│  - Caching                              │
└────────┬────────────────────────────────┘
         │
         ↓
┌─────────────────────────────────────────┐
│  Laravel Application                    │
│  - Authentication                       │
│  - CSRF Protection                      │
│  - Input Validation                     │
│  - Error Logging                        │
└────────┬────────────────────────────────┘
         │
         ↓
┌─────────────────────────────────────────┐
│  Environment Variables (.env)           │
│  - AI_PROVIDER                          │
│  - API_KEY_1                            │
│  - API_KEY_2 (backup)                   │
│  - MODEL                                │
└────────┬────────────────────────────────┘
         │
         ↓
┌─────────────────────────────────────────┐
│  External Services                      │
│  - Groq API (Primary)                   │
│  - OpenRouter API (Alternative)         │
│  - Logging Service                      │
│  - Monitoring Service                   │
└─────────────────────────────────────────┘
```

## Technology Stack

```
Frontend:
  - Blade Templates (Laravel)
  - Alpine.js (Interactivity)
  - Tailwind CSS (Styling)
  - Vanilla JavaScript (AJAX)

Backend:
  - Laravel 12
  - PHP 8.2+
  - HTTP Client (Guzzle)
  - Middleware (Auth, CSRF, Rate Limit)

External:
  - Groq API (AI Provider)
  - OpenRouter API (Alternative)

Database:
  - None required (stateless)
  - Optional: For conversation persistence

Logging:
  - Laravel Logs
  - API Provider Logs
  - Browser Console
```

## Performance Considerations

```
┌──────────────────────────────────────────────────────────────┐
│                  PERFORMANCE FLOW                             │
└──────────────────────────────────────────────────────────────┘

User sends message
    ↓
Request time: ~100ms (network)
    ↓
Server processing: ~50ms
    ↓
API call to provider: ~1-3 seconds (main delay)
    ↓
Response parsing: ~50ms
    ↓
Return to client: ~100ms (network)
    ↓
Total: ~1.3-3.3 seconds (mostly API provider)

Optimization opportunities:
- Streaming responses (real-time)
- Caching common responses
- Parallel requests
- Model selection (faster models)
```

## Scalability

```
┌──────────────────────────────────────────────────────────────┐
│                  SCALABILITY DESIGN                           │
└──────────────────────────────────────────────────────────────┘

Current:
  - Single instance
  - Stateless (no session storage)
  - Direct API calls

Scalable to:
  - Multiple servers (load balanced)
  - Queue system (for async processing)
  - Caching layer (Redis)
  - Database persistence (optional)
  - Multiple API providers (failover)

Bottlenecks:
  - API provider rate limits
  - API provider costs
  - Network latency
  - Server resources
```

---

This architecture is designed to be:
- ✅ Simple and maintainable
- ✅ Secure by default
- ✅ Scalable for growth
- ✅ Easy to customize
- ✅ Production-ready
