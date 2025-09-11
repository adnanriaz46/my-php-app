# Guest Header Bar Component

A responsive header bar component designed for guest pages with hover menus and user authentication support.

## Features

- **Responsive Design**: Works on desktop and mobile devices
- **Hover Menus**: Dropdown menus that appear on hover for desktop
- **User Authentication**: Shows login/signup buttons for guests, user dropdown for logged-in users
- **Mobile Menu**: Slide-out menu for mobile devices
- **Search Integration**: Built-in search icon
- **Brand Integration**: Uses the Revamp365.ai logo and branding

## Components

### GuestHeaderBar.vue
The main header bar component that includes:
- Logo and branding
- Desktop navigation with hover menus
- User authentication buttons/dropdown
- Mobile menu trigger

### MobileMenu.vue
A slide-out mobile menu that includes:
- Full navigation menu
- User authentication options
- Search functionality
- User profile information (when logged in)

## Usage

### Basic Usage

```vue
<script setup lang="ts">
import GuestHeaderBar from '@/components/ui/guest-header-bar/GuestHeaderBar.vue';
</script>

<template>
    <div class="min-h-screen">
        <GuestHeaderBar />
        <!-- Your page content here -->
    </div>
</template>
```

### Navigation Items

The component includes predefined navigation items:

- **Product**: Product overview, market coverage, deal finder, property AI, etc.
- **Solutions**: Solutions for investors, agents, and companies
- **Company**: About us, careers, contact
- **Resources**: Blog, help center, API documentation
- **Pricing**: Direct link to pricing page

### User Authentication States

#### Guest Users
- Shows "Login" and "Sign Up" buttons
- Login button links to `/login`
- Sign Up button links to `/register`

#### Logged-in Users
- Shows user avatar with dropdown menu
- Dropdown includes:
  - User information
  - Upgrade account option
  - Learn section
  - Settings
  - Admin dashboard (if admin)
  - Appearance settings
  - Logout option

## Styling

The component uses Tailwind CSS classes and includes:
- Sticky positioning with backdrop blur
- Smooth hover transitions
- Responsive breakpoints
- Dark mode support (via CSS variables)

## Customization

### Modifying Navigation Items

You can customize the navigation items by editing the `mainNavItems` array in both `GuestHeaderBar.vue` and `MobileMenu.vue`:

```typescript
const mainNavItems: NavItem[] = [
    {
        title: 'Custom Menu',
        items: [
            {
                title: 'Custom Item',
                description: 'Description here',
                href: '/custom-link'
            }
        ]
    }
];
```

### Styling Customization

The component uses CSS custom properties for theming. You can override these in your CSS:

```css
:root {
    --header-bg: rgba(255, 255, 255, 0.95);
    --header-border: #e5e7eb;
    --primary-color: #f97316; /* Orange */
}
```

## Dependencies

- Vue 3 with Composition API
- Inertia.js for routing
- Lucide Vue Next for icons
- Tailwind CSS for styling
- Existing UI components (Button, DropdownMenu, Sheet, etc.)

## Browser Support

- Modern browsers with CSS backdrop-filter support
- Graceful degradation for older browsers
- Mobile-responsive design 