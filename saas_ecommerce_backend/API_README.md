# SaaS Ecommerce Backend API Guide

This document lists all API endpoints available in this backend so you can share it with your friend.

## Base URL

Use your laptop LAN IP when sharing over local network:

- `http://192.168.1.82:8000`

All routes below are prefixed with `/api`.

Example full URL:

- `http://192.168.1.82:8000/api/auth/login`

## Common Response Format

Every API returns JSON in this format:

```json
{
  "success": true,
  "message": "...",
  "data": {}
}
```

On errors:

```json
{
  "success": false,
  "message": "...",
  "data": null
}
```

## Authentication

Protected routes require a Sanctum Bearer token:

```http
Authorization: Bearer <token>
Accept: application/json
Content-Type: application/json
```

You receive the token from:

- `POST /api/auth/register`
- `POST /api/auth/login`

## Public Endpoints

### 1) Send Email OTP

- Method: `POST`
- URL: `/api/auth/email-otp/send`
- Throttle: 5 requests per minute

Request body:

```json
{
  "email": "user@example.com"
}
```

Validation:

- `email`: required, valid email, max 255, must be unique in users table

Success message:

- `OTP sent successfully.`

### 2) Verify Email OTP

- Method: `POST`
- URL: `/api/auth/email-otp/verify`
- Throttle: 10 requests per minute

Request body:

```json
{
  "email": "user@example.com",
  "otp": "123456"
}
```

Validation:

- `email`: required, valid email, max 255, must be unique in users table
- `otp`: required, exactly 6 digits

### 3) Register

- Method: `POST`
- URL: `/api/auth/register`
- Requires successful OTP verification first

Request body:

```json
{
  "full_name": "John Doe",
  "email": "user@example.com",
  "address": "Kathmandu, Nepal",
  "phone_no": "9800000000",
  "password": "Password123!",
  "password_confirmation": "Password123!",
  "device_name": "friend-laptop"
}
```

Validation:

- `full_name`: required, string, max 255
- `email`: required, valid email, unique, max 255
- `address`: nullable, string, max 1000
- `phone_no`: nullable, string, max 20
- `password`: required, confirmed, Laravel default password rule
- `device_name`: nullable, string, max 100

Success data:

- `user`
- `token`
- `token_type` (`Bearer`)

### 4) Login

- Method: `POST`
- URL: `/api/auth/login`

Request body:

```json
{
  "email": "user@example.com",
  "password": "Password123!",
  "device_name": "friend-laptop"
}
```

Validation:

- `email`: required, valid email, max 255
- `password`: required
- `device_name`: nullable, string, max 100

Success data:

- `user`
- `token`
- `token_type` (`Bearer`)

## Protected Endpoints

Add `Authorization: Bearer <token>` header for all endpoints below.

### 5) Logout

- Method: `POST`
- URL: `/api/auth/logout`

Behavior:

- Deletes current access token

### 6) Get Logged-In User Profile

- Method: `GET`
- URL: `/api/user`

Response data:

- authenticated user object

### 7) Update Logged-In User Profile

- Method: `PUT`
- URL: `/api/user`

Request body:

```json
{
  "full_name": "John Updated",
  "email": "new@example.com",
  "address": "Pokhara",
  "phone_no": "9811111111"
}
```

Validation:

- `full_name`: required, string, max 255
- `email`: required, valid email, max 255, unique except current user
- `address`: nullable, string, max 1000
- `phone_no`: nullable, string, max 20

### 8) List Businesses

- Method: `GET`
- URL: `/api/businesses`

Response data:

- all businesses belonging to logged-in user

### 9) Create Business

- Method: `POST`
- URL: `/api/businesses`

Request body:

```json
{
  "business_name": "My Shop",
  "desired_url_name": "my-shop",
  "business_location": "Kathmandu",
  "business_registration_number": "REG-1001",
  "business_email": "shop@example.com",
  "business_phone": "9801234567"
}
```

Validation:

- `business_name`: required, string, max 255
- `desired_url_name`: required, string, max 120, alpha_dash, unique
- `business_location`: required, string, max 255
- `business_registration_number`: required, string, max 120, unique
- `business_email`: nullable, valid email, max 255
- `business_phone`: required, string, max 20

### 10) Get One Business

- Method: `GET`
- URL: `/api/businesses/{business}`

Behavior:

- Returns 403 if business does not belong to authenticated user

### 11) Update Business

- Method: `PUT`
- URL: `/api/businesses/{business}`

Request body:

```json
{
  "business_name": "My Shop Updated",
  "desired_url_name": "my-shop-updated",
  "business_location": "Lalitpur",
  "business_registration_number": "REG-1001",
  "business_email": "updated@example.com",
  "business_phone": "9812345678"
}
```

Validation:

- Same fields as create
- Unique checks ignore current business record

### 12) Delete Business

- Method: `DELETE`
- URL: `/api/businesses/{business}`

Behavior:

- Returns 403 if business does not belong to authenticated user

## Quick cURL Examples

### Login

```bash
curl -X POST "http://192.168.1.82:8000/api/auth/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"user@example.com\",\"password\":\"Password123!\",\"device_name\":\"friend-laptop\"}"
```

### Get Profile (Protected)

```bash
curl -X GET "http://192.168.1.82:8000/api/user" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Notes for LAN Testing

- Start backend with:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

- Use your laptop IP (`192.168.1.82`) from your friend device.
- Both laptops must be on same Wi-Fi network.
- Windows firewall must allow inbound TCP on port 8000.
