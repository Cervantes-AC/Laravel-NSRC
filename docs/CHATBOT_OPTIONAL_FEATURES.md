# AI ChatBot - Optional Features & Enhancements

This document outlines optional features you can add to enhance the ChatBot functionality.

## 1. Save Conversation History to Database

### Create Migration

```bash
php artisan make:migration create_chatbot_conversations_table
php artisan make:migration create_chatbot_messages_table
```

### Migration Files

**create_chatbot_conversations_table.php**
```php
Schema::create('chatbot_conversations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('title')->nullable();
    $table->text('summary')->nullable();
    $table->integer('message_count')->default(0);
    $table->timestamps();
    $table->softDeletes();
});
```

**create_chatbot_messages_table.php**
```php
Schema::create('chatbot_messages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('conversation_id')->constrained('chatbot_conversations')->onDelete('cascade');
    $table->enum('role', ['user', 'assistant', 'system']);
    $table->longText('content');
    $table->integer('tokens_used')->nullable();
    $table->timestamps();
});
```

### Create Models

```bash
php artisan make:model ChatbotConversation
php artisan make:model ChatbotMessage
```

### Update ChatBotService

Add methods to save conversations:

```php
public function saveConversation(User $user, array $messages, string $title = null): ChatbotConversation
{
    $conversation = ChatbotConversation::create([
        'user_id' => $user->id,
        'title' => $title ?? 'Conversation ' . now()->format('M d, Y'),
        'message_count' => count($messages),
    ]);

    foreach ($messages as $message) {
        ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'role' => $message['role'],
            'content' => $message['content'],
            'tokens_used' => $message['tokens_used'] ?? null,
        ]);
    }

    return $conversation;
}

public function getUserConversations(User $user): Collection
{
    return $user->chatbotConversations()
        ->latest()
        ->get();
}
```

### Add Routes

```php
Route::middleware(['auth'])->prefix('api/chatbot')->name('api.chatbot.')->group(function () {
    Route::get('/conversations', [ChatBotController::class, 'getConversations']);
    Route::get('/conversations/{id}', [ChatBotController::class, 'getConversation']);
    Route::delete('/conversations/{id}', [ChatBotController::class, 'deleteConversation']);
});
```

## 2. Add Admin Dashboard for Analytics

### Create Controller

```bash
php artisan make:controller Admin/ChatBotAnalyticsController
```

### Add Routes

```php
Route::middleware(['auth', 'role:admin'])->prefix('admin/chatbot')->group(function () {
    Route::get('/analytics', [ChatBotAnalyticsController::class, 'index'])->name('admin.chatbot.analytics');
    Route::get('/api/stats', [ChatBotAnalyticsController::class, 'stats'])->name('admin.chatbot.stats');
});
```

### Analytics View

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">ChatBot Analytics</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-600 text-sm">Total Conversations</p>
                    <p class="text-3xl font-bold" x-text="stats.total_conversations"></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-600 text-sm">Active Users</p>
                    <p class="text-3xl font-bold" x-text="stats.active_users"></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-600 text-sm">Total Messages</p>
                    <p class="text-3xl font-bold" x-text="stats.total_messages"></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-600 text-sm">Avg Response Time</p>
                    <p class="text-3xl font-bold" x-text="stats.avg_response_time + 'ms'"></p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

## 3. Implement Streaming Responses

The controller already has a `streamMessage` method. To use it:

```javascript
async function streamChat(message) {
    const response = await fetch('/api/chatbot/stream', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            message: message,
            conversation_history: []
        })
    });

    const reader = response.body.getReader();
    const decoder = new TextDecoder();

    while (true) {
        const { done, value } = await reader.read();
        if (done) break;
        
        const chunk = decoder.decode(value);
        console.log(chunk);
    }
}
```

## 4. Add Custom Knowledge Base

### Create Knowledge Base Model

```bash
php artisan make:model KnowledgeBase -m
```

### Migration

```php
Schema::create('knowledge_bases', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->longText('content');
    $table->string('category');
    $table->boolean('active')->default(true);
    $table->timestamps();
});
```

### Update ChatBotService

```php
public function chatWithKnowledgeBase(string $message, array $conversationHistory = []): array
{
    // Get relevant knowledge base articles
    $relevantDocs = KnowledgeBase::where('active', true)
        ->where('content', 'like', '%' . $message . '%')
        ->limit(3)
        ->get();

    // Add to system prompt
    $context = $relevantDocs->map(fn($doc) => $doc->content)->join('\n\n');
    
    $messages[] = [
        'role' => 'system',
        'content' => "You are a helpful assistant. Use this knowledge base to answer questions:\n\n{$context}"
    ];

    // ... rest of chat logic
}
```

## 5. Add Multi-Language Support

### Create Language Configuration

```php
// config/chatbot.php
return [
    'languages' => ['en', 'es', 'fr', 'tl'],
    'system_prompts' => [
        'en' => 'You are a helpful assistant...',
        'es' => 'Eres un asistente útil...',
        'fr' => 'Vous êtes un assistant utile...',
        'tl' => 'Ikaw ay isang mapagkakatiwalaang tulong...',
    ],
];
```

### Update Service

```php
public function chat(string $message, array $conversationHistory = [], string $language = 'en'): array
{
    $systemPrompt = config("chatbot.system_prompts.{$language}");
    // ... use system prompt in messages
}
```

## 6. Add Feedback/Rating System

### Create Migration

```bash
php artisan make:migration create_chatbot_feedback_table
```

### Migration

```php
Schema::create('chatbot_feedback', function (Blueprint $table) {
    $table->id();
    $table->foreignId('message_id')->constrained('chatbot_messages')->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->integer('rating')->min(1)->max(5);
    $table->text('comment')->nullable();
    $table->timestamps();
});
```

### Add to View

```html
<div class="flex gap-2 mt-2">
    <button @click="rateFeedback(messageId, 1)" class="text-gray-400 hover:text-red-500">👎</button>
    <button @click="rateFeedback(messageId, 5)" class="text-gray-400 hover:text-green-500">👍</button>
</div>
```

## 7. Add Rate Limiting Per User

### Create Middleware

```bash
php artisan make:middleware ChatBotRateLimit
```

### Middleware Implementation

```php
public function handle(Request $request, Closure $next)
{
    $user = $request->user();
    $key = "chatbot_messages_{$user->id}";
    
    if (Cache::has($key) && Cache::get($key) >= 100) {
        return response()->json([
            'success' => false,
            'message' => 'Rate limit exceeded. Try again later.'
        ], 429);
    }

    Cache::increment($key);
    Cache::put($key, Cache::get($key), now()->addHour());

    return $next($request);
}
```

### Register Middleware

```php
// app/Http/Kernel.php
protected $routeMiddleware = [
    // ...
    'chatbot.rate-limit' => \App\Http\Middleware\ChatBotRateLimit::class,
];
```

## 8. Add Webhook Integration

### Create Event

```bash
php artisan make:event ChatBotMessageReceived
```

### Dispatch Event

```php
// In ChatBotService
event(new ChatBotMessageReceived($user, $message, $response));
```

### Listen for Events

```bash
php artisan make:listener LogChatBotMessage
```

## Implementation Priority

1. **High Priority**: Conversation persistence (Feature #1)
2. **Medium Priority**: Feedback system (Feature #6), Rate limiting (Feature #7)
3. **Low Priority**: Analytics dashboard (Feature #2), Streaming (Feature #3)
4. **Nice to Have**: Knowledge base (Feature #4), Multi-language (Feature #5), Webhooks (Feature #8)

## Testing

Add tests for new features:

```bash
php artisan make:test ChatBotTest
```

```php
public function test_can_save_conversation()
{
    $user = User::factory()->create();
    $service = new ChatBotService();
    
    $conversation = $service->saveConversation($user, [
        ['role' => 'user', 'content' => 'Hello'],
        ['role' => 'assistant', 'content' => 'Hi there!'],
    ]);

    $this->assertDatabaseHas('chatbot_conversations', [
        'user_id' => $user->id,
    ]);
}
```

---

Choose the features that best fit your needs and implement them incrementally!
