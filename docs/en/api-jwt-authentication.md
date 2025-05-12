# JWT Authentication for API

Webillium CMS supports JWT (JSON Web Token) authentication for RESTful APIs. This guide explains how to use the JWT authentication endpoints for secure API access.

## What is JWT?

JWT (JSON Web Token) is an open standard (RFC 7519) that defines a compact and self-contained way for securely transmitting information between parties as a JSON object. The token contains all the required information about a user, eliminating the need for database queries for each request.

## Features

- Secure, stateless authentication for your API
- Tokens contain user data, reducing database queries
- Token expiration and refresh capability
- IP address and User-Agent verification (optional)
- Role-based access control through user privileges included in the token

## Configuration

JWT authentication settings can be configured in your `.env` file:

```
JWT_SECRET=your_secret_key_here
JWT_TTL=1440
JWT_REFRESH_TTL=20160
JWT_VERIFY_IP=true
JWT_VERIFY_USER_AGENT=true
```

| Setting | Description | Default |
|---------|-------------|---------|
| JWT_SECRET | Secret key used to sign tokens | webillium_jwt_secret_key |
| JWT_TTL | Token lifetime in minutes | 1440 (24 hours) |
| JWT_REFRESH_TTL | Refresh token lifetime in minutes | 20160 (14 days) |
| JWT_VERIFY_IP | Whether to verify IP address | true |
| JWT_VERIFY_USER_AGENT | Whether to verify User-Agent | true |

## API Endpoints

### Login

Authenticate a user and get a JWT token.

- **URL**: `/api/jwt/login`
- **Method**: `POST`
- **Authentication**: None

**Request body**:
```json
{
  "email": "user@example.com",
  "password": "yourpassword"
}
```

**Success Response (200 OK)**:
```json
{
  "api_status": 1,
  "api_message": "Login successful",
  "data": {
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "token_type": "Bearer",
    "expires_in": 86400,
    "user": {
      "id": 1,
      "name": "Administrator",
      "email": "admin@example.com",
      "photo": "http://example.com/uploads/avatar.jpg"
    }
  }
}
```

**Error Response (401 Unauthorized)**:
```json
{
  "api_status": 0,
  "api_message": "Email or password is invalid!"
}
```

### Refresh Token

Get a new token when the current one is about to expire.

- **URL**: `/api/jwt/refresh`
- **Method**: `POST`
- **Authentication**: Bearer Token

**Headers**:
```
Authorization: Bearer your_token_here
```

**Success Response (200 OK)**:
```json
{
  "api_status": 1,
  "api_message": "Token refreshed successfully",
  "data": {
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "token_type": "Bearer",
    "expires_in": 86400
  }
}
```

**Error Response (401 Unauthorized)**:
```json
{
  "api_status": 0,
  "api_message": "Invalid token!",
  "error": "Token has expired"
}
```

### Get Current User

Get details for the currently authenticated user.

- **URL**: `/api/jwt/me`
- **Method**: `GET`
- **Authentication**: Bearer Token

**Headers**:
```
Authorization: Bearer your_token_here
```

**Success Response (200 OK)**:
```json
{
  "api_status": 1,
  "api_message": "Success",
  "data": {
    "user": {
      "id": 1,
      "name": "Administrator",
      "email": "admin@example.com",
      "photo": "http://example.com/uploads/avatar.jpg",
      "privileges": {
        "role": "Superadministrator",
        "modules": {
          "Users Management": {
            "create": true,
            "read": true,
            "edit": true,
            "delete": true
          },
          "Menu Management": {
            "create": true,
            "read": true,
            "edit": true,
            "delete": true
          }
        }
      }
    }
  }
}
```

### Logout

Invalidate the current token.

- **URL**: `/api/jwt/logout`
- **Method**: `POST`
- **Authentication**: Bearer Token

**Headers**:
```
Authorization: Bearer your_token_here
```

**Success Response (200 OK)**:
```json
{
  "api_status": 1,
  "api_message": "Successfully logged out"
}
```

## Using JWT Authentication in Your Requests

Once you've obtained a JWT token via the login endpoint, you can include it in the Authorization header of your subsequent API requests:

```
Authorization: Bearer your_token_here
```

Example using cURL:

```bash
curl -X GET \
  https://your-domain.com/api/some-endpoint \
  -H 'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...' \
  -H 'Content-Type: application/json'
```

Example using JavaScript Fetch:

```javascript
fetch('https://your-domain.com/api/some-endpoint', {
  method: 'GET',
  headers: {
    'Authorization': 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...',
    'Content-Type': 'application/json',
  },
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
```

## Token Structure

The JWT token contains the following information:

- **iss** (Issuer): Your application URL
- **sub** (Subject): User ID
- **iat** (Issued At): Timestamp when the token was created
- **exp** (Expiration Time): Timestamp when the token expires
- **user**: Object containing user information
  - **id**: User ID
  - **name**: User name
  - **email**: User email
  - **privileges**: Object containing user privileges
  - **photo**: URL to user's photo (if available)

## Security Considerations

1. **Always use HTTPS** when implementing JWT authentication
2. **Store tokens securely** on the client side (e.g., HttpOnly cookies, secure local storage)
3. **Keep the JWT_SECRET key secure** and use a strong, unique value
4. **Set appropriate token expiration times** to limit the window of opportunity for token misuse
5. **Implement token refresh** to maintain user sessions without requiring frequent re-authentication

## Troubleshooting

Common issues and solutions:

### "Invalid token" error
- Check if the token is correctly formatted in the Authorization header
- Verify the token hasn't expired
- Ensure you're using the correct JWT_SECRET

### "Token has expired" error
- Use the refresh token endpoint to get a new token
- Adjust the JWT_TTL value if tokens are expiring too quickly

### IP or User-Agent verification failures
- These verifications can cause issues for users with dynamic IPs or when switching devices
- Consider disabling these verifications in development or for specific use cases by setting `JWT_VERIFY_IP=false` and/or `JWT_VERIFY_USER_AGENT=false` in your .env file

## What's Next
- [API Documentation](./api-documentation.md)
- [Back To Index](./index.md)