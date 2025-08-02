# Eloquent Models - Online Betting Platform

## Model Relationships Overview

This document outlines all the Eloquent models and their relationships for the online betting platform.

## 📋 **Core Models Structure**

### **👤 User Management Models**

#### **User Model**
```php
// Relationships
hasOne: UserProfile
hasMany: KycDocument, Wallet, Transaction, Bet, BettingLimit, UserPromotion, Notification, SupportTicket, SupportMessage, UserSession, AuditLog

// Key Methods
isAdmin(), isAgent(), isActive(), getMainWallet()
```

#### **UserProfile Model**
```php
// Relationships
belongsTo: User

// Key Methods
getFullNameAttribute(), getFullAddressAttribute()
```

#### **KycDocument Model**
```php
// Relationships
belongsTo: User, User (reviewer)

// Key Methods
isPending(), isApproved(), isRejected(), isExpired()
```

### **💰 Financial Models**

#### **Wallet Model**
```php
// Relationships
belongsTo: User
hasMany: Transaction

// Key Methods
getTotalBalanceAttribute(), getAvailableBalanceAttribute(), canWithdraw(), canBet(), addBalance(), subtractBalance(), lockBalance(), unlockBalance()
```

#### **Transaction Model**
```php
// Relationships
belongsTo: User, Wallet, Bet

// Key Methods
isPending(), isCompleted(), isFailed(), isDeposit(), isWithdrawal(), isBetTransaction()
```

#### **BettingLimit Model**
```php
// Relationships
belongsTo: User

// Key Methods
isActive(), isExceeded(), getRemainingAmount(), getUsagePercentage(), canSpend(), addUsage(), resetUsage()
```

### **🏆 Sports & Games Models**

#### **Sport Model**
```php
// Relationships
hasMany: League, Team, Match

// Key Methods
isActive(), getActiveLeagues(), getActiveMatches()
```

#### **League Model**
```php
// Relationships
belongsTo: Sport
hasMany: Match

// Key Methods
isActive(), getUpcomingMatches(), getLiveMatches()
```

#### **Team Model**
```php
// Relationships
belongsTo: Sport
hasMany: Match (as home/away team)

// Key Methods
isActive(), getAllMatches(), getUpcomingMatches(), getRecentMatches(), getDisplayName()
```

#### **Match Model**
```php
// Relationships
belongsTo: Sport, League, Team (home), Team (away)
hasMany: BetOption, LiveEvent, BetSelection

// Key Methods
isScheduled(), isLive(), isFinished(), getMatchDisplayName(), getHomeScore(), getAwayScore(), getScoreDisplay(), getWinner(), canPlaceBets()
```

### **🎯 Betting System Models**

#### **BetMarket Model**
```php
// Relationships
hasMany: BetOption

// Key Methods
isActive(), getOptionsForMatch()
```

#### **BetOption Model**
```php
// Relationships
belongsTo: Match, BetMarket
hasMany: BetSelection, OddsHistory

// Key Methods
isActive(), isSuspended(), canBet(), updateOdds(), incrementBetCount(), getOddsMovement(), getFormattedOdds()
```

#### **Bet Model**
```php
// Relationships
belongsTo: User
hasMany: BetSelection, Transaction

// Key Methods
isPending(), isWon(), isLost(), isVoid(), canCashout(), isSingle(), isMultiple(), isSystem(), calculateTotalOdds(), calculatePotentialWin(), settle(), getSelectionCount()
```

#### **BetSelection Model**
```php
// Relationships
belongsTo: Bet, Match, BetOption

// Key Methods
isPending(), isWon(), isLost(), isVoid(), isSettled(), settle(), getMatchName(), getLeagueName(), getSportName()
```

### **🎁 Promotion Models**

#### **Promotion Model**
```php
// Relationships
hasMany: UserPromotion

// Key Methods
isActive(), isAvailable(), canUserClaim(), calculateBonusAmount(), incrementUsage(), getRemainingUsage()
```

#### **UserPromotion Model**
```php
// Relationships
belongsTo: User, Promotion

// Key Methods
isActive(), isCompleted(), isExpired(), getRemainingWagering(), getWageringProgress(), addWagering(), expire(), cancel()
```

### **🔔 Communication Models**

#### **Notification Model**
```php
// Relationships
belongsTo: User

// Key Methods
isRead(), markAsRead(), markAsUnread(), getIcon(), getColor()
```

#### **SupportTicket Model**
```php
// Relationships
belongsTo: User, User (assigned agent)
hasMany: SupportMessage

// Key Methods
isOpen(), isInProgress(), isResolved(), assignTo(), resolve(), close(), reopen(), getStatusColor(), getPriorityColor()
```

#### **SupportMessage Model**
```php
// Relationships
belongsTo: SupportTicket, User

// Key Methods
isStaffReply(), isUserMessage(), hasAttachments(), getAttachmentCount()
```

### **🛡️ Security & Audit Models**

#### **UserSession Model**
```php
// Relationships
belongsTo: User

// Key Methods
isActive(), isExpired(), expire(), updateActivity(), extend(), getDeviceIcon(), getLocationDisplay()
```

#### **AuditLog Model**
```php
// Relationships
belongsTo: User
morphTo: model

// Key Methods
getChangedAttributes(), hasChanges(), getActionIcon(), getActionColor(), getUserName(), getModelName()
```

### **📊 Analytics Models**

#### **OddsHistory Model**
```php
// Relationships
belongsTo: BetOption

// Key Methods
getMovement(), getFormattedOdds()
```

#### **LiveEvent Model**
```php
// Relationships
belongsTo: Match

// Key Methods
isGoal(), isCard(), isSubstitution(), getEventIcon(), getEventColor(), getMinuteDisplay(), getTeamDisplay()
```

#### **Setting Model**
```php
// Static Methods
get(), set(), getPublicSettings(), getByGroup()

// Instance Methods
getValue(), setValue(), isPublic()
```

## 🔗 **Key Relationship Patterns**

### **User-Centric Relationships**
- Every financial operation ties back to User
- User can have multiple wallets (multi-currency)
- All betting activities are tracked per user

### **Sports Data Hierarchy**
```
Sport → League → Match
Sport → Team → Match (as home/away)
Match → BetOption → BetSelection
```

### **Betting Flow**
```
User → Bet → BetSelection → BetOption → Match
Bet → Transaction (stake, winnings, refunds)
```

### **Financial Flow**
```
User → Wallet → Transaction
Bet → Transaction (bet placed, won, lost)
Promotion → UserPromotion → Transaction (bonus)
```

## 🎯 **Model Features**

### **Scopes Available**
- **Active/Inactive** filtering on most models
- **Date-based** filtering (recent, today, etc.)
- **Status-based** filtering (pending, completed, etc.)
- **User-specific** filtering
- **Type-based** filtering

### **Helper Methods**
- **Status checks** (isActive, isPending, etc.)
- **Calculations** (totals, percentages, remainders)
- **Formatting** (display names, formatted numbers)
- **Business logic** (canBet, canWithdraw, etc.)

### **Timestamps & Soft Deletes**
- All models use Laravel timestamps
- User model uses soft deletes
- Audit trail for sensitive operations

## 🚀 **Usage Examples**

### **Get User's Active Bets**
```php
$user->bets()->pending()->with('selections.match')->get();
```

### **Get Live Matches with Betting Options**
```php
Match::live()->with('betOptions.betMarket')->get();
```

### **Check User's Betting Limits**
```php
$user->bettingLimits()->active()->byCategory('bet')->first();
```

### **Get Match Statistics**
```php
$match->liveEvents()->goals()->count();
```

This comprehensive model structure provides a solid foundation for all betting platform operations with proper relationships, business logic, and data integrity.
