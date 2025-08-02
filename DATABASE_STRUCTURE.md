# Online Betting Platform - Database Structure

## Database Schema Overview

This document outlines the comprehensive database structure for an online betting platform built with Laravel.

## Core Tables Summary

### 🔐 **Authentication & User Management**
- `users` - Main user accounts
- `user_profiles` - Extended user information
- `kyc_documents` - KYC verification documents
- `user_sessions` - Active user sessions

### 💰 **Financial System**
- `wallets` - User wallet balances (multi-currency)
- `transactions` - All financial transactions
- `betting_limits` - User betting limits (daily/weekly/monthly)

### 🏆 **Sports & Games**
- `sports` - Available sports categories
- `leagues` - Sports leagues/competitions
- `teams` - Teams/players
- `matches` - Games/matches
- `live_events` - Live match events

### 🎯 **Betting System**
- `bet_markets` - Types of bets (match winner, over/under, etc.)
- `bet_options` - Specific betting options with odds
- `bets` - User placed bets
- `bet_selections` - Individual selections within a bet
- `odds_history` - Historical odds tracking

### 🎁 **Promotions & Bonuses**
- `promotions` - Available promotions
- `user_promotions` - User claimed promotions

### 🔔 **Communication & Support**
- `notifications` - User notifications
- `support_tickets` - Customer support tickets
- `support_messages` - Support ticket messages

### 🛡️ **Security & Compliance**
- `audit_logs` - System audit trail
- `risk_management` - Risk management alerts
- `settings` - Application configuration

## Detailed Table Relationships

### **Users System Flow**
```
users (1) → (1) user_profiles
users (1) → (∞) kyc_documents
users (1) → (∞) user_sessions
users (1) → (∞) wallets
users (1) → (∞) transactions
users (1) → (∞) bets
users (1) → (∞) betting_limits
users (1) → (∞) user_promotions
users (1) → (∞) notifications
users (1) → (∞) support_tickets
users (1) → (∞) audit_logs
users (1) → (∞) risk_management
```

### **Sports & Betting Flow**
```
sports (1) → (∞) leagues
sports (1) → (∞) teams
sports (1) → (∞) matches

leagues (1) → (∞) matches
teams (1) → (∞) matches (as home_team)
teams (1) → (∞) matches (as away_team)

matches (1) → (∞) bet_options
matches (1) → (∞) live_events
bet_markets (1) → (∞) bet_options

bet_options (1) → (∞) bet_selections
bet_options (1) → (∞) odds_history

bets (1) → (∞) bet_selections
bets (1) → (∞) transactions
```

### **Financial Flow**
```
users (1) → (∞) wallets
wallets (1) → (∞) transactions
bets (1) → (∞) transactions
promotions (1) → (∞) user_promotions
user_promotions (1) → (∞) transactions
```

## Key Database Features

### **Multi-Currency Support**
- Users can have multiple wallets for different currencies
- All financial transactions support different currencies
- Settings table stores supported currencies

### **Flexible Betting System**
- Support for single, multiple, and system bets
- Live betting capabilities
- Cash-out functionality
- Historical odds tracking

### **Comprehensive User Management**
- Role-based access control (Admin, Agent, User)
- KYC verification workflow
- Two-factor authentication support
- Session management

### **Risk Management**
- Real-time risk assessment
- User betting limits
- Fraud detection capabilities
- Audit trail for all actions

### **Promotion System**
- Multiple promotion types (welcome, deposit, free bet, etc.)
- Wagering requirements tracking
- User-specific promotion targeting

### **Support System**
- Ticket-based support system
- Real-time messaging
- Categorized support requests

## Security Considerations

### **Data Protection**
- Soft deletes for user accounts
- Encrypted sensitive data storage
- Audit logging for all critical actions
- Session security with token management

### **Financial Security**
- Transaction integrity with before/after balance tracking
- Locked balance for pending bets
- Risk management alerts
- Betting limit enforcement

### **Fraud Prevention**
- IP tracking and geolocation
- Suspicious activity monitoring
- Account verification requirements
- Multiple security layers

## Indexing Strategy

### **Performance Indexes**
- User-based queries (`user_id` indexes on all user-related tables)
- Time-based queries (`created_at`, `start_time` indexes)
- Status-based queries (`status`, `is_active` indexes)
- Financial queries (`transaction_type`, `amount` indexes)
- Sports queries (`sport_id`, `league_id`, `match_id` indexes)

### **Unique Constraints**
- Unique bet IDs, transaction IDs
- Unique user email, phone
- Unique sport/league/team slugs
- Unique promotion codes

## Scalability Features

### **Horizontal Scaling Support**
- JSON columns for flexible data storage
- Proper foreign key relationships
- Optimized queries with proper indexing
- Separation of concerns across tables

### **Caching Strategy**
- Sports data caching
- Odds caching
- User session caching
- Settings caching

## Migration Order

The migrations are numbered sequentially and should be run in order:
1. Update users table
2. User profiles and KYC
3. Wallet system
4. Transaction system
5. Sports structure
6. Betting system
7. Promotions
8. Support system
9. Security and auditing
10. Settings and configuration

This database structure provides a solid foundation for a comprehensive online betting platform with room for future expansion and customization.
