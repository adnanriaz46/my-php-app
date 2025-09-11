<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\SendGridEvent;

class SendGridWebhookController extends Controller
{
    /**
     * Handle SendGrid webhook events
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function eventSync(Request $request)
    {
        try {
            // Verify webhook signature (optional but recommended for production)
            if (!$this->verifyWebhookSignature($request)) {
                Log::warning('SendGrid webhook signature verification failed');
                return response()->json(['message' => 'Invalid signature'], 401);
            }

            $events = $request->input();

            if (!$events || !isset($events[0])) {
                Log::warning('SendGrid webhook received empty or invalid events data');
                return response()->json(['message' => 'No events received'], 200);
            }

            $processedCount = 0;
            $errors = [];

            foreach ($events as $event) {
                try {
                    $email = $event['email'] ?? '';
                    $eventType = $event['event'] ?? '';
                    $timestamp = $event['timestamp'] ?? '';
                    $response = $event['response'] ?? '';
                    $messageId = $event['sg_message_id'] ?? '';
                    $eventId = $event['sg_event_id'] ?? '';
                    $category = $event['category'] ?? null;
                    $status = $event['status'] ?? null;
                    $type = $event['type'] ?? null;
                    $reason = $event['reason'] ?? null;
                    $machineOpen = $event['machine_open'] ?? null;

                    $eventData = [
                        'date_time' => $timestamp ? Carbon::createFromTimestamp($timestamp)->toDateTimeString() : null,
                        'category' => $category,
                        'email' => $email,
                        'event' => $eventType,
                        'event_id' => $eventId,
                        'machine_open' => $machineOpen,
                        'message_id' => $messageId ? explode('.', $messageId)[0] : '',
                        'message_id_full' => $messageId,
                        'reason' => $reason,
                        'response' => $response,
                        'status' => $status,
                        'timestamp' => $timestamp ? Carbon::createFromTimestamp($timestamp)->toDateTimeString() : null,
                        'type' => $type,
                    ];

                    SendGridEvent::create($eventData);
                    $processedCount++;

                } catch (\Exception $e) {
                    $errors[] = [
                        'event' => $event,
                        'error' => $e->getMessage()
                    ];
                    Log::error('SendGrid event processing error: ' . $e->getMessage(), [
                        'event' => $event,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            Log::info("SendGrid webhook processed: {$processedCount} events successfully" .
                (count($errors) > 0 ? ", " . count($errors) . " errors" : ""));

            return response()->json([
                'message' => 'Webhook processed successfully',
                'processed' => $processedCount,
                'errors' => count($errors)
            ], 200);

        } catch (\Exception $e) {
            Log::error('SendGrid webhook processing failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify SendGrid webhook signature
     * 
     * @param Request $request
     * @return bool
     */
    private function verifyWebhookSignature(Request $request): bool
    {
        // Skip verification if webhook secret is not configured
        $webhookSecret = config('services.sendgrid.webhook_secret');
        if (!$webhookSecret) {
            return true; // Skip verification if not configured
        }

        $signature = $request->header('X-Twilio-Email-Event-Webhook-Signature');
        $timestamp = $request->header('X-Twilio-Email-Event-Webhook-Timestamp');
        $payload = $request->getContent();

        if (!$signature || !$timestamp) {
            return false;
        }

        // Verify timestamp is within 5 minutes
        if (abs(time() - $timestamp) > 300) {
            return false;
        }

        // Verify signature
        $expectedSignature = hash_hmac('sha256', $timestamp . $payload, $webhookSecret);
        return hash_equals($expectedSignature, $signature);
    }
}
