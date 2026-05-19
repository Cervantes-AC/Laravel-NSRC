# AI ChatBot - Deployment Checklist

Use this checklist to ensure the ChatBot is properly configured and ready for production.

## ✅ Pre-Deployment

### Configuration
- [ ] AI provider selected (Groq or OpenRouter)
- [ ] API key obtained from provider
- [ ] API key added to `.env` file
- [ ] `AI_PROVIDER` environment variable set
- [ ] `config/ai.php` reviewed and understood
- [ ] System prompt customized (if needed)

### Code Review
- [ ] `app/Services/ChatbotService.php` reviewed
- [ ] `app/Http/Controllers/ChatbotController.php` reviewed
- [ ] `resources/views/chatbot/index.blade.php` reviewed
- [ ] `routes/web.php` changes reviewed
- [ ] No syntax errors in code
- [ ] All imports are correct

### Testing
- [ ] ChatBot page loads without errors
- [ ] Can send a test message
- [ ] Receives response from AI
- [ ] Conversation history works
- [ ] Quick action buttons work
- [ ] Error handling works (test with invalid input)
- [ ] Mobile responsiveness tested
- [ ] Works on different browsers

### Security
- [ ] API key is in `.env` (not in code)
- [ ] `.env` file is in `.gitignore`
- [ ] CSRF protection is enabled
- [ ] Authentication middleware is active
- [ ] Rate limiting is configured
- [ ] No sensitive data in logs
- [ ] HTTPS is enabled in production

### Documentation
- [ ] `CHATBOT_QUICK_START.md` reviewed
- [ ] `CHATBOT_SETUP.md` reviewed
- [ ] `CHATBOT_USER_GUIDE.md` reviewed
- [ ] Team members informed about new feature
- [ ] Documentation is accessible to users

## 🚀 Deployment Steps

### 1. Environment Setup
```bash
# Add to .env
AI_PROVIDER=groq
GROQ_API_KEY_1=your_actual_api_key_here
GROQ_MODEL=llama-3.3-70b-versatile
```

### 2. Clear Cache
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Verify Routes
```bash
php artisan route:list | grep chatbot
```

Expected output:
```
GET|HEAD  /chatbot                          chatbot.index
POST      /api/chatbot/send                 api.chatbot.send
GET|HEAD  /api/chatbot/models               api.chatbot.models
POST      /api/chatbot/stream               api.chatbot.stream
```

### 4. Test in Production Environment
- [ ] Access `/chatbot` page
- [ ] Send test message
- [ ] Verify response
- [ ] Check logs for errors
- [ ] Monitor API usage

### 5. Monitor Initial Usage
- [ ] Check error logs daily for first week
- [ ] Monitor API provider usage/costs
- [ ] Gather user feedback
- [ ] Track response times
- [ ] Monitor rate limiting

## 📋 Post-Deployment

### First Week
- [ ] Monitor system logs daily
- [ ] Check API provider dashboard
- [ ] Respond to user feedback
- [ ] Fix any reported issues
- [ ] Verify no performance impact

### First Month
- [ ] Analyze usage patterns
- [ ] Review error logs
- [ ] Optimize if needed
- [ ] Plan enhancements
- [ ] Update documentation

### Ongoing
- [ ] Monitor API costs
- [ ] Update system prompt if needed
- [ ] Add new quick actions based on feedback
- [ ] Plan optional features
- [ ] Keep documentation current

## 🔍 Verification Tests

### Test 1: Basic Functionality
```
1. Log in to system
2. Navigate to /chatbot
3. Type: "Hello"
4. Verify: Response appears within 5 seconds
5. Expected: AI greets you back
```

### Test 2: Conversation History
```
1. Send: "What is the attendance policy?"
2. Send: "Can you explain more?"
3. Verify: AI understands context from first message
4. Expected: Response references attendance policy
```

### Test 3: Error Handling
```
1. Send: "" (empty message)
2. Verify: Error message appears
3. Expected: "Please enter a message"
```

### Test 4: Quick Actions
```
1. Click: "Attendance Policy" button
2. Verify: Message is sent automatically
3. Expected: AI responds about attendance
```

### Test 5: Mobile Responsiveness
```
1. Open /chatbot on mobile device
2. Verify: Layout adapts to screen size
3. Verify: Buttons are clickable
4. Verify: Input field is usable
5. Expected: Full functionality on mobile
```

### Test 6: Authentication
```
1. Log out
2. Try to access /chatbot
3. Verify: Redirected to login
4. Expected: Cannot access without login
```

### Test 7: Rate Limiting
```
1. Send 50+ messages rapidly
2. Verify: Rate limit kicks in
3. Expected: Error message after limit
```

### Test 8: API Endpoints
```
# Test send message
curl -X POST http://localhost/api/chatbot/send \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: token" \
  -d '{"message":"test","conversation_history":[]}'

# Test get models
curl http://localhost/api/chatbot/models

# Verify: Both return valid JSON responses
```

## 🐛 Common Issues & Solutions

### Issue: "API key not configured"
**Solution:**
1. Check `.env` file has API key
2. Run `php artisan config:cache`
3. Restart application
4. Verify key is correct

### Issue: Slow responses
**Solution:**
1. Check internet connection
2. Verify API provider status
3. Try simpler questions
4. Check server resources

### Issue: CORS errors
**Solution:**
1. This shouldn't happen (same-origin)
2. Check browser console for actual error
3. Verify HTTPS is enabled
4. Check CSRF token is being sent

### Issue: ChatBot not appearing
**Solution:**
1. Clear browser cache
2. Hard refresh (Ctrl+Shift+R)
3. Check browser console for errors
4. Verify routes are cached correctly

## 📊 Monitoring

### Key Metrics to Track
- [ ] Daily active users
- [ ] Messages per day
- [ ] Average response time
- [ ] Error rate
- [ ] API provider costs
- [ ] User satisfaction

### Logs to Monitor
- [ ] `storage/logs/laravel.log` - Application errors
- [ ] API provider dashboard - Usage and costs
- [ ] Browser console - Frontend errors
- [ ] Server logs - Performance issues

### Alerts to Set Up
- [ ] High error rate (>5%)
- [ ] Slow response time (>10s)
- [ ] API quota exceeded
- [ ] Authentication failures
- [ ] Rate limit exceeded

## 🎯 Success Criteria

The deployment is successful when:

✅ ChatBot page loads without errors
✅ Can send and receive messages
✅ Conversation history works
✅ Error handling is graceful
✅ Mobile responsive
✅ Authentication required
✅ Rate limiting works
✅ No performance impact
✅ Users can access it
✅ Documentation is clear

## 📞 Support Contacts

### For Technical Issues
- **Developer**: Check logs and error messages
- **System Admin**: Verify configuration
- **API Provider**: Check service status

### For User Issues
- **User Guide**: `CHATBOT_USER_GUIDE.md`
- **Setup Guide**: `CHATBOT_SETUP.md`
- **Quick Start**: `CHATBOT_QUICK_START.md`

## 🔄 Rollback Plan

If issues occur:

### Step 1: Disable ChatBot
```bash
# Comment out routes in routes/web.php
# Or disable in config/ai.php
```

### Step 2: Notify Users
- Inform users ChatBot is temporarily unavailable
- Provide alternative support channels

### Step 3: Investigate
- Check logs for errors
- Verify API provider status
- Review recent changes

### Step 4: Fix & Redeploy
- Fix identified issues
- Test thoroughly
- Redeploy when ready

## ✨ Final Checklist

Before going live:

- [ ] All tests pass
- [ ] Documentation complete
- [ ] Team trained
- [ ] Monitoring set up
- [ ] Rollback plan ready
- [ ] Support contacts identified
- [ ] Users informed
- [ ] API key secured
- [ ] Performance acceptable
- [ ] Security verified

---

**Deployment Date**: _______________
**Deployed By**: _______________
**Approved By**: _______________

**Notes**:
```
_________________________________
_________________________________
_________________________________
```

---

**Status**: Ready for Deployment ✅
