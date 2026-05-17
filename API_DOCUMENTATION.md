# Kiava HR API Documentation

## Authentication

All API requests require Bearer token authentication (when implemented).

```
Authorization: Bearer {token}
```

## Endpoints

### Authentication

#### Login
```
POST /auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password",
  "remember": false
}

Response: 200 OK
Redirects to dashboard on success
```

#### Logout
```
POST /auth/logout
Authorization: Bearer {token}

Response: 200 OK
Redirects to login
```

#### Forgot Password
```
POST /auth/forgot-password
Content-Type: application/json

{
  "email": "user@example.com"
}

Response: 200 OK
{
  "message": "Password reset link sent"
}
```

#### Reset Password
```
POST /auth/reset-password
Content-Type: application/json

{
  "token": "{reset_token}",
  "email": "user@example.com",
  "password": "newpassword",
  "password_confirmation": "newpassword"
}

Response: 200 OK
Redirects to login with success message
```

#### Force Password Change
```
POST /auth/force-password-change
Authorization: Bearer {token}
Content-Type: application/json

{
  "password": "newpassword",
  "password_confirmation": "newpassword"
}

Response: 200 OK
Redirects to dashboard
```

### Documents

#### List Documents
```
GET /api/documents
Authorization: Bearer {token}

Query Parameters:
- status: pending|approved|rejected
- company_id: {id}
- employee_id: {id}

Response: 200 OK
{
  "success": true,
  "data": [
    {
      "id": 1,
      "employee_id": 1,
      "document_requirement_id": 1,
      "status": "approved",
      "expiration_date": "2025-12-31",
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

#### Upload Document
```
POST /api/documents/upload
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "document_requirement_id": 1,
  "file": <binary>,
  "expiration_date": "2025-12-31",
  "notes": "Optional notes"
}

Response: 201 Created
{
  "success": true,
  "message": "Document uploaded successfully",
  "data": {
    "id": 1,
    "status": "pending"
  }
}
```

#### Approve Document
```
POST /api/documents/{id}/approve
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "message": "Document approved"
}
```

#### Reject Document
```
POST /api/documents/{id}/reject
Authorization: Bearer {token}
Content-Type: application/json

{
  "rejection_reason": "Document is unclear"
}

Response: 200 OK
{
  "success": true,
  "message": "Document rejected"
}
```

### Notifications

#### Get Notifications
```
GET /api/notifications
Authorization: Bearer {token}

Query Parameters:
- unread: true|false
- type: document_expiring|document_expired|approval_status

Response: 200 OK
{
  "success": true,
  "data": [
    {
      "id": 1,
      "type": "document_expiring",
      "title": "Document Expiring",
      "message": "Your medical license expires in 30 days",
      "read_at": null,
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

#### Mark Notification as Read
```
POST /api/notifications/{id}/read
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "message": "Notification marked as read"
}
```

#### Mark All as Read
```
POST /api/notifications/read-all
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "message": "All notifications marked as read"
}
```

### Employees

#### List Employees
```
GET /api/employees
Authorization: Bearer {token}

Query Parameters:
- search: name|email
- department: {dept}
- status: active|inactive

Response: 200 OK
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "department": "Nursing",
      "compliance_percentage": 85,
      "missing_documents_count": 2
    }
  ]
}
```

#### Get Employee Details
```
GET /api/employees/{id}
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "documents": [
      {
        "id": 1,
        "requirement_name": "Medical License",
        "status": "approved",
        "expiration_date": "2025-12-31"
      }
    ]
  }
}
```

### Compliance Reports

#### Get Compliance Dashboard
```
GET /api/compliance/dashboard
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "data": {
    "total_employees": 50,
    "compliant_employees": 42,
    "compliance_percentage": 84,
    "missing_documents_count": 12,
    "expired_documents_count": 3,
    "expiring_soon_count": 8
  }
}
```

#### Generate Compliance Report
```
GET /api/compliance/report
Authorization: Bearer {token}

Query Parameters:
- format: pdf|csv|json
- date_range: this_month|this_quarter|this_year
- department: {dept}

Response: 200 OK
{
  "success": true,
  "data": {
    "report_id": "RPT-2024-001",
    "generated_at": "2024-01-15T10:30:00Z",
    "summary": {}
  }
}
```

## Error Responses

### 400 Bad Request
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required"]
  }
}
```

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Not authorized to perform this action"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### 500 Server Error
```json
{
  "success": false,
  "message": "An internal server error occurred"
}
```

## Rate Limiting

- API requests are limited to 60 per minute per user
- Limit headers are included in all responses:
  - `X-RateLimit-Limit: 60`
  - `X-RateLimit-Remaining: 45`
  - `X-RateLimit-Reset: 1642252800`

## WebSocket Channels (Real-Time)

### Broadcasting Channels

#### Company Updates
```
Channel: company.{company_id}
Events:
- document.uploaded
- document.approved
- document.rejected
- employee.added
- employee.removed
```

#### User Notifications
```
Channel: user.{user_id}
Events:
- notification.sent
- document.status_changed
- expiration.alert
```

#### Compliance Updates
```
Channel: company.{company_id}.compliance
Events:
- compliance.updated
- report.generated
- alert.triggered
```

## Implementation Notes

- All timestamps are in UTC ISO-8601 format
- Sensitive data (SSN) is encrypted and not returned in API responses
- File uploads are stored in private storage with signed URLs
- All requests must include a valid tenant company context
- Pagination is applied with default 15 items per page, max 100
