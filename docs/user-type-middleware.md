# User Type Middleware Documentation

This document explains how to use the user type middleware system to restrict routes based on user types.

## User Types

The system supports three user types defined in the `User` model:

- `ADMIN` (1) - Administrator users
- `FREE` (2) - Free tier users  
- `PREMIUM` (3) - Premium tier users

## Available Middleware

### 1. CheckUserType (Single Type)
Restricts access to users of a specific type.

**Alias:** `user.type`

**Usage:**
```php
// Only admin users can access
Route::middleware(['auth', 'user.type:admin'])->group(function () {
    // Admin routes
});

// Only premium users can access
Route::middleware(['auth', 'user.type:premium'])->group(function () {
    // Premium routes
});

// Only free users can access
Route::middleware(['auth', 'user.type:free'])->group(function () {
    // Free routes
});
```

### 2. CheckUserTypes (Multiple Types)
Restricts access to users of any of the specified types.

**Alias:** `user.types`

**Usage:**
```php
// Admin and premium users can access
Route::middleware(['auth', 'user.types:admin,premium'])->group(function () {
    // Admin and premium routes
});

// Free and premium users can access
Route::middleware(['auth', 'user.types:free,premium'])->group(function () {
    // Free and premium routes
});
```

## User Model Helper Methods

The `User` model includes helper methods for checking user types:

```php
$user = auth()->user();

// Check specific user types
$user->isAdmin();    // Returns true if user is admin
$user->isFree();     // Returns true if user is free
$user->isPremium();  // Returns true if user is premium

// Check multiple user types
$user->hasAnyType(['ADMIN', 'PREMIUM']); // Returns true if user is admin or premium

// Get user type name
$user->getUserTypeName(); // Returns 'Admin', 'Free', 'Premium', or 'Unknown'
```

## Error Handling

When a user doesn't have the required permissions:

- **API requests** return a 403 JSON response with message "Insufficient permissions."
- **Web requests** redirect to the dashboard with an error message

## Examples

### Admin Routes
```php
Route::middleware(['auth', 'user.type:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/admin/users', [AdminController::class, 'users']);
});
```

### Premium Features
```php
Route::middleware(['auth', 'user.type:premium'])->group(function () {
    Route::get('/email-marketing', [EmailMarketingController::class, 'index']);
    Route::get('/wholesale', [WholesaleController::class, 'index']);
});
```

### Mixed Access
```php
Route::middleware(['auth', 'user.types:admin,premium'])->group(function () {
    Route::get('/advanced-features', [FeatureController::class, 'index']);
});
```

### Controller-Level Checks
```php
public function someMethod()
{
    $user = auth()->user();
    
    if (!$user->isPremium()) {
        return redirect()->route('upgrade')->with('error', 'This feature requires a premium subscription.');
    }
    
    // Premium-only logic here
}
```

## Best Practices

1. **Always use middleware for route-level protection** - This ensures consistent access control
2. **Use controller-level checks for conditional logic** - For features that work differently based on user type
3. **Provide clear error messages** - Help users understand why they can't access certain features
4. **Consider upgrade paths** - Redirect users to upgrade pages when they try to access premium features

## Security Notes

- The middleware validates user types against the constants defined in the User model
- Invalid user types will throw an exception during route registration
- Always ensure the `auth` middleware is applied before user type middleware
- The middleware handles both authenticated and unauthenticated users appropriately 