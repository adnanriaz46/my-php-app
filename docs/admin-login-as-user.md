# Admin Login as User Functionality

This document describes the "Login as User" feature that allows administrators to impersonate other users for debugging and support purposes.

## Overview

The login as user functionality allows administrators to temporarily switch to another user's account to:
- Debug user-specific issues
- Provide customer support
- Test user experience from different account types
- Investigate reported problems

## Security Features

### Access Control
- Only users with `ADMIN` user type can use this feature
- Admins cannot impersonate other admin users
- Regular users cannot access this functionality

### Session Management
- The original admin user ID is stored in the session
- Impersonation can be stopped at any time
- Session is automatically cleared when stopping impersonation

## Backend Implementation

### Routes
```php
// Start impersonation (admin only)
POST /admin/users/{id}/login-as

// Stop impersonation (any authenticated user)
POST /stop-impersonation
```

### Controller Methods

#### `AdminController@loginAsUser($id)`
- Validates that the current user is an admin
- Prevents impersonation of other admin users
- Stores the original admin ID in session
- Logs in as the target user
- Returns success response with redirect URL

#### `AdminController@stopImpersonation()`
- Retrieves the original admin user from session
- Validates the admin user still exists and is an admin
- Clears the impersonation session
- Logs back in as the original admin
- Returns success response with redirect URL

## Frontend Implementation

### User Detail Dialog
- "Login as User" button appears next to the user type badge
- Only visible to admin users
- Shows success/error toast messages
- Redirects to user dashboard after successful impersonation

### User Menu
- "Stop Impersonation" option appears in the user menu when impersonating
- Allows admin to return to their original account
- Shows success/error toast messages
- Redirects to admin dashboard after stopping impersonation

### Visual Indicators
- Yellow banner appears at the top of the page during impersonation
- Clear indication that the admin is currently impersonating a user
- Consistent styling with the application's design system

## Usage

### Starting Impersonation
1. Navigate to Admin Dashboard > Users
2. Click on a user to view their details
3. Click "Login as User" button
4. Confirm the action
5. You will be redirected to the user's dashboard

### Stopping Impersonation
1. Click on the user menu (top right)
2. Select "Stop Impersonation"
3. You will be redirected back to the admin dashboard

## Technical Details

### Session Storage
- Backend: Uses Laravel session to store `impersonating_admin_id`
- Frontend: Uses server session data shared via Inertia props

### Security Considerations
- Impersonation sessions are tied to the browser session
- Admin credentials are not exposed during impersonation
- All actions performed while impersonating are logged under the target user
- Impersonation can be stopped immediately if needed

### Error Handling
- Unauthorized access attempts return 403 status
- Invalid user IDs return 404 status
- Missing session data returns 400 status
- All errors are displayed as user-friendly toast messages

## Testing

The functionality has been tested and verified to work correctly:

### ✅ **Working Features:**
- **Admin Login as User**: Admins can successfully impersonate regular users
- **Security Restrictions**: Admins cannot impersonate other admins
- **Access Control**: Only admin users can access the impersonation feature
- **Visual Indicators**: Yellow banner appears on all authenticated pages during impersonation
- **Menu Integration**: "Stop Impersonation" option appears in user menus during impersonation
- **Session Management**: Proper session storage and cleanup
- **Guest vs Authenticated Pages**: Banner and menu items work correctly on authenticated pages

### **Expected Behavior:**
- **Guest Pages**: No impersonation banner (users not logged in)
- **Authenticated Pages**: Banner and menu items show when impersonating
- **Admin Pages**: Full impersonation functionality available

## Future Enhancements

Potential improvements could include:
- Audit logging of impersonation sessions
- Time limits for impersonation sessions
- Notification to target user when impersonation starts
- More granular permissions for different admin roles 