<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationType;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create a new notification for a user.
     */
    public function createNotification(
        User $user,
        string $typeName,
        string $title,
        string $message,
        string $severity = 'info',
        array $options = []
    ): Notification {
        $notificationType = NotificationType::findByName($typeName);

        if (!$notificationType) {
            throw new \InvalidArgumentException("Notification type '{$typeName}' not found.");
        }

        return Notification::create([
            'user_id' => $user->id,
            'notification_type_id' => $notificationType->id,
            'title' => $title,
            'message' => $message,
            'severity' => $severity,
            'action_label' => $options['action_label'] ?? null,
            'action_url' => $options['action_url'] ?? null,
            'action_data' => $options['action_data'] ?? null,
            'related_entity_type' => $options['related_entity_type'] ?? null,
            'related_entity_id' => $options['related_entity_id'] ?? null,
            'metadata' => $options['metadata'] ?? null,
            'scheduled_at' => $options['scheduled_at'] ?? null,
            'sent_at' => $options['sent_at'] ?? now(),
        ]);
    }

    /**
     * Create a property-related notification.
     */
    public function createPropertyViewNotification(
        User $user,
        string $title,
        string $message,
        string $severity = 'info',
        ?int $propertyId = null,
        array $options = []
    ): Notification {
        $options['related_entity_type'] = 'property';
        $options['related_entity_id'] = $propertyId;
        $options['action_label'] = 'View Property';
        $options['action_url'] = route('property.search') . '?propertyid=' . $propertyId;

        return $this->createNotification($user, 'property', $title, $message, $severity, $options);
    }

    /**
     * Create a user account notification.
     */
    public function createUserAccountNotification(
        User $user,
        string $title,
        string $message,
        string $severity = 'info',
        array $options = []
    ): Notification {
        $options['related_entity_type'] = 'user-account';
        $options['related_entity_id'] = $user->id;
        $options['action_label'] = 'View Account';
        $options['action_url'] = route('settings.account');
        return $this->createNotification($user, 'user-account', $title, $message, $severity, $options);
    }

    /**
     * Create a site update notification.
     */
    public function createSiteUpdateNotification(
        User $user,
        string $title,
        string $message,
        string $severity = 'info',
        array $options = []
    ): Notification {
        $options['related_entity_type'] = 'site-updates';
        $options['related_entity_id'] = $user->id;
        $options['action_label'] = null;
        $options['action_url'] = null;
        return $this->createNotification($user, 'site-updates', $title, $message, $severity, $options);
    }

    /**
     * Create a buyer activity notification.
     */
    public function createBuyerNotification(
        User $user,
        string $title,
        string $message,
        string $severity = 'info',
        ?int $buyerId = null,
        array $options = []
    ): Notification {
        $options['related_entity_type'] = 'buyer';
        $options['related_entity_id'] = $buyerId;
        $options['action_label'] = null;
        $options['action_url'] = null;
        return $this->createNotification($user, 'buyers', $title, $message, $severity, $options);
    }

    /**
     * Create an email marketing notification.
     */
    public function createEmailMarketingNotification(
        User $user,
        string $title,
        string $message,
        string $severity = 'info',
        ?int $campaignId = null,
        array $options = []
    ): Notification {
        $options['related_entity_type'] = 'campaign';
        $options['related_entity_id'] = $campaignId;
        $options['action_label'] = 'View Campaign';
        $options['action_url'] = route('email-marketing.campaign') . '?campaignid=' . $campaignId;
        return $this->createNotification($user, 'email-marketing', $title, $message, $severity, $options);
    }

    /**
     * Create a subscription notification.
     */
    public function createSubscriptionNotification(
        User $user,
        string $title,
        string $message,
        string $severity = 'info',
        array $options = []
    ): Notification {
        return $this->createNotification($user, 'subscription', $title, $message, $severity, $options);
    }

    /**
     * Get notifications for a user.
     */
    public function getUserNotifications(
        User $user,
        int $limit = 10,
        bool $includeRead = true
    ): Collection {
        $query = $user->notifications()->with('notificationType')->orderBy('created_at', 'desc');

        if (!$includeRead) {
            $query->unread();
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get unread notifications count for a user.
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotificationsCount();
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $notificationId, User $user): bool
    {
        $notification = $user->notifications()->find($notificationId);

        if (!$notification) {
            return false;
        }

        return $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(User $user): int
    {
        return $user->markAllNotificationsAsRead();
    }

    /**
     * Delete a notification.
     */
    public function deleteNotification(string $notificationId, User $user): bool
    {
        $notification = $user->notifications()->find($notificationId);

        if (!$notification) {
            return false;
        }

        return $notification->delete();
    }

    /**
     * Clear all notifications for a user.
     */
    public function clearAllNotifications(User $user): int
    {
        return $user->notifications()->delete();
    }

    /**
     * Get notifications by type for a user.
     */
    public function getNotificationsByType(
        User $user,
        string $typeName,
        int $limit = 10
    ): Collection {
        return $user->notificationsByType($typeName)
            ->with('notificationType')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all active notification types.
     */
    public function getActiveNotificationTypes(): Collection
    {
        return NotificationType::getActiveTypes();
    }
}