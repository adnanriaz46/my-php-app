<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { NotificationTray, type Notification, type NotificationType } from './index'

// Sample notification types
const notificationTypes: NotificationType[] = [
  {
    id: 1,
    name: 'property',
    display_name: 'Property Updates',
    description: 'Notifications related to property listings, updates, and changes',
    icon: 'tabler:home',
    color: 'blue',
    is_active: true,
  },
  {
    id: 2,
    name: 'user-account',
    display_name: 'Account Updates',
    description: 'Notifications related to user account changes, settings, and profile updates',
    icon: 'tabler:user',
    color: 'green',
    is_active: true,
  },
  {
    id: 3,
    name: 'site-updates',
    display_name: 'Site Updates',
    description: 'System notifications, maintenance updates, and platform changes',
    icon: 'tabler:settings',
    color: 'purple',
    is_active: true,
  },
  {
    id: 4,
    name: 'buyers',
    display_name: 'Buyer Activity',
    description: 'Notifications related to buyer inquiries, offers, and interactions',
    icon: 'tabler:users',
    color: 'orange',
    is_active: true,
  },
  {
    id: 5,
    name: 'email-marketing',
    display_name: 'Email Marketing',
    description: 'Notifications related to email campaigns, delivery status, and analytics',
    icon: 'tabler:mail',
    color: 'indigo',
    is_active: true,
  },
  {
    id: 6,
    name: 'subscription',
    display_name: 'Subscription',
    description: 'Notifications related to subscription changes, payments, and billing',
    icon: 'tabler:credit-card',
    color: 'red',
    is_active: true,
  },
]

// Sample notifications with the new structure
const notifications = ref<Notification[]>([
  {
    id: '1',
    user_id: 1,
    notification_type_id: 1,
    title: 'New Property Listed',
    message: 'A new property matching your search criteria has been listed in your area.',
    severity: 'info',
    read: false,
    action_label: 'View Property',
    action_url: '/property/123',
    created_at: new Date(Date.now() - 5 * 60 * 1000), // 5 minutes ago
    updated_at: new Date(Date.now() - 5 * 60 * 1000),
    notification_type: notificationTypes[0], // property
  },
  {
    id: '2',
    user_id: 1,
    notification_type_id: 5,
    title: 'Email Campaign Sent',
    message: 'Your email campaign "Summer Properties" has been successfully sent to 1,234 contacts.',
    severity: 'success',
    read: false,
    created_at: new Date(Date.now() - 30 * 60 * 1000), // 30 minutes ago
    updated_at: new Date(Date.now() - 30 * 60 * 1000),
    notification_type: notificationTypes[4], // email-marketing
  },
  {
    id: '3',
    user_id: 1,
    notification_type_id: 6,
    title: 'Payment Failed',
    message: 'Your monthly subscription payment failed. Please update your payment method.',
    severity: 'error',
    read: true,
    read_at: new Date(Date.now() - 60 * 60 * 1000), // 1 hour ago
    action_label: 'Update Payment',
    action_url: '/settings/payment',
    created_at: new Date(Date.now() - 2 * 60 * 60 * 1000), // 2 hours ago
    updated_at: new Date(Date.now() - 60 * 60 * 1000),
    notification_type: notificationTypes[5], // subscription
  },
  {
    id: '4',
    user_id: 1,
    notification_type_id: 3,
    title: 'System Maintenance',
    message: 'Scheduled maintenance will occur tonight at 2 AM EST. Service may be temporarily unavailable.',
    severity: 'warning',
    read: false,
    created_at: new Date(Date.now() - 4 * 60 * 60 * 1000), // 4 hours ago
    updated_at: new Date(Date.now() - 4 * 60 * 60 * 1000),
    notification_type: notificationTypes[2], // site-updates
  },
  {
    id: '5',
    user_id: 1,
    notification_type_id: 4,
    title: 'New Buyer Inquiry',
    message: 'You have received a new inquiry from a potential buyer for your property.',
    severity: 'info',
    read: false,
    action_label: 'View Inquiry',
    action_url: '/buyers/inquiry/456',
    created_at: new Date(Date.now() - 6 * 60 * 60 * 1000), // 6 hours ago
    updated_at: new Date(Date.now() - 6 * 60 * 60 * 1000),
    notification_type: notificationTypes[3], // buyers
  },
])

const handleMarkAsRead = (notificationId: string) => {
  const notification = notifications.value.find(n => n.id === notificationId)
  if (notification) {
    notification.read = true
    notification.read_at = new Date()
  }
}

const handleMarkAllRead = () => {
  notifications.value.forEach(n => {
    if (!n.read) {
      n.read = true
      n.read_at = new Date()
    }
  })
}

const handleDeleteNotification = (notificationId: string) => {
  notifications.value = notifications.value.filter(n => n.id !== notificationId)
}

const handleClearAll = () => {
  notifications.value = []
}

const handleNotificationClick = (notification: Notification) => {
  console.log('Notification clicked:', notification)
  
  // If it's a property notification, you might want to navigate to the property
  if (notification.notification_type?.name === 'property' && notification.action_url) {
    console.log('Navigating to property:', notification.action_url)
  }
}

// Add a new test notification
const addTestNotification = () => {
  const newNotification: Notification = {
    id: (notifications.value.length + 1).toString(),
    user_id: 1,
    notification_type_id: 2,
    title: 'Test Notification',
    message: 'This is a test notification created at ' + new Date().toLocaleTimeString(),
    severity: 'info',
    read: false,
    created_at: new Date(),
    updated_at: new Date(),
    notification_type: notificationTypes[1], // user-account
  }
  
  notifications.value.unshift(newNotification)
}
</script>

<template>
  <div class="p-8">
    <h2 class="text-2xl font-bold mb-4">Notification Tray Example</h2>
    
    <div class="flex items-center gap-4 mb-6">
      <NotificationTray
        :notifications="notifications"
        :max-notifications="5"
        :show-badge="true"
        @mark-as-read="handleMarkAsRead"
        @mark-all-read="handleMarkAllRead"
        @delete-notification="handleDeleteNotification"
        @clear-all="handleClearAll"
        @notification-click="handleNotificationClick"
      />
      
      <div class="text-sm text-muted-foreground">
        Click the bell icon to see notifications
      </div>

      <button
        @click="addTestNotification"
        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
      >
        Add Test Notification
      </button>
    </div>
    
    <div class="mt-8">
      <h3 class="text-lg font-semibold mb-2">Current Notifications ({{ notifications.length }})</h3>
      <div class="space-y-2">
        <div
          v-for="notification in notifications"
          :key="notification.id"
          class="p-3 border rounded-lg"
          :class="{ 'bg-gray-50': notification.read }"
        >
          <div class="flex items-center justify-between">
            <div>
              <div class="font-medium">{{ notification.title }}</div>
              <div class="text-sm text-muted-foreground">{{ notification.message }}</div>
              <div class="text-xs text-muted-foreground mt-1">
                {{ notification.created_at.toLocaleString() }}
                <span class="ml-2" :class="notification.read ? 'text-green-600' : 'text-orange-600'">
                  {{ notification.read ? 'Read' : 'Unread' }}
                </span>
                <span class="ml-2 text-blue-600">
                  {{ notification.notification_type?.display_name }}
                </span>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span
                class="px-2 py-1 text-xs rounded-full"
                :class="{
                  'bg-blue-100 text-blue-800': notification.severity === 'info',
                  'bg-green-100 text-green-800': notification.severity === 'success',
                  'bg-red-100 text-red-800': notification.severity === 'error',
                  'bg-yellow-100 text-yellow-800': notification.severity === 'warning'
                }"
              >
                {{ notification.severity }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-8">
      <h3 class="text-lg font-semibold mb-2">Notification Types</h3>
      <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div
          v-for="type in notificationTypes"
          :key="type.id"
          class="p-3 border rounded-lg"
        >
          <div class="flex items-center gap-2 mb-2">
            <div class="w-6 h-6 rounded-full border flex items-center justify-center"
                 :class="{
                   'text-blue-600 bg-blue-50 border-blue-200': type.color === 'blue',
                   'text-green-600 bg-green-50 border-green-200': type.color === 'green',
                   'text-purple-600 bg-purple-50 border-purple-200': type.color === 'purple',
                   'text-orange-600 bg-orange-50 border-orange-200': type.color === 'orange',
                   'text-indigo-600 bg-indigo-50 border-indigo-200': type.color === 'indigo',
                   'text-red-600 bg-red-50 border-red-200': type.color === 'red',
                 }">
              <span class="text-xs">{{ type.name.charAt(0).toUpperCase() }}</span>
            </div>
            <span class="font-medium">{{ type.display_name }}</span>
          </div>
          <p class="text-sm text-muted-foreground">{{ type.description }}</p>
        </div>
      </div>
    </div>
  </div>
</template> 