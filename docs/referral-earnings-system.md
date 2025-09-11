# Referral Earnings System Documentation

## Overview

The referral earnings system is a comprehensive solution for managing affiliate marketing commissions and payouts. It includes automatic earnings generation, status management, approval workflows, and admin controls.

## System Architecture

### Core Components

1. **UserReferralEarning Model** - Database model for earnings records
2. **ReferralEarningService** - Business logic for earnings management
3. **ReferralController** - API endpoints for earnings operations
4. **Admin Pages** - Vue.js interfaces for earnings management
5. **User Referral Page** - User-facing earnings dashboard

### Database Schema

#### user_referral_earnings Table

```sql
- id (Primary Key)
- user_id (Foreign Key to users) - The referrer who earns the commission
- from_user_id (Foreign Key to users) - The affiliate who generated the earning
- amount (Decimal) - Commission amount
- description (String) - Description of the earning
- status (Enum) - pending, approved, rejected, paid, cancelled
- type (Enum) - subscription_upgrade, first_payment, recurring_payment, bonus
- reference_id (BigInt) - ID of the triggering event (subscription, payment, etc.)
- reference_type (String) - Type of reference (subscription, payment, etc.)
- paid_at (Timestamp) - When the earning was paid
- notes (Text) - Admin notes
- created_at, updated_at (Timestamps)
```

## Earnings Types & Commission Rates

| Type | Commission Rate | Description |
|------|----------------|-------------|
| `subscription_upgrade` | 10% | When affiliate upgrades to premium |
| `first_payment` | 15% | First payment from affiliate |
| `recurring_payment` | 5% | Recurring payments from affiliate |
| `bonus` | 100% | Fixed bonus amounts |

## Status Workflow

```
PENDING → APPROVED → PAID
    ↓
REJECTED/CANCELLED
```

### Status Descriptions

- **PENDING**: Newly created earnings awaiting admin review
- **APPROVED**: Admin has approved the earning for payment
- **PAID**: Payment has been processed
- **REJECTED**: Admin has rejected the earning
- **CANCELLED**: Earning has been cancelled

## Key Features

### 1. Automatic Earnings Generation

The system automatically generates earnings when:
- Affiliates upgrade their subscription
- Affiliates make their first payment
- Affiliates make recurring payments
- Bonuses are awarded

### 2. Admin Management

**Admin Referrals Overview Page** (`/admin/referrals/index`)
- View all referrers with statistics
- Filter by user type, search by name/email
- Sort by various criteria
- Summary cards with key metrics

**Admin W9s Management** (`/admin/referrals/w9s`)
- Review and approve/reject W9 documents
- Add remarks to W9 submissions
- Filter by status and user
- View uploaded documents

**Admin Earnings Management** (`/admin/referrals/earnings`)
- Review and approve/reject earnings
- Mark earnings as paid
- Bulk operations for multiple earnings
- Filter by status, type, and user
- Detailed earnings information

### 3. User Dashboard

**User Referral Page** (`/referrals`)
- View personal earnings summary
- See pending, approved, and paid amounts
- Payout threshold indicators
- Detailed earnings history
- W9 document management

### 4. Business Logic

**ReferralEarningService** provides:
- Automatic commission calculation
- Earnings generation methods
- Status management
- Payout threshold checking
- Bulk operations
- Summary statistics

## API Endpoints

### Admin Routes

```php
GET    /admin/referrals/index          - Referrals overview
GET    /admin/referrals/w9s            - W9s management
GET    /admin/referrals/earnings       - Earnings management
PATCH  /admin/referrals/w9s/approve    - Approve/reject W9
PATCH  /admin/referrals/earnings/approve - Approve/reject earning
PATCH  /admin/referrals/earnings/pay   - Mark earning as paid
PATCH  /admin/referrals/earnings/bulk-approve - Bulk approve earnings
PATCH  /admin/referrals/earnings/bulk-pay - Bulk pay earnings
```

### User Routes

```php
GET    /referrals                      - User referral dashboard
POST   /upload-w9                      - Upload W9 document
GET    /ref/{uuid}                     - Referral link handler
```

## Usage Examples

### Generating Earnings

```php
use App\Services\ReferralEarningService;

$earningService = new ReferralEarningService();

// Generate subscription upgrade earning
$earning = $earningService->generateSubscriptionUpgradeEarning(
    $affiliate,
    99.99, // subscription amount
    $subscriptionId
);

// Generate first payment earning
$earning = $earningService->generateFirstPaymentEarning(
    $affiliate,
    149.99, // payment amount
    $paymentId
);

// Generate bonus earning
$earning = $earningService->generateBonusEarning(
    $referrer,
    50.00, // bonus amount
    "Referral milestone bonus"
);
```

### Managing Earnings

```php
// Approve an earning
$earning->approve();

// Mark as paid
$earning->markAsPaid();

// Reject with notes
$earning->reject("Invalid referral source");

// Get user earnings summary
$summary = $earningService->getEarningsSummary($user);
```

## Testing Commands

### Setup Sample Data

```bash
# Create sample referral relationships
php artisan referrals:setup-sample --count=10

# Generate sample earnings
php artisan referrals:generate-sample-earnings --count=20
```

### Database Operations

```bash
# Run migrations
php artisan migrate

# Rollback if needed
php artisan migrate:rollback
```

## Configuration

### Commission Rates

Edit `ReferralEarningService::COMMISSION_RATES` to adjust commission percentages:

```php
const COMMISSION_RATES = [
    'subscription_upgrade' => 10, // 10%
    'first_payment' => 15,        // 15%
    'recurring_payment' => 5,     // 5%
    'bonus' => 100,               // 100% (fixed amounts)
];
```

### Payout Threshold

Edit `ReferralEarningService::MIN_PAYOUT_THRESHOLD` to set minimum payout amount:

```php
const MIN_PAYOUT_THRESHOLD = 50.00; // $50 minimum
```

## Integration Points

### Payment System Integration

To integrate with your payment system, call the earning service when payments are processed:

```php
// In your payment webhook or payment success handler
public function handlePaymentSuccess($payment)
{
    $affiliate = User::find($payment->user_id);
    
    if ($affiliate->affiliated_user) {
        $earningService = new ReferralEarningService();
        
        // Determine if this is first payment or recurring
        $isFirstPayment = !$affiliate->hasPreviousPayments();
        
        if ($isFirstPayment) {
            $earningService->generateFirstPaymentEarning($affiliate, $payment->amount, $payment->id);
        } else {
            $earningService->generateRecurringPaymentEarning($affiliate, $payment->amount, $payment->id);
        }
    }
}
```

### Subscription System Integration

```php
// In your subscription upgrade handler
public function handleSubscriptionUpgrade($subscription)
{
    $affiliate = User::find($subscription->user_id);
    
    if ($affiliate->affiliated_user) {
        $earningService = new ReferralEarningService();
        $earningService->generateSubscriptionUpgradeEarning(
            $affiliate, 
            $subscription->amount, 
            $subscription->id
        );
    }
}
```

## Security Considerations

1. **Admin Authentication**: All admin routes require admin user type
2. **Data Validation**: All inputs are validated before processing
3. **Audit Trail**: All earnings changes are logged
4. **Permission Checks**: Users can only view their own earnings
5. **CSRF Protection**: All forms include CSRF tokens

## Performance Optimizations

1. **Database Indexes**: Added indexes on frequently queried columns
2. **Eager Loading**: Relationships are loaded efficiently
3. **Pagination**: Large datasets are paginated
4. **Caching**: Consider caching earnings summaries for frequently accessed data

## Monitoring & Analytics

The system provides comprehensive analytics:

- Total earnings by status
- Commission rates by type
- Payout thresholds and eligibility
- User referral performance
- Admin approval workflows

## Future Enhancements

1. **Automated Payouts**: Integration with payment gateways
2. **Tiered Commissions**: Different rates based on performance
3. **Referral Trees**: Multi-level referral tracking
4. **Advanced Analytics**: Detailed reporting and insights
5. **Email Notifications**: Automated notifications for status changes
6. **API Endpoints**: RESTful API for external integrations

## Troubleshooting

### Common Issues

1. **No earnings generated**: Check if users have proper affiliate relationships
2. **Commission calculations wrong**: Verify commission rates in service
3. **Admin pages not loading**: Ensure admin user type and proper routes
4. **Database errors**: Run migrations and check foreign key constraints

### Debug Commands

```bash
# Check referral relationships
php artisan tinker --execute="echo 'Referrers: ' . App\Models\User::whereNotNull('affiliate_slug')->count();"

# Check earnings
php artisan tinker --execute="echo 'Earnings: ' . App\Models\UserReferralEarning::count();"

# Check specific user earnings
php artisan tinker --execute="echo 'User 1 earnings: ' . App\Models\UserReferralEarning::where('user_id', 1)->sum('amount');"
```

## Support

For technical support or questions about the referral earnings system, please refer to the codebase documentation or contact the development team. 