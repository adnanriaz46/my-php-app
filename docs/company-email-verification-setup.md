# Company Email Verification System

This document explains the company email verification functionality that has been added to the application.

## Overview

The company email verification system allows users to verify their company email addresses separately from their primary email addresses. This is useful for business users who want to ensure their company email is verified for professional communications.

## Database Changes

### New Fields Added to Users Table

- `company_email_verified_at` - Timestamp when company email was verified
- `company_email_verified` - Boolean flag indicating if company email is verified

## Features

### 1. Company Email Verification Methods

The User model now includes these methods:

```php
// Check if company email is verified
$user->hasVerifiedCompanyEmail();

// Mark company email as verified
$user->markCompanyEmailAsVerified();

// Send verification notification
$user->sendCompanyEmailVerificationNotification();

// Get verification URL
$user->getCompanyEmailVerificationUrl();
```

### 2. Routes

The following routes have been added:

- `GET /verify-company-email` - Show verification prompt
- `GET /verify-company-email/{id}/{hash}` - Verify company email
- `POST /company-email/verification-notification` - Resend verification email
- `POST /settings/profile/company-email-verification` - Send verification from profile

### 3. Controllers

#### CompanyEmailVerificationController

- `verify()` - Mark company email as verified
- `resend()` - Send new verification notification
- `show()` - Display verification prompt

#### ProfileController

- `sendCompanyEmailVerification()` - Send verification from profile settings

### 4. Notification

#### CompanyEmailVerificationNotification

Sends a professional email with:
- Verification link (expires in 60 minutes)
- Clear instructions
- Security information

## Usage

### For Users

1. **Set Company Email**: Users can set their company email in profile settings
2. **Send Verification**: Click "Send Verification Email" button
3. **Verify Email**: Click the link in the email to verify
4. **Status Check**: Verification status is displayed in profile settings

### For Developers

#### Check Verification Status

```php
if ($user->hasVerifiedCompanyEmail()) {
    // Company email is verified
}
```

#### Send Verification

```php
$user->sendCompanyEmailVerificationNotification();
```

#### Verify Email

```php
if ($user->markCompanyEmailAsVerified()) {
    // Email was successfully verified
}
```

## Frontend Integration

### Profile Settings

The Profile settings page now includes:

- Company email verification status display
- "Send Verification Email" button
- Success/error messages
- Visual indicators for verification status

### Props

The Profile component now accepts:

```typescript
interface Props {
  mustVerifyEmail: boolean;
  mustVerifyCompanyEmail: boolean; // New prop
  status?: string;
  error?: string;
  success?: string;
}
```

## Security Features

1. **Signed URLs**: Verification links are signed and expire after 60 minutes
2. **Hash Verification**: Email hash is verified to prevent tampering
3. **Rate Limiting**: Verification requests are rate-limited
4. **Throttling**: Prevents abuse of verification system

## Email Template

The verification email includes:

- Professional greeting with user's first name
- Clear explanation of verification purpose
- Secure verification button
- Expiration notice
- Security information

## Configuration

### Environment Variables

No additional environment variables are required. The system uses existing mail configuration.

### Mail Configuration

Uses the same mail configuration as the main application (SendGrid).

## Testing

### Manual Testing

1. Set a company email address in profile settings
2. Click "Send Verification Email"
3. Check email for verification link
4. Click verification link
5. Verify status is updated in profile

### Automated Testing

```php
// Test verification
$user = User::factory()->create(['company_email' => 'test@company.com']);
$user->sendCompanyEmailVerificationNotification();

// Test verification status
$this->assertFalse($user->hasVerifiedCompanyEmail());
$user->markCompanyEmailAsVerified();
$this->assertTrue($user->hasVerifiedCompanyEmail());
```

## Migration

Run the migration to add the new fields:

```bash
php artisan migrate
```

## Troubleshooting

### Common Issues

1. **Email not sending**: Check mail configuration and SendGrid settings
2. **Verification link expired**: Links expire after 60 minutes
3. **Invalid hash**: Ensure email hasn't been changed since link generation

### Debugging

- Check Laravel logs for email sending errors
- Verify mail configuration in `.env`
- Check SendGrid webhook events for delivery status

## Future Enhancements

Potential improvements:

1. **Bulk verification**: Verify multiple company emails at once
2. **Domain verification**: Verify entire company domains
3. **Advanced security**: Add additional verification methods
4. **Analytics**: Track verification rates and success metrics 