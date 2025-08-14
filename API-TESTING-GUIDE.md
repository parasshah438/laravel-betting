# 🚀 BetMaster Pro API Testing Guide

## 📁 Postman Collection Overview

This collection includes **65+ API endpoints** organized in 8 main categories:

### 🔐 Authentication (6 endpoints)
- User registration & login
- Password management
- Token-based authentication
- Auto-token storage

### 👤 User Profile (5 endpoints)
- Profile management
- Avatar upload
- KYC document handling
- Preferences management

### 💰 Wallet Operations (6 endpoints)
- Multi-currency wallet support
- Deposit/withdrawal operations
- Transaction history
- Payment method management

### 🏈 Sports & Matches (8 endpoints)
- Sports data retrieval
- League & team information
- Live & upcoming matches
- Match statistics & betting options

### 🎯 Betting Operations (6 endpoints)
- Single & multiple bet placement
- Bet management & tracking
- Cash-out functionality
- Betting statistics

### 🎁 Promotions & Bonuses (4 endpoints)
- Promotion management
- Promo code application
- Bonus tracking
- User rewards

### 🔔 Notifications (4 endpoints)
- Notification management
- Read/unread status
- Bulk operations
- User preferences

### ⚙️ Admin Operations (5 endpoints)
- Dashboard statistics
- User management
- Match creation & management
- Odds management

## 🛠️ Setup Instructions

### 1. Import Collection
```
File → Import → Select "BetMaster-Pro-API.postman_collection.json"
```

### 2. Import Environment
```
File → Import → Select "BetMaster-Pro-Environment.postman_environment.json"
```

### 3. Set Environment
```
Top-right dropdown → Select "BetMaster Pro - Development"
```

### 4. Configure Base URL
```
Environment: base_url = http://localhost:8000
```

## 🔑 Authentication Flow

### Step 1: Register New User
```http
POST /api/v1/auth/register
```
**Auto-saves**: `auth_token` and `user_id` to environment

### Step 2: Login Existing User
```http
POST /api/v1/auth/login
```
**Auto-saves**: `auth_token` for subsequent requests

### Step 3: Test Protected Routes
All authenticated endpoints use: `Bearer {{auth_token}}`

## 🎯 Testing Workflow

### Quick Start Testing Sequence:
1. **Health Check** → `GET /api/v1/public/health`
2. **Register User** → `POST /api/v1/auth/register`
3. **Get Profile** → `GET /api/v1/profile`
4. **Get Sports** → `GET /api/v1/sports`
5. **Get Matches** → `GET /api/v1/matches`
6. **Get Wallet** → `GET /api/v1/wallet`
7. **Place Bet** → `POST /api/v1/bets`

### Advanced Testing:
1. **Deposit Money** → `POST /api/v1/wallet/deposit`
2. **Get Live Matches** → `GET /api/v1/matches/live`
3. **Place Multiple Bets** → `POST /api/v1/bets` (multiple selections)
4. **Cash Out Bet** → `POST /api/v1/bets/{{bet_id}}/cashout`
5. **Check Statistics** → `GET /api/v1/bets/statistics/overview`

## 📊 Environment Variables

### Auto-Managed Variables:
- `auth_token` - JWT token (auto-saved on login)
- `user_id` - Current user ID (auto-saved)
- `match_id` - Selected match ID (auto-saved)
- `bet_id` - Placed bet ID (auto-saved)

### Manual Configuration:
- `base_url` - API base URL (default: localhost:8000)
- `admin_token` - Admin JWT token (manual setup)

## 🔍 Debugging Features

### Request Logging:
- Automatic request method & URL logging
- Timestamp tracking
- Response time measurement

### Response Handling:
- Auto-success/error detection
- JSON response parsing
- Pagination data extraction
- Error message logging

### Console Output:
```
Making POST request to: http://localhost:8000/api/v1/auth/login
Response status: 200
Response time: 245ms
✅ Request successful
```

## 📋 Sample Test Data

### User Registration:
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john.doe@example.com",
  "password": "Password123!",
  "phone": "+1234567890",
  "country": "US",
  "currency": "USD"
}
```

### Single Bet:
```json
{
  "bet_type": "single",
  "stake": 25.00,
  "currency": "USD",
  "selections": [
    {
      "bet_option_id": 1,
      "odds": 2.50
    }
  ]
}
```

### Deposit:
```json
{
  "amount": 100.00,
  "currency": "USD",
  "method": "credit_card",
  "payment_details": {
    "card_number": "4111111111111111",
    "expiry_month": "12",
    "expiry_year": "2025"
  }
}
```

## 🚦 Status Code Guide

- **200** - Success (GET, PUT)
- **201** - Created (POST)
- **204** - No Content (DELETE)
- **400** - Bad Request (validation error)
- **401** - Unauthorized (invalid/missing token)
- **403** - Forbidden (insufficient permissions)
- **404** - Not Found
- **422** - Unprocessable Entity (validation failed)
- **500** - Server Error

## 🔧 Common Issues & Solutions

### Issue: "Unauthorized" Error
**Solution**: Check if `auth_token` is set in environment

### Issue: "Validation Failed"
**Solution**: Check request body format matches API expectations

### Issue: "Route Not Found"
**Solution**: Verify `base_url` in environment settings

### Issue: Admin Routes Failing
**Solution**: Ensure you have admin role and `admin_token` set

## 📈 Performance Testing

### Load Testing Endpoints:
- `GET /api/v1/matches/live` (high frequency)
- `GET /api/v1/sports` (cached data)
- `POST /api/v1/bets` (critical path)
- `GET /api/v1/wallet/transactions` (pagination)

### Recommended Test Patterns:
1. **Sequential Testing** - Follow user journey flow
2. **Parallel Testing** - Test multiple endpoints simultaneously
3. **Stress Testing** - Rapid API calls to same endpoint
4. **Edge Case Testing** - Invalid data scenarios

## 🎯 Next Steps

After successful API testing:
1. **Frontend Integration** - Connect React/Vue frontend
2. **WebSocket Testing** - Real-time features
3. **Performance Optimization** - Caching, indexing
4. **Security Testing** - Penetration testing
5. **Load Testing** - High traffic simulation

---

## 📞 Support

For API issues or questions:
- Check Laravel logs: `storage/logs/laravel.log`
- Enable debug mode: `APP_DEBUG=true` in `.env`
- Use Postman console for detailed request/response data

**Happy Testing! 🚀**
