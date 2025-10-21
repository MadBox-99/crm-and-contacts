# API Documentation

## Overview

This CRM system provides a RESTful API for managing customers, orders, campaigns, and other resources. The API uses token-based authentication via Laravel Sanctum.

**Base URL**: `/api/v1`

**Authentication**: Bearer Token

**Response Format**: JSON

**API Version**: v1

## Authentication

All API requests (except login) require authentication using a Bearer token.

### Login

Authenticate a user and receive an API token.

**Endpoint**: `POST /api/v1/login`

**Request Body**:
```json
{
  "email": "user@example.com",
  "password": "password",
  "device_name": "mobile-app"
}
```

**Response** (200 OK):
```json
{
  "token": "1|abc123def456...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "created_at": "2025-10-08T10:30:00Z"
  }
}
```

**Error Responses**:
- `422 Unprocessable Entity`: Invalid credentials
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

### Get Current User

Get the authenticated user's information.

**Endpoint**: `GET /api/v1/me`

**Headers**:
```
Authorization: Bearer YOUR_TOKEN
```

**Response** (200 OK):
```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "created_at": "2025-10-08T10:30:00Z"
  }
}
```

### Logout

Revoke the current access token.

**Endpoint**: `POST /api/v1/logout`

**Headers**:
```
Authorization: Bearer YOUR_TOKEN
```

**Response** (200 OK):
```json
{
  "message": "Logged out successfully"
}
```

## Customers

### List Customers

Retrieve a paginated list of customers with optional filtering.

**Endpoint**: `GET /api/v1/customers`

**Headers**:
```
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters**:
- `page` (integer, optional): Page number (default: 1)
- `per_page` (integer, optional): Items per page (default: 15, max: 100)
- `search` (string, optional): Search by name, email, or unique identifier
- `type` (string, optional): Filter by customer type (B2B or B2C)

**Example Request**:
```bash
GET /api/v1/customers?page=1&per_page=20&search=acme&type=B2B
```

**Response** (200 OK):
```json
{
  "data": [
    {
      "id": 1,
      "unique_identifier": "CUST-001",
      "name": "Acme Corporation",
      "type": "B2B",
      "email": "contact@acme.com",
      "phone": "+36301234567",
      "tax_number": "12345678-1-23",
      "website": "https://acme.com",
      "industry": "Technology",
      "annual_revenue": "5000000.00",
      "employee_count": 50,
      "is_active": true,
      "created_at": "2025-10-08T10:00:00Z",
      "updated_at": "2025-10-08T10:00:00Z"
    }
  ],
  "links": {
    "first": "/api/v1/customers?page=1",
    "last": "/api/v1/customers?page=5",
    "prev": null,
    "next": "/api/v1/customers?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "path": "/api/v1/customers",
    "per_page": 15,
    "to": 15,
    "total": 73
  }
}
```

### Get Customer

Retrieve a single customer by ID.

**Endpoint**: `GET /api/v1/customers/{id}`

**Headers**:
```
Authorization: Bearer YOUR_TOKEN
```

**Response** (200 OK):
```json
{
  "data": {
    "id": 1,
    "unique_identifier": "CUST-001",
    "name": "Acme Corporation",
    "type": "B2B",
    "email": "contact@acme.com",
    "phone": "+36301234567",
    "tax_number": "12345678-1-23",
    "website": "https://acme.com",
    "industry": "Technology",
    "annual_revenue": "5000000.00",
    "employee_count": 50,
    "is_active": true,
    "created_at": "2025-10-08T10:00:00Z",
    "updated_at": "2025-10-08T10:00:00Z"
  }
}
```

**Error Responses**:
- `404 Not Found`: Customer not found
- `403 Forbidden`: Insufficient permissions

### Create Customer

Create a new customer.

**Endpoint**: `POST /api/v1/customers`

**Headers**:
```
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body** (B2B Customer):
```json
{
  "unique_identifier": "CUST-002",
  "name": "Tech Solutions Ltd",
  "type": "B2B",
  "email": "info@techsolutions.com",
  "phone": "+36301234568",
  "tax_number": "87654321-2-43",
  "website": "https://techsolutions.com",
  "industry": "IT Services",
  "annual_revenue": "3000000",
  "employee_count": 30,
  "is_active": true
}
```

**Request Body** (B2C Customer):
```json
{
  "unique_identifier": "CUST-003",
  "name": "John Smith",
  "type": "B2C",
  "email": "john.smith@example.com",
  "phone": "+36301234569",
  "date_of_birth": "1985-05-15",
  "gender": "male",
  "preferred_language": "hu",
  "is_active": true
}
```

**Response** (201 Created):
```json
{
  "data": {
    "id": 2,
    "unique_identifier": "CUST-002",
    "name": "Tech Solutions Ltd",
    "type": "B2B",
    "email": "info@techsolutions.com",
    "phone": "+36301234568",
    "tax_number": "87654321-2-43",
    "website": "https://techsolutions.com",
    "industry": "IT Services",
    "annual_revenue": "3000000.00",
    "employee_count": 30,
    "is_active": true,
    "created_at": "2025-10-08T14:30:00Z",
    "updated_at": "2025-10-08T14:30:00Z"
  }
}
```

**Validation Rules**:
- `unique_identifier`: required, unique, max 100 characters
- `name`: required, max 255 characters
- `type`: required, must be "B2B" or "B2C"
- `email`: required, valid email, unique
- `phone`: nullable, max 30 characters
- `tax_number`: required if type is B2B
- `date_of_birth`: required if type is B2C, valid date

**Error Responses**:
- `422 Unprocessable Entity`: Validation error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."],
    "tax_number": ["The tax number field is required when type is B2B."]
  }
}
```
- `403 Forbidden`: Insufficient permissions

### Update Customer

Update an existing customer.

**Endpoint**: `PUT /api/v1/customers/{id}`

**Headers**:
```
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body** (partial update allowed):
```json
{
  "name": "Acme Corporation Inc",
  "email": "info@acme.com",
  "phone": "+36301234570",
  "annual_revenue": "6000000"
}
```

**Response** (200 OK):
```json
{
  "data": {
    "id": 1,
    "unique_identifier": "CUST-001",
    "name": "Acme Corporation Inc",
    "type": "B2B",
    "email": "info@acme.com",
    "phone": "+36301234570",
    "tax_number": "12345678-1-23",
    "website": "https://acme.com",
    "industry": "Technology",
    "annual_revenue": "6000000.00",
    "employee_count": 50,
    "is_active": true,
    "created_at": "2025-10-08T10:00:00Z",
    "updated_at": "2025-10-08T15:45:00Z"
  }
}
```

**Error Responses**:
- `404 Not Found`: Customer not found
- `422 Unprocessable Entity`: Validation error
- `403 Forbidden`: Insufficient permissions

### Delete Customer

Soft delete a customer.

**Endpoint**: `DELETE /api/v1/customers/{id}`

**Headers**:
```
Authorization: Bearer YOUR_TOKEN
```

**Response** (204 No Content)

**Error Responses**:
- `404 Not Found`: Customer not found
- `403 Forbidden`: Insufficient permissions

## Error Handling

The API uses standard HTTP status codes to indicate success or failure.

### Status Codes

- `200 OK`: Request succeeded
- `201 Created`: Resource created successfully
- `204 No Content`: Request succeeded with no response body
- `400 Bad Request`: Invalid request format
- `401 Unauthorized`: Missing or invalid authentication token
- `403 Forbidden`: Authenticated but insufficient permissions
- `404 Not Found`: Resource not found
- `422 Unprocessable Entity`: Validation error
- `429 Too Many Requests`: Rate limit exceeded
- `500 Internal Server Error`: Server error

### Error Response Format

All errors follow this format:

```json
{
  "message": "Human readable error message",
  "errors": {
    "field_name": [
      "Validation error message"
    ]
  }
}
```

### Authentication Errors

**Missing Token**:
```json
{
  "message": "Unauthenticated."
}
```

**Invalid Token**:
```json
{
  "message": "Unauthenticated."
}
```

**Expired Token**:
```json
{
  "message": "Token has expired."
}
```

## Rate Limiting

The API implements rate limiting to prevent abuse.

**Limits**:
- Authenticated requests: 60 requests per minute
- Unauthenticated requests: 10 requests per minute

**Headers**:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 58
X-RateLimit-Reset: 1696776000
```

When rate limit is exceeded, you'll receive a `429 Too Many Requests` response:

```json
{
  "message": "Too many requests. Please try again later."
}
```

## Permissions

Each endpoint requires specific permissions. The authenticated user must have the appropriate permission to access the resource.

### Customer Permissions

- `view_any_customer`: List customers
- `view_customer`: View a specific customer
- `create_customer`: Create new customers
- `update_customer`: Update existing customers
- `delete_customer`: Delete customers
- `restore_customer`: Restore soft-deleted customers
- `force_delete_customer`: Permanently delete customers

### Permission Errors

When a user lacks the required permission:

```json
{
  "message": "This action is unauthorized."
}
```

## Pagination

List endpoints support pagination with the following query parameters:

- `page`: The page number (default: 1)
- `per_page`: Items per page (default: 15, max: 100)

Response includes pagination metadata:

```json
{
  "data": [...],
  "links": {
    "first": "https://example.com/api/v1/customers?page=1",
    "last": "https://example.com/api/v1/customers?page=10",
    "prev": null,
    "next": "https://example.com/api/v1/customers?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "path": "https://example.com/api/v1/customers",
    "per_page": 15,
    "to": 15,
    "total": 142
  }
}
```

## Filtering and Searching

### Search

Use the `search` parameter to search across multiple fields:

```
GET /api/v1/customers?search=acme
```

Searches in: name, email, unique_identifier

### Type Filter

Filter customers by type:

```
GET /api/v1/customers?type=B2B
```

Valid values: `B2B`, `B2C`

### Combining Filters

```
GET /api/v1/customers?search=tech&type=B2B&per_page=25&page=2
```

## Date Formats

All dates are in ISO 8601 format with timezone:

```
2025-10-08T14:30:00Z
```

When sending dates in requests, use the format:
```
YYYY-MM-DD
```

Example: `2025-10-08`

## Best Practices

### 1. Use Pagination

Always use pagination for list endpoints to improve performance:

```
GET /api/v1/customers?per_page=50
```

### 2. Filter Data

Use filters to reduce payload size:

```
GET /api/v1/customers?type=B2B&is_active=true
```

### 3. Handle Errors Gracefully

Always check the response status code and handle errors appropriately:

```javascript
try {
  const response = await fetch('/api/v1/customers', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });

  if (!response.ok) {
    const error = await response.json();
    console.error('API Error:', error.message);
  }

  const data = await response.json();
  // Process data
} catch (error) {
  console.error('Network Error:', error);
}
```

### 4. Store Tokens Securely

- Never expose tokens in client-side code
- Store tokens securely (e.g., HttpOnly cookies, secure storage)
- Implement token refresh mechanism if needed

### 5. Respect Rate Limits

Monitor rate limit headers and implement exponential backoff:

```javascript
const rateLimitRemaining = response.headers.get('X-RateLimit-Remaining');
if (rateLimitRemaining < 5) {
  // Slow down requests
}
```

## Code Examples

### PHP (using Guzzle)

```php
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'https://your-domain.com/api/v1/',
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ]
]);

// Get customers
$response = $client->get('customers', [
    'query' => [
        'type' => 'B2B',
        'per_page' => 20
    ]
]);

$customers = json_decode($response->getBody(), true);
```

### JavaScript (using Fetch API)

```javascript
const API_BASE = 'https://your-domain.com/api/v1';
const token = 'your-token-here';

// Create customer
async function createCustomer(customerData) {
  const response = await fetch(`${API_BASE}/customers`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(customerData)
  });

  if (!response.ok) {
    throw new Error('Failed to create customer');
  }

  return await response.json();
}

// Usage
const newCustomer = await createCustomer({
  unique_identifier: 'CUST-004',
  name: 'New Company Ltd',
  type: 'B2B',
  email: 'contact@newcompany.com',
  phone: '+36301234571',
  tax_number: '11111111-1-11'
});
```

### Python (using requests)

```python
import requests

API_BASE = 'https://your-domain.com/api/v1'
token = 'your-token-here'

headers = {
    'Authorization': f'Bearer {token}',
    'Content-Type': 'application/json'
}

# Get customers
response = requests.get(
    f'{API_BASE}/customers',
    headers=headers,
    params={'type': 'B2B', 'per_page': 20}
)

customers = response.json()

# Create customer
new_customer = {
    'unique_identifier': 'CUST-005',
    'name': 'Python Company',
    'type': 'B2B',
    'email': 'info@pythoncompany.com',
    'phone': '+36301234572',
    'tax_number': '22222222-2-22'
}

response = requests.post(
    f'{API_BASE}/customers',
    headers=headers,
    json=new_customer
)

created_customer = response.json()
```

## Changelog

### Version 1.0.0 (2025-10-08)

- Initial API release
- Customer management endpoints
- Bearer token authentication
- Pagination and filtering support

## Support

For API support, please contact:
- Email: api-support@yourcompany.com
- Documentation: https://your-domain.com/docs/api
- Status Page: https://status.your-domain.com
