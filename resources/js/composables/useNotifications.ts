import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import type { Notification } from '@/components/ui/notification-tray'

// interface NotificationResponse {
//   notifications: Notification[]
//   unread_count: number
// }

// interface NotificationTypesResponse {
//   types: NotificationType[]
// }

export function useNotifications() {
  const notifications = ref<Notification[]>([])
  const unreadCount = ref(0)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const hasUnreadNotifications = computed(() => unreadCount.value > 0)

  /**
   * Fetch notifications from the API
   */
  const fetchNotifications = async (limit = 10, includeRead = true) => {
    loading.value = true
    error.value = null

    try {
      await router.get('/notifications', {
        limit,
        include_read: includeRead
      }, {
        preserveState: true,
        preserveScroll: true,
        only: ['notifications']
      })

      // The response will be handled by the page props
      // We'll need to get the data from the page props instead
    } catch (err) {
      error.value = 'Failed to fetch notifications'
      console.error('Error fetching notifications:', err)
    } finally {
      loading.value = false
    }
  }

  /**
   * Fetch unread count
   */
  const fetchUnreadCount = async () => {
    try {
      await router.get('/notifications/unread-count', {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['count']
      })

      // The response will be handled by the page props
    } catch (err) {
      console.error('Error fetching unread count:', err)
    }
  }

  /**
   * Mark a notification as read
   */
  const markAsRead = async (notificationId: string) => {
    try {
      await router.patch(`/notifications/${notificationId}/read`, {}, {
        preserveState: true,
        preserveScroll: true
      })

      // Update local state
      const notification = notifications.value.find(n => n.id.toString() === notificationId)
      if (notification && !notification.read) {
        notification.read = true
        notification.read_at = new Date()
        unreadCount.value = Math.max(0, unreadCount.value - 1)
      }
    } catch (err) {
      console.error('Error marking notification as read:', err)
    }
  }

  /**
   * Mark all notifications as read
   */
  const markAllAsRead = async () => {
    try {
      await router.patch('/notifications/mark-all-read', {}, {
        preserveState: true,
        preserveScroll: true
      })

      // Update local state
      notifications.value.forEach(notification => {
        if (!notification.read) {
          notification.read = true
          notification.read_at = new Date()
        }
      })
      unreadCount.value = 0
    } catch (err) {
      console.error('Error marking all notifications as read:', err)
    }
  }

  /**
   * Delete a notification
   */
  const deleteNotification = async (notificationId: string) => {
    try {
      await router.delete(`/notifications/${notificationId}`, {
        preserveState: true,
        preserveScroll: true
      })

      // Update local state
      const index = notifications.value.findIndex(n => n.id.toString() === notificationId)
      if (index !== -1) {
        const notification = notifications.value[index]
        if (!notification.read) {
          unreadCount.value = Math.max(0, unreadCount.value - 1)
        }
        notifications.value.splice(index, 1)
      }
    } catch (err) {
      console.error('Error deleting notification:', err)
    }
  }

  /**
   * Clear all notifications
   */
  const clearAll = async () => {
    try {
      await router.delete('/notifications/clear-all', {
        preserveState: true,
        preserveScroll: true
      })

      // Update local state
      notifications.value = []
      unreadCount.value = 0
    } catch (err) {
      console.error('Error clearing all notifications:', err)
    }
  }

  /**
   * Fetch notifications by type
   */
  const fetchByType = async (type: string, limit = 10) => {
    loading.value = true
    error.value = null

    try {
      await router.get(`/notifications/by-type/${type}`, {
        limit
      }, {
        preserveState: true,
        preserveScroll: true,
        only: ['notifications']
      })

      // The response will be handled by the page props
    } catch (err) {
      error.value = 'Failed to fetch notifications by type'
      console.error('Error fetching notifications by type:', err)
    } finally {
      loading.value = false
    }
  }

  /**
   * Create a test notification (for development)
   */
  const createTestNotification = async (data: {
    type: string
    title: string
    message: string
    severity?: 'info' | 'success' | 'warning' | 'error'
    action_label?: string
    action_url?: string
    action_data?: any
  }) => {
    try {
      await router.post('/notifications/test', data, {
        preserveState: true,
        preserveScroll: true
      })

      // Refresh notifications after creating test notification
      await fetchNotifications()
      await fetchUnreadCount()
    } catch (err) {
      console.error('Error creating test notification:', err)
    }
  }

  /**
   * Initialize notifications
   */
  const initialize = async () => {
    await Promise.all([
      fetchNotifications(),
      fetchUnreadCount()
    ])
  }

  return {
    // State
    notifications,
    unreadCount,
    loading,
    error,
    hasUnreadNotifications,

    // Methods
    fetchNotifications,
    fetchUnreadCount,
    markAsRead,
    markAllAsRead,
    deleteNotification,
    clearAll,
    fetchByType,
    createTestNotification,
    initialize
  }
} 