# Lead Source Tracking System

This document explains the lead source tracking system that tracks where users come from before they register.

## Overview

The lead source tracking system automatically tracks the pages users visit before they reach the registration/login pages. This helps understand which pages are most effective at converting visitors into registered users.

## How It Works

### 1. Page Visit Tracking

- **Middleware**: `TrackLeadSource` middleware runs on every request
- **Storage**: Lead source data is stored in session and cookies (not database)
- **Scope**: Only tracks unauthenticated users (not logged-in users)
- **Exclusions**: Skips tracking on auth pages (login, register, password reset, etc.)

### 2. Data Captured

For each page visit, the system captures:
- **Page Name**: Human-readable page name (e.g., "Home Page", "Property / Search")
- **Page URL**: Full URL with query parameters
- **User Agent**: Browser/device information
- **Timestamp**: When the page was visited

### 3. Page Name Conversion

The system automatically converts route paths to readable page names:
- `/` → "Home Page"
- `/property/search` → "Property / Search"
- `/learn/getting-started` → "Learn / Getting Started"
- `/property_overview` → "Property Overview"

### 4. Registration Integration

When a user registers:
- The system retrieves the stored lead source data from session/cookies
- Creates a `LeadSource` record linked to the new user
- Clears the session data after storing

## Database Schema

### LeadSources Table

```sql
CREATE TABLE lead_sources (
    id BIGINT PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
    page_name VARCHAR(255),
    page_url TEXT,
    user_agent TEXT,
    visited_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Usage Examples

### 1. User Journey Tracking

**Scenario**: User visits Home → Property Search → Learn → Register

**Result**: 
- Primary Lead Source: "Home Page" (first page visited)
- Latest Lead Source: "Learn / Getting Started" (last page before registration)

### 2. Auth-Required Redirects

**Scenario**: User visits `/property/search` (requires auth) → redirects to Login → User registers

**Result**: Tracks "Property / Search" as the lead source (captures the intended URL)

### 3. Direct Registration

**Scenario**: User goes directly to `/register`

**Result**: No lead source tracked (no prior page visits)

## API Methods

### User Model Methods

```php
// Get all lead sources for a user
$user->leadSources();

// Get the first page visited (primary lead source)
$user->getPrimaryLeadSource();

// Get the last page visited before registration
$user->getLatestLeadSource();
```

### LeadSource Model Methods

```php
// Get latest lead sources
LeadSource::latest()->get();

// Get lead sources in date range
LeadSource::inDateRange($startDate, $endDate)->get();
```

## Admin Interface

### Lead Sources Analytics Page

Access: `/admin/lead-sources`

Features:
- **Statistics**: Total lead sources, unique pages, users with lead data
- **Top Lead Sources**: Most common pages users visit before registration
- **Detailed View**: All lead sources with pagination and search
- **Filtering**: By date range, page name, or user email

## Testing

### Manual Testing

1. **Clear your session/cookies** (to simulate a new visitor)
2. **Visit any page** (e.g., `/`, `/property/search`)
3. **Go to registration page** (`/register`)
4. **Register a new account**
5. **Check lead source data** in the admin interface at `/admin/lead-sources`

## Configuration

### Middleware Registration

The `TrackLeadSource` middleware is automatically registered in `bootstrap/app.php`:

```php
$middleware->web(append: [
    // ... other middleware
    TrackLeadSource::class,
]);
```

### Auth Page Exclusions

The following pages are excluded from tracking:
- `/register`
- `/forgot-password`
- `/reset-password`
- `/verify-email`
- `/confirm-password`
- `/unsubscribe`
- `/campaign/unsubscribe`

**Note**: The `/login` page is handled specially to capture the intended URL when users are redirected from auth-required pages.

## Data Retention

- **Session Storage**: Lead source data is stored in session during browsing
- **Cookie Storage**: Also stored in cookies for persistence across sessions (30 days)
- **Database Storage**: Only stored when user registers
- **Cleanup**: Session/cookie data is cleared after registration

## Best Practices

1. **Privacy**: Only track necessary data (page name, URL, user agent)
2. **Performance**: Middleware is lightweight and doesn't impact page load
3. **Accuracy**: Tracks the most recent non-auth page visited
4. **Analytics**: Use admin interface to analyze conversion effectiveness

## Troubleshooting

### No Lead Sources Found

If a user has no lead sources:
- They may have registered directly without visiting other pages
- The middleware might not be working (check middleware registration)
- Session/cookie data might have been cleared

### Testing the System

1. Use the test commands to verify functionality
2. Check admin interface for lead source data
3. Verify middleware is running by checking session data
4. Test with different user journeys to ensure proper tracking 