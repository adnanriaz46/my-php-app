# Email Verification System

This document explains how the email verification system works in the application.

## Overview

The application uses a custom email verification system that integrates with SendGrid templates instead of Laravel's default email verification.

## Components

### 1. Direct Email Verification (Simple Approach)

**File**: `app/Http/Controllers/Auth/RegisteredUserController.php`

The registration controller:
- Sends email verification directly during registration
- Uses `EmailService` to send emails via SendGrid
- Uses the configured template ID from `config/user_email_config.php`

**File**: `app/Http/Controllers/Auth/EmailVerificationNotificationController.php`

The resend controller:
- Sends email verification when user requests resend
- Uses `EmailService` to send emails via SendGrid
- Uses the same template and data structure

### 2. User Model Integration

**File**: `app/Models/User.php`

The User model:
- Implements `MustVerifyEmail` interface
- Has custom `getEmailVerificationUrl()` method
- Simple and direct approach (no complex notifications)

### 3. Email Service Integration

**File**: `app/Services/EmailService.php`

The EmailService includes:
- `sendCustom()` method used directly for email verification
- `sendCompanyEmailVerification()` method for company email verification

## Configuration

### Email Template Configuration

**File**: `config/user_email_config.php`

```php
'account' => [
    'email_verify' => [
        'root' => true,
        'name' => 'Email verification',
        'description' => 'Email verification',
        'template_id' => 'd-d61a333dfe4446629bce17fc5dc9bedb',
    ],
    // ...
],
```

### SendGrid Template Data

The email verification template expects these dynamic data fields:

```php
[
    'user_name' => 'User\'s first name or full name',
    'verification_url' => 'Signed verification URL',
    'email' => 'User\'s email address',
    'expires_in' => '60 minutes'
]
```

## How It Works

1. **User Registration**: When a user registers, the controller directly calls `sendEmailVerificationEmail()`
2. **Direct Email Sending**: The controller uses `EmailService` to send emails via SendGrid
3. **SendGrid Integration**: Direct integration with SendGrid using `sendCustom()` method
4. **Template Rendering**: SendGrid renders the email using the configured template ID
5. **Verification**: User clicks the link and Laravel handles the verification process
6. **Resend**: When user requests resend, the same direct approach is used

## Routes

The standard Laravel email verification routes are used:

- `GET /verify-email` - Show verification prompt
- `GET /verify-email/{id}/{hash}` - Verify email
- `POST /email/verification-notification` - Resend verification email

## Testing

### Manual Testing

1. Register a new user account
2. Check email for verification link
3. Click verification link
4. Verify account is marked as verified

### Template Testing

To test the SendGrid template:

1. Update the template ID in `config/user_email_config.php`
2. Send a test verification email
3. Check the email rendering in SendGrid

## Customization

### Changing the Template

To use a different SendGrid template:

1. Create a new template in SendGrid
2. Update the `template_id` in `config/user_email_config.php`
3. Ensure the template accepts the required dynamic data fields

### Modifying Email Content

To modify the email content:

1. Update the SendGrid template directly
2. Or modify the data structure in `EmailService::sendEmailVerification()`

### Adding Custom Fields

To add custom fields to the email:

1. Update the data array in `EmailService::sendEmailVerification()`
2. Add corresponding fields to your SendGrid template

## Troubleshooting

### Common Issues

1. **Email not sending**: Check SendGrid API key and configuration
2. **Template not rendering**: Verify template ID and dynamic data structure
3. **Verification link broken**: Check route configuration and signing

### Debugging

- Check Laravel logs for email sending errors
- Verify SendGrid webhook events for delivery status
- Test template rendering in SendGrid dashboard

## Security Features

1. **Signed URLs**: Verification links are signed and expire after 60 minutes
2. **Hash Verification**: Email hash is verified to prevent tampering
3. **Rate Limiting**: Verification requests are rate-limited
4. **Throttling**: Prevents abuse of verification system 