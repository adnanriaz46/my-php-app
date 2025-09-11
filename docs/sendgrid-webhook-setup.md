# SendGrid Webhook Setup

This document explains how to set up and use the SendGrid webhook integration for tracking email events.

## Overview

The SendGrid webhook integration allows you to receive real-time notifications about email events such as:
- Delivered
- Opened
- Clicked
- Bounced
- Dropped
- Spam reports
- Unsubscribes

## Database Migration

The webhook events are stored in the `sendgrid_events` table. Run the migration to create the table:

```bash
php artisan migrate
```

## Environment Configuration

Add the following environment variables to your `.env` file:

```env
# SendGrid Webhook Secret (optional but recommended for production)
SENDGRID_WEBHOOK_SECRET=your_webhook_secret_here
```

## Webhook Endpoint

The webhook endpoint is available at:
```
POST /webhook/sendgrid/events
```

## Setting up SendGrid Webhook

1. Log in to your SendGrid account
2. Go to Settings > Mail Settings > Event Webhook
3. Configure the following settings:
   - **HTTP Post URL**: `https://yourdomain.com/webhook/sendgrid/events`
   - **HTTP Post URL**: Select the events you want to track
   - **HTTP Post URL**: Enable "Signed Webhooks" for security (recommended)

## Webhook Events Tracked

The following fields are captured from SendGrid events:

- `date_time` - Event timestamp
- `category` - Email category
- `email` - Recipient email address
- `event` - Event type (delivered, opened, clicked, etc.)
- `event_id` - SendGrid event ID
- `machine_open` - Whether the open was from a machine
- `message_id` - Message ID (shortened)
- `message_id_full` - Full message ID
- `reason` - Reason for bounce/drop
- `response` - Response details
- `status` - Event status
- `timestamp` - Event timestamp
- `type` - Event type

## Using the SendGridEvent Model

```php
use App\Models\SendGridEvent;

// Get all events for a specific email
$events = SendGridEvent::getEventsForEmail('user@example.com');

// Get events by type
$deliveredEvents = SendGridEvent::byEvent('delivered')->get();

// Get events for a specific message
$messageEvents = SendGridEvent::getEventsForMessage('message_id');

// Get recent events
$recentEvents = SendGridEvent::orderBy('timestamp', 'desc')->limit(100)->get();
```

## Security

The webhook includes signature verification to ensure requests are coming from SendGrid:

1. Enable "Signed Webhooks" in your SendGrid settings
2. Set the `SENDGRID_WEBHOOK_SECRET` environment variable
3. The webhook will automatically verify the signature

## Testing

You can test the webhook endpoint using tools like Postman or curl:

```bash
curl -X POST https://yourdomain.com/webhook/sendgrid/events \
  -H "Content-Type: application/json" \
  -d '[
    {
      "email": "test@example.com",
      "event": "delivered",
      "timestamp": 1234567890,
      "sg_message_id": "test_message_id",
      "sg_event_id": "test_event_id"
    }
  ]'
```

## Monitoring

The webhook logs all activities. Check your Laravel logs for:
- Successful event processing
- Error messages
- Signature verification failures

## Troubleshooting

1. **Webhook not receiving events**: Check your SendGrid webhook configuration
2. **Signature verification failing**: Ensure the webhook secret is correctly set
3. **Database errors**: Check your database connection and migration status
4. **Performance issues**: Consider implementing queue processing for high-volume webhooks

## Queue Processing (Optional)

For high-volume webhooks, consider implementing queue processing:

1. Create a job for processing webhook events
2. Dispatch the job instead of processing immediately
3. Configure your queue worker to process the jobs

This will improve webhook response times and prevent timeouts. 