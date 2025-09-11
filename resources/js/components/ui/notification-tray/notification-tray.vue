<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Bell, Check, X } from 'lucide-vue-next'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger, DropdownMenuSeparator } from '@/components/ui/dropdown-menu'
import { Icon } from '@iconify/vue'
import axios from 'axios'

export interface NotificationType {
  id: number
  name: string
  display_name: string
  description?: string
  icon?: string
  color?: string
  is_active: boolean
}

export interface Notification {
  id: string
  user_id: number
  notification_type_id: number
  title: string
  message: string
  severity: 'info' | 'success' | 'warning' | 'error'
  read: boolean
  read_at?: Date
  action_label?: string
  action_url?: string
  action_data?: any
  related_entity_type?: string
  related_entity_id?: number
  metadata?: any
  scheduled_at?: Date
  sent_at?: Date
  created_at: Date
  updated_at: Date
  notification_type?: NotificationType
}

interface Props {
  maxNotifications?: number
  showBadge?: boolean
  class?: string
}

const props = withDefaults(defineProps<Props>(), {
  maxNotifications: 10,
  showBadge: true,
})

const notifications = ref<Notification[]>([])

const isOpen = ref(false)
const unreadCount = computed(() =>
  notifications.value.filter(n => !n.read).length
)

const displayNotifications = computed(() =>
  notifications.value.slice(0, props.maxNotifications)
)

const loadMoreNotifications = () => {
  notifications.value = notifications.value.slice(0, displayNotifications.value.length + 10)
}

onMounted(() => {
  refreshNotifications()
})

const getNotificationIcon = (notification: Notification) => {
  // First try to use the notification type icon
  if (notification.notification_type?.icon) {
    return notification.notification_type.icon
  }

  // Fallback to severity-based icons
  switch (notification.severity) {
    case 'success':
      return 'tabler:check'
    case 'warning':
      return 'tabler:alert-triangle'
    case 'error':
      return 'tabler:alert-circle'
    default:
      return 'tabler:info-circle'
  }
}

const getNotificationColor = (notification: Notification) => {
  // First try to use the notification type color
  // if (notification.notification_type?.color) {
  //   const colorMap: Record<string, string> = {
  //     'blue': 'text-blue-600 border-blue-600',
  //     'green': 'text-green-600 border-green-600',
  //     'purple': 'text-purple-600 border-purple-600',
  //     'orange': 'text-orange-600 border-orange-600',
  //     'indigo': 'text-indigo-600 border-indigo-600',
  //     'red': 'text-red-600 border-red-600',
  //   }
  //   return colorMap[notification.notification_type.color] || 'text-gray-600 border-gray-600'
  // }

  // Fallback to severity-based colors
  switch (notification.severity) {
    case 'success':
      return 'text-green-600 border-green-600'
    case 'warning':
      return 'text-yellow-600 border-yellow-600'
    case 'error':
      return 'text-red-600 border-red-600'
    default:
      return 'text-blue-600 border-blue-600'
  }
}

const formatTimestamp = (timestamp: Date) => {
  const now = new Date()
  const diff = now.getTime() - new Date(timestamp).getTime()
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)

  if (minutes < 1) return 'Just now'
  if (minutes < 60) return `${minutes}m ago`
  if (hours < 24) return `${hours}h ago`
  if (days < 7) return `${days}d ago`
  return new Date(timestamp).toLocaleDateString()
}

const handleNotificationClick = (notification: Notification) => {
  if (!notification.read) {
    handleMarkAsRead(notification.id.toString())
  }
  refreshNotifications()

  // Handle action if present
  if (notification.action_label) {
    if (notification.action_url) {
      window.open(notification.action_url, '_blank')
    } else if (notification.action_data?.onClick) {
      notification.action_data.onClick()
    }
  }
}

const handleMarkAsRead = (notificationId: string) => {
  axios.patch(route('notifications.mark-as-read', notificationId)).then((response) => {
    refreshNotifications()
  })
}

const handleDeleteNotification = (notificationId: string) => {
  axios.delete(route('notifications.destroy', notificationId)).then((response) => {
    refreshNotifications()
  })
}

const handleMarkAllRead = () => {
  axios.patch(route('notifications.mark-all-read')).then((response) => {
    refreshNotifications()
  })
}

const handleClearAll = () => {
  axios.delete(route('notifications.clear-all')).then((response) => {
    refreshNotifications()
  })
}

const refreshNotifications = () => {
  axios.get(route('notifications.index')).then((response) => {
    notifications.value = response.data?.notifications || []
  })
}

// Close dropdown when clicking outside
const handleClickOutside = (event: Event) => {
  const target = event.target as Element
  if (!target.closest('[data-notification-tray]')) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div data-notification-tray class="relative">
    <DropdownMenu v-model:open="isOpen">
      <DropdownMenuTrigger as-child>
        <Button variant="ghost" size="icon" class="relative" :class="props.class">
          <Icon icon="tabler:bell" class="size-6" />
          <Badge v-if="showBadge && unreadCount > 0" variant="destructive"
            class="absolute -top-1 -right-1 h-5 w-5 rounded-full p-0 text-xs flex items-center justify-center">
            {{ unreadCount > 99 ? '99+' : unreadCount }}
          </Badge>
        </Button>
      </DropdownMenuTrigger>

      <DropdownMenuContent align="end" class="w-80 overflow-y-hidden rounded-lg">
        <div class="flex items-center justify-between p-4 border-b">
          <h3 class="font-semibold text-sm">Notifications</h3>
          <div class="flex items-center gap-2">
            <Button v-if="unreadCount > 0" variant="ghost" size="sm" class="h-6 px-2 text-xs"
              @click="handleMarkAllRead">
              Mark all read
            </Button>
            <Button v-if="displayNotifications.length > 0" variant="ghost" size="sm"
              class="h-6 px-2 text-xs text-destructive hover:text-destructive" @click="handleClearAll">
              Clear all
            </Button>
          </div>
        </div>

        <div v-if="displayNotifications.length === 0" class="p-8 text-center text-muted-foreground">
          <Bell class="h-8 w-8 mx-auto mb-2 opacity-50" />
          <p class="text-sm">No notifications</p>
        </div>

        <div v-else class="divide-y max-h-96 overflow-y-auto">
          <div v-for="notification in displayNotifications" :key="notification.id"
            class="p-4 hover:bg-accent/50 transition-colors cursor-pointer group"
            :class="{ 'bg-primary/20': !notification.read }" @click="handleNotificationClick(notification)">
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0 w-6 h-6 rounded-full border flex items-center justify-center"
                :class="getNotificationColor(notification)">
                <Icon :icon="getNotificationIcon(notification)" class="size-8" />
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                  <h4 class="text-sm font-medium leading-tight flex flex-col gap-0.5">
                    <div class="flex items-center gap-1">
                      {{ notification.title }}
                      <Icon icon="tabler:circle-filled" class="size-3" :class="getNotificationColor(notification)" />
                    </div>
                    <span class="text-xs text-muted-foreground">({{
                      notification.notification_type?.display_name }})</span>
                  </h4>
                  <div class="flex items-center gap-1">
                    <Button v-if="!notification.read" variant="ghost" size="icon" title="Mark as read"
                      class="h-6 w-6 opacity-0 group-hover:opacity-100 transition-opacity"
                      @click.stop="handleMarkAsRead(notification.id.toString())">
                      <Check class="h-3 w-3" />
                    </Button>
                    <Button variant="ghost" size="icon" title="Delete"
                      class="h-6 w-6 opacity-0 group-hover:opacity-100 transition-opacity text-destructive"
                      @click.stop="handleDeleteNotification(notification.id.toString())">
                      <X class="h-3 w-3" />
                    </Button>
                  </div>
                </div>

                <p class="text-xs text-muted-foreground mt-1 line-clamp-2 leading-relaxed">
                  {{ notification.message }}
                </p>

                <div class="flex items-center justify-between mt-2">
                  <span class="text-xs text-muted-foreground">
                    {{ formatTimestamp(notification.created_at) }}
                  </span>

                  <Button v-if="notification.action_label" variant="link" size="sm" class="h-auto p-0 text-xs"
                    @click.stop="handleNotificationClick(notification)">
                    {{ notification.action_label }}
                  </Button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <DropdownMenuSeparator v-if="displayNotifications.length > 0" />

        <div v-if="displayNotifications.length > 0 && displayNotifications.length < notifications.length" class="p-2">
          <Button variant="ghost" size="sm" class="w-full text-xs" @click="loadMoreNotifications">
            View all notifications
          </Button>
        </div>
      </DropdownMenuContent>
    </DropdownMenu>
  </div>
</template>