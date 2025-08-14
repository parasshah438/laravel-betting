# Laravel Betting Platform API Documentation

## 🎯 Overview

This is a comprehensive RESTful API for an online betting platform built with Laravel. The API provides complete functionality for user management, betting operations, sports data, financial transactions, promotions, and administrative features.

## 🚀 API Base URL

```
http://localhost:8000/api/v1
```

## 🔐 Authentication

The API uses Laravel Sanctum for authentication. Most endpoints require a Bearer token in the Authorization header.

```
Authorization: Bearer {your-access-token}
```

## 📋 API Endpoints Overview

### Authentication (`/auth`)
- `POST /auth/register` - User registration
- `POST /auth/login` - User login
- `POST /auth/logout` - User logout
- `POST /auth/forgot-password` - Password reset request
- `POST /auth/reset-password` - Reset password
- `POST /auth/verify-email` - Verify email address
- `GET /auth/me` - Get current user info
- `POST /auth/change-password` - Change password

### Profile Management (`/profile`)
- `GET /profile` - Get user profile
- `PUT /profile` - Update user profile
- `POST /profile/avatar` - Upload avatar
- `PUT /profile/preferences` - Update preferences
- `GET /profile/kyc-documents` - Get KYC documents
- `POST /profile/kyc-documents` - Upload KYC document

### Wallet & Financial (`/wallet`)
- `GET /wallet` - Get all wallets
- `GET /wallet/{currency}` - Get specific wallet
- `POST /wallet/deposit` - Make deposit
- `POST /wallet/withdraw` - Make withdrawal
- `GET /wallet/transactions` - Get transaction history
- `GET /wallet/deposit/methods` - Get deposit methods
- `GET /wallet/withdraw/methods` - Get withdrawal methods

### Sports Data (`/sports`)
- `GET /sports` - Get all sports
- `GET /sports/{sportId}` - Get specific sport
- `GET /sports/{sportId}/leagues` - Get sport leagues
- `GET /sports/leagues/{leagueId}` - Get league details
- `GET /sports/leagues/{leagueId}/teams` - Get league teams
- `GET /sports/teams/{teamId}` - Get team details

### Matches (`/matches`)
- `GET /matches` - Get all matches
- `GET /matches/live` - Get live matches
- `GET /matches/upcoming` - Get upcoming matches
- `GET /matches/finished` - Get finished matches
- `GET /matches/featured` - Get featured matches
- `GET /matches/{matchId}` - Get match details
- `GET /matches/{matchId}/betting-options` - Get betting options

### Betting (`/bets`)
- `POST /bets` - Place a bet
- `GET /bets` - Get user bets
- `GET /bets/{betId}` - Get specific bet
- `POST /bets/{betId}/cashout` - Cash out bet
- `GET /bets/statistics/overview` - Get betting statistics

### Promotions (`/promotions`)
- `GET /promotions` - Get available promotions
- `GET /promotions/{promotionId}` - Get promotion details
- `POST /promotions/{promotionId}/claim` - Claim promotion
- `POST /promotions/promo-code` - Apply promo code
- `GET /promotions/bonuses` - Get user bonuses
- `GET /promotions/bonuses/{bonusId}/progress` - Get bonus progress

### Notifications (`/notifications`)
- `GET /notifications` - Get notifications
- `GET /notifications/unread-count` - Get unread count
- `PUT /notifications/{notificationId}/read` - Mark as read
- `PUT /notifications/mark-all-read` - Mark all as read
- `DELETE /notifications/{notificationId}` - Delete notification
- `GET /notifications/preferences` - Get preferences
- `PUT /notifications/preferences` - Update preferences

### Admin (`/admin`) - Requires admin role
- `GET /admin/dashboard` - Dashboard statistics
- `GET /admin/users` - Get users with filters
- `GET /admin/users/{userId}` - Get user details
- `PUT /admin/users/{userId}/status` - Update user status
- `GET /admin/bets` - Get all bets
- `GET /admin/transactions` - Get all transactions
- `GET /admin/settings` - Get system settings
- `PUT /admin/settings` - Update system settings
- `GET /admin/audit-logs` - Get audit logs

### Admin Match Management (`/admin/matches`)
- `GET /admin/matches` - Get matches for admin
- `POST /admin/matches` - Create new match
- `PUT /admin/matches/{matchId}` - Update match
- `POST /admin/matches/{matchId}/betting-markets` - Add betting market
- `PUT /admin/matches/betting-options/{optionId}/odds` - Update odds
- `PUT /admin/matches/{matchId}/toggle-betting` - Suspend/resume betting

### Public (`/public`)
- `GET /public/system-info` - System information
- `GET /public/health` - Health check
- `GET /public/docs` - API documentation links

## 📄 Request/Response Format

### Standard Response Format

All API responses follow this structure:

```json
{
    "success": true|false,
    "message": "Response message",
    "data": {
        // Response data
    },
    "errors": {
        // Validation errors (if any)
    }
}
```

### Pagination Response

For paginated endpoints:

```json
{
    "success": true,
    "data": {
        "items": [...],
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 20,
            "total": 100
        }
    }
}
```

## 🔑 Authentication Examples

### Register

```bash
POST /api/v1/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+1234567890",
    "date_of_birth": "1990-01-01",
    "country": "US",
    "currency": "USD",
    "agree_terms": true
}
```

### Login

```bash
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

Response:
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {...},
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

## 💰 Betting Examples

### Place Single Bet

```bash
POST /api/v1/bets
Authorization: Bearer {token}
Content-Type: application/json

{
    "selections": [
        {
            "bet_option_id": 1,
            "odds": 2.50
        }
    ],
    "stake": 10,
    "bet_type": "single",
    "currency": "USD"
}
```

### Place Multiple Bet

```bash
POST /api/v1/bets
Authorization: Bearer {token}
Content-Type: application/json

{
    "selections": [
        {
            "bet_option_id": 1,
            "odds": 2.50
        },
        {
            "bet_option_id": 2,
            "odds": 1.80
        }
    ],
    "stake": 20,
    "bet_type": "multiple",
    "currency": "USD"
}
```

## 💳 Wallet Examples

### Make Deposit

```bash
POST /api/v1/wallet/deposit
Authorization: Bearer {token}
Content-Type: application/json

{
    "amount": 100,
    "currency": "USD",
    "payment_method": "credit_card",
    "payment_details": {
        "card_number": "4111111111111111",
        "expiry": "12/25",
        "cvv": "123"
    }
}
```

### Make Withdrawal

```bash
POST /api/v1/wallet/withdraw
Authorization: Bearer {token}
Content-Type: application/json

{
    "amount": 50,
    "currency": "USD",
    "withdrawal_method": "bank_transfer",
    "withdrawal_details": {
        "account_number": "1234567890",
        "routing_number": "123456789"
    }
}
```

## 🎁 Promotion Examples

### Apply Promo Code

```bash
POST /api/v1/promotions/promo-code
Authorization: Bearer {token}
Content-Type: application/json

{
    "promo_code": "WELCOME100"
}
```

## 🛡️ Security Features

- JWT-based authentication with Laravel Sanctum
- Role-based access control (user, admin, super_admin)
- Rate limiting on sensitive endpoints
- Request validation and sanitization
- Password hashing with bcrypt
- Two-factor authentication support
- IP address and user agent logging
- Comprehensive audit logging

## 📊 Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `429` - Too Many Requests
- `500` - Internal Server Error

## 🧪 Testing

### Using Postman

1. Import the Postman collection from `/public/postman-collection.json`
2. Set the `base_url` variable to your API URL
3. Login to get an auth token
4. The token will be automatically set for authenticated requests

### Using cURL

```bash
# Health check
curl -X GET "http://localhost:8000/api/v1/public/health"

# Login
curl -X POST "http://localhost:8000/api/v1/auth/login" \
     -H "Content-Type: application/json" \
     -d '{"email":"john@example.com","password":"password123"}'

# Get matches (with token)
curl -X GET "http://localhost:8000/api/v1/matches" \
     -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## 🔄 Real-time Features

### WebSocket Events (Coming Soon)

- Live betting odds updates
- Match score updates
- Bet settlement notifications
- Balance updates
- System announcements

### Supported Events

- `match.started`
- `match.finished`
- `odds.updated`
- `bet.settled`
- `balance.updated`
- `notification.received`

## 📈 Rate Limits

- Authentication endpoints: 60 requests per minute
- General API endpoints: 1000 requests per minute
- Admin endpoints: 300 requests per minute
- File upload endpoints: 10 requests per minute

## 🌍 Supported Features

### Currencies
- USD, EUR, GBP, CAD, AUD
- Bitcoin (BTC), Ethereum (ETH)
- More cryptocurrencies available

### Languages
- English (en)
- Spanish (es)
- French (fr)
- German (de)
- More languages available

### Payment Methods
- Credit/Debit Cards (Visa, MasterCard, Amex)
- Bank Transfers
- Digital Wallets (PayPal, Skrill, Neteller)
- Cryptocurrencies (Bitcoin, Ethereum)

### Sports Coverage
- Football (Soccer)
- Basketball
- Tennis
- American Football
- Baseball
- Hockey
- And many more...

## 🔧 Environment Setup

### Required Environment Variables

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_betting
DB_USERNAME=root
DB_PASSWORD=

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:3000

# Payment Gateways
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
PAYPAL_CLIENT_ID=...
PAYPAL_CLIENT_SECRET=...

# External APIs
SPORTS_API_KEY=...
ODDS_API_KEY=...

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=...
MAIL_PASSWORD=...
```

## 🚀 Deployment

### Laravel Artisan Commands

```bash
# Install dependencies
composer install

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Install Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

# Create storage link
php artisan storage:link

# Start server
php artisan serve
```

## 📞 Support

For API support and questions:
- Email: api-support@bettingplatform.com
- Documentation: `/api/v1/public/docs`
- Postman Collection: `/postman-collection.json`

---

**Version:** 1.0.0  
**Last Updated:** August 2025  
**Status:** Active Development
