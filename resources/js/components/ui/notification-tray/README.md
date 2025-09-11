# Notification Tray Component

A comprehensive notification tray component for Vue 3 with TypeScript support. This component provides a dropdown-style notification interface with support for different notification types, read/unread states, and various actions.

## Features

- 🔔 Bell icon with unread count badge
- 📱 Responsive dropdown design
- 🎨 Multiple notification types (info, success, warning, error)
- ✅ Mark as read functionality
- 🗑️ Delete individual notifications
- 📋 Mark all as read
- 🧹 Clear all notifications
- ⏰ Relative timestamp formatting
- 🔗 Action buttons for notifications
- 🎯 Click outside to close
- 📦 TypeScript support

## Installation

The component is already included in your project. Import it where needed:

```typescript
import { NotificationTray, type Notification } from '@/components/ui/notification-tray'
```

## Basic Usage

```vue
<template>
  <NotificationTray
    :notifications="notifications"
    @mark-as-read="handleMarkAsRead"
    @notification-click="handleNotificationClick"
  />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { NotificationTray, type Notification } from '@/components/ui/notification-tray'

const notifications = ref<Notification[]>([
  {
    id: '1',
    title: 'New Property Listed',
    message: 'A new property matching your criteria has been listed.',
    type: 'info',
    timestamp: new Date(),
    read: false
  }
])

const handleMarkAsRead = (notificationId: string) => {
  // Handle marking notification as read
}

const handleNotificationClick = (notification: Notification) => {
  // Handle notification click
}
</script>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `notifications` | `Notification[]` | `[]` | Array of notifications to display |
| `maxNotifications` | `number` | `10` | Maximum number of notifications to show |
| `showBadge` | `boolean` | `true` | Whether to show the unread count badge |
| `class` | `string` | `undefined` | Additional CSS classes |

## Events

| Event | Payload | Description |
|-------|---------|-------------|
| `mark-as-read` | `notificationId: string` | Emitted when a notification is marked as read |
| `mark-all-read` | - | Emitted when all notifications are marked as read |
| `delete-notification` | `notificationId: string` | Emitted when a notification is deleted |
| `clear-all` | - | Emitted when all notifications are cleared |
| `notification-click` | `notification: Notification` | Emitted when a notification is clicked |

## Notification Interface

```typescript
interface Notification {
  id: string
  title: string
  message: string
  type: 'info' | 'success' | 'warning' | 'error'
  timestamp: Date
  read: boolean
  action?: {
    label: string
    url?: string
    onClick?: () => void
  }
}
```

## Notification Types

- **info**: Blue styling with info icon
- **success**: Green styling with check icon
- **warning**: Yellow styling with warning icon
- **error**: Red styling with error icon

## Action Buttons

Notifications can include action buttons that can either:
- Execute a callback function via `onClick`
- Navigate to a URL via `url` (opens in new tab)

## Styling

The component uses Tailwind CSS classes and follows the design system of your application. It's fully responsive and includes hover states, transitions, and proper accessibility features.

## Example with All Features

```vue
<template>
  <NotificationTray
    :notifications="notifications"
    :max-notifications="5"
    :show-badge="true"
    class="custom-notification-tray"
    @mark-as-read="handleMarkAsRead"
    @mark-all-read="handleMarkAllRead"
    @delete-notification="handleDeleteNotification"
    @clear-all="handleClearAll"
    @notification-click="handleNotificationClick"
  />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { NotificationTray, type Notification } from '@/components/ui/notification-tray'

const notifications = ref<Notification[]>([
  {
    id: '1',
    title: 'Property Update',
    message: 'A new property matching your search criteria has been listed.',
    type: 'info',
    timestamp: new Date(Date.now() - 5 * 60 * 1000),
    read: false,
    action: {
      label: 'View Property',
      onClick: () => console.log('View property clicked')
    }
  },
  {
    id: '2',
    title: 'Payment Failed',
    message: 'Your monthly subscription payment failed. Please update your payment method.',
    type: 'error',
    timestamp: new Date(Date.now() - 2 * 60 * 60 * 1000),
    read: true,
    action: {
      label: 'Update Payment',
      url: '/settings/payment'
    }
  }
])

const handleMarkAsRead = (notificationId: string) => {
  const notification = notifications.value.find(n => n.id === notificationId)
  if (notification) {
    notification.read = true
  }
}

const handleMarkAllRead = () => {
  notifications.value.forEach(n => n.read = true)
}

const handleDeleteNotification = (notificationId: string) => {
  notifications.value = notifications.value.filter(n => n.id !== notificationId)
}

const handleClearAll = () => {
  notifications.value = []
}

const handleNotificationClick = (notification: Notification) => {
  console.log('Notification clicked:', notification)
}
</script>
```

## Accessibility

The component includes proper ARIA attributes and keyboard navigation support. The dropdown can be opened with the Enter or Space key when the trigger button is focused.

## Browser Support

- Chrome 88+
- Firefox 85+
- Safari 14+
- Edge 88+ 