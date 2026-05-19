# ChatBot Testing Guide

## ✅ Configuration Status

Your `.env` file is already configured with:
- ✅ `AI_PROVIDER=groq`
- ✅ `GROQ_API_KEY_1` - Set
- ✅ `GROQ_MODEL=llama-3.3-70b-versatile`
- ✅ `AI_TEMPERATURE=0.7`
- ✅ `AI_MAX_TOKENS=1024`

## ✅ Routes Status

All ChatBot routes are registered:
- ✅ `GET /chatbot` - ChatBot interface
- ✅ `POST /api/chatbot/send` - Send message
- ✅ `GET /api/chatbot/models` - Get models
- ✅ `POST /api/chatbot/stream` - Stream responses

## 🧪 Testing Steps

### Step 1: Clear Cache
```bash
php artisan config:cache
php artisan route:cache
```

### Step 2: Access the ChatBot
1. Log in to your NSRC AMS system
2. Navigate to: `http://localhost/chatbot`
3. You should see the chat interface

### Step 3: Send a Test Message
1. Type: "Hello, how are you?"
2. Click "Send"
3. Wait for response (1-3 seconds)
4. You should see the AI response

### Step 4: Test Conversation History
1. Send: "What is the attendance policy?"
2. Send: "Can you explain more?"
3. The AI should understand context from the first message

### Step 5: Test Quick Actions
1. Click "Attendance Policy" button
2. The message should be sent automatically
3. AI should respond about attendance

## 🔍 Troubleshooting

### If ChatBot page doesn't load:
```bash
# Clear all caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart your server
```

### If you get "API key not configured":
1. Check `.env` has `GROQ_API_KEY_1`
2. Run `php artisan config:cache`
3. Verify the key is not empty

### If responses are slow:
1. Check internet connection
2. Verify Groq API status
3. Try a simpler question

### If you get CSRF errors:
1. Make sure you're logged in
2. Check browser console for errors
3. Verify CSRF token is being sent

## 📊 API Testing

### Test Send Message Endpoint
```bash
curl -X POST http://localhost/api/chatbot/send \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your_csrf_token" \
  -d '{
    "message": "Hello",
    "conversation_history": []
  }'
```

Expected response:
```json
{
  "success": true,
  "message": "Hi there! How can I help you?",
  "tokens_used": 45
}
```

### Test Get Models Endpoint
```bash
curl http://localhost/api/chatbot/models
```

Expected response:
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

## 📝 Manual Testing Checklist

- [ ] ChatBot page loads without errors
- [ ] Can type in message input
- [ ] Send button works
- [ ] Receives response from AI
- [ ] Response appears in chat window
- [ ] Conversation history is maintained
- [ ] Quick action buttons work
- [ ] Error handling works (try empty message)
- [ ] Mobile view is responsive
- [ ] Works on different browsers

## 🐛 Debug Mode

To enable debug logging:

1. Edit `config/ai.php` and add:
```php
'debug' => true,
```

2. Check logs:
```bash
tail -f storage/logs/laravel.log
```

3. Look for ChatBot entries:
```
[2026-05-19 10:30:45] local.DEBUG: ChatBot request received
[2026-05-19 10:30:46] local.DEBUG: AI response: "..."
```

## 🎯 Success Criteria

The ChatBot is working correctly when:

✅ Page loads at `/chatbot`
✅ Can send messages
✅ Receives AI responses
✅ Conversation history works
✅ No errors in browser console
✅ No errors in `storage/logs/laravel.log`
✅ Response time is 1-3 seconds
✅ Works on mobile devices

## 📞 Support

If you encounter issues:

1. Check `storage/logs/laravel.log` for errors
2. Verify `.env` configuration
3. Run `php artisan config:cache`
4. Check browser console (F12)
5. Review `CHATBOT_SETUP.md` troubleshooting section

## ✨ Next Steps

Once testing is complete:

1. ✅ Customize the system prompt (optional)
2. ✅ Add to navigation menu (optional)
3. ✅ Deploy to production
4. ✅ Monitor usage and feedback

---

**Status**: Ready for Testing ✅
