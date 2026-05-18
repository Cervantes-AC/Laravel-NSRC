# API Documentation

## Overview

The NSRC AMS provides a comprehensive REST API for programmatic access to system data and functionality.

## Authentication

All API endpoints require authentication. Include your authentication token in the request header:

```
Authorization: Bearer {token}
```

## Base URL

```
http://127.0.0.1:8000/api
```

## Response Format

All responses are in JSON format:

```json
{
  "success": true,
  "data": {},
  "message": "Success"
}
```

## Error Responses

```json
{
  "success": false,
  "error": "Error message",
  "code": 400
}
```

## API Endpoints

### Dashboard

**Get Dashboard Data**
```
GET /api/dashboard/data
```

Returns key metrics and statistics for the dashboard.

### Sessions

**Get Sessions**
```
GET /api/sessions
```

Returns list of duty sessions.

**Sync Sessions**
```
POST /api/sessions/sync
```

Synchronizes session data.

### Notifications

**Get Notifications**
```
GET /api/notifications
```

Returns user notifications.

**Stream Notifications**
```
GET /api/notifications/stream
```

Real-time notification stream.

**Mark as Read**
```
POST /api/notifications/{id}/read
```

Mark notification as read.

**Mark All as Read**
```
POST /api/notifications/read-all
```

Mark all notifications as read.

**Delete Notification**
```
DELETE /api/notifications/{id}
```

Delete a notification.

### Analytics

**Get Analytics Data**
```
GET /api/analytics/data
```

Returns analytics and performance data.

### Rankings

**Get Rankings**
```
GET /api/rankings
```

Returns volunteer rankings.

### Personnel

**Get Personnel**
```
GET /api/personnel
```

Returns personnel records.

**Get Personnel History**
```
GET /api/personnel/history
```

Returns personnel change history.

### Accounts

**Get Accounts**
```
GET /api/accounts
```

Returns user accounts (admin only).

**Approve Account**
```
POST /api/accounts/{id}/approve
```

Approve a pending account (admin only).

**Reject Account**
```
POST /api/accounts/{id}/reject
```

Reject a pending account (admin only).

**Suspend Account**
```
POST /api/accounts/{id}/suspend
```

Suspend an account (admin only).

**Bulk Action**
```
POST /api/accounts/bulk-action
```

Perform bulk actions on accounts (admin only).

### Audit Logs

**Get Audit Logs**
```
GET /api/audit-logs
```

Returns audit logs (admin only).

**Export Audit Logs**
```
GET /api/audit-logs/export
```

Export audit logs (admin only).

### Reports

**Generate Report**
```
POST /api/reports/generate
```

Generate a report (admin only).

**Export as CSV**
```
POST /api/reports/export-csv
```

Export report as CSV (admin only).

**Export as PDF**
```
POST /api/reports/export-pdf
```

Export report as PDF (admin only).

### Member Attendance

**Get Member Attendance**
```
GET /api/member/attendance
```

Returns member's attendance records.

**Time In**
```
POST /api/member/time-in
```

Log time in for current session.

**Time Out**
```
POST /api/member/time-out
```

Log time out for current session.

### AI Provider

**Switch Provider**
```
POST /api/ai/provider/switch
```

Switch AI provider (admin only).

**Switch API Key**
```
POST /api/ai/api-key/switch
```

Switch AI API key (admin only).

## Rate Limiting

API requests are rate limited to prevent abuse:

- Standard: 60 requests per minute
- Admin: 120 requests per minute

Rate limit information is included in response headers:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1234567890
```

## Error Codes

| Code | Meaning |
|------|---------|
| 200 | OK |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Unprocessable Entity |
| 429 | Too Many Requests |
| 500 | Internal Server Error |

## Examples

### Get Dashboard Data

```bash
curl -X GET http://127.0.0.1:8000/api/dashboard/data \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Time In

```bash
curl -X POST http://127.0.0.1:8000/api/member/time-in \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

### Generate Report

```bash
curl -X POST http://127.0.0.1:8000/api/reports/generate \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "attendance",
    "start_date": "2024-01-01",
    "end_date": "2024-12-31"
  }'
```

## Pagination

List endpoints support pagination:

```
GET /api/endpoint?page=1&per_page=15
```

Response includes pagination metadata:

```json
{
  "data": [],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

## Filtering

Many endpoints support filtering:

```
GET /api/endpoint?filter[status]=active&filter[role]=member
```

## Sorting

List endpoints support sorting:

```
GET /api/endpoint?sort=name&order=asc
```

## Best Practices

1. Always include authentication token
2. Handle rate limiting gracefully
3. Implement error handling
4. Use pagination for large datasets
5. Cache responses when appropriate
6. Monitor API usage
7. Keep API keys secure
