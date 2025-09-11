<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionLog;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Cassandra\Type\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function createSubscription(Request $request)
    {
        $user = auth()->user();
        $userSubscriptionID = $user->stripe_subscription_id;

        $priceId = $request->get('price_id');
        $counties = $request->get('counties');

        $isMonthly = $request->get('is_monthly');
        $quantity = $request->get('quantity');
        $successUrl = route('subscription.success');
        $cancelUrl = route('subscription.failed');

        $isTest = str_starts_with(config('services.stripe.secret'), 'sk_test_');

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            if (!empty($userSubscriptionID)) {
                $subscription = Subscription::retrieve($userSubscriptionID);

                // update existing subscription items
                $updated = Subscription::update($userSubscriptionID, [
                    'cancel_at_period_end' => false,
                    'items' => [
                        [
                            'id' => $subscription->items->data[0]->id,
                            'price' => $priceId,
                            'quantity' => $quantity,
                        ]
                    ],
                    'metadata' => [
                        'user_id' => $user->id,
                        'counties' => json_encode($counties),
                        'is_monthly' => $isMonthly,
                        'quantity' => $quantity,
                    ],
                ]);

                $updateArray = [
                    'subscription_status' => $updated->status,
                    'subscription_period_monthly' => $isMonthly,
                    'subscription_start' => now(),
                    'subscription_end' => now()->addMonths($isMonthly ? 1 : 12),
                ];

                if (in_array($updated->status, ['active', 'trialing'])) {
                    $updateArray = array_merge($updateArray, [
                        'user_type' => User::PREMIUM,
                        'subscribed_counties' => $counties,
                    ]);
                }

                $user->update($updateArray);

                SubscriptionLog::create([
                    'user_id' => $user->id,
                    'stripe_subscription_id' => $updated->id,
                    'stripe_price_id' => $priceId,
                    'quantity' => $quantity,
                    'is_monthly' => $isMonthly,
                    'starts_at' => now(),
                    'is_test' => $isTest,
                    'ends_at' => now()->addMonths($isMonthly ? 1 : 12),
                    'status' => 'success',
                    'extra_data' => json_encode([
                        'counties' => $counties,
                    ]),
                ]);

                return response()->json(['success' => 'Subscription has been updated, (' . count($counties) . ') Counties']);
            }
        } catch (ApiErrorException $e) {
            Log::warning('Stripe subscription update failed: ' . $e->getMessage());
            return response()->json(['error' => 'Stripe subscription update failed: ' . $e->getMessage()]);
        }
        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'line_items' => [
                [
                    'price' => $priceId,
                    'quantity' => $quantity,
                ]
            ],
            'customer_email' => $user->email,
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'user_id' => $user->id,
                'counties' => json_encode($counties),
                'is_monthly' => $isMonthly,
                'quantity' => $quantity,
            ],
        ]);

        return response()->json(['url' => $session->url]);
    }

    public function failedResponse(Request $request)
    {
        $user = auth()->user();

        SubscriptionLog::create([
            'user_id' => $user->id,
            'stripe_subscription_id' => null,
            'stripe_price_id' => null,
            'quantity' => 0,
            'is_monthly' => false,
            'starts_at' => null,
            'ends_at' => null,
            'status' => 'failed',
            'extra' => null,
        ]);

        return redirect()->route('profile.edit')->with('error', 'Subscription was cancelled or failed.');

    }

    public function successResponse(Request $request)
    {
        $user = auth()->user();

        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('settings.profile')->with('error', 'Missing session ID.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = Session::retrieve($sessionId);
            $subscription = Subscription::retrieve($session->subscription);
            $metadata = $session->metadata;

            $priceId = $subscription->items->data[0]->price->id;
            $isMonthly = $priceId === config('services.stripe.monthly');
            $quantity = $subscription->items->data[0]->quantity;
            $startsAt = now();
            $endsAt = now()->addMonths($isMonthly ? 1 : 12);
            $counties = json_decode($metadata->counties) ?? [];

            $user->update([
                'user_type' => User::PREMIUM,
                'subscribed_counties' => $counties,
                'stripe_customer_id' => $session->customer,
                'stripe_subscription_id' => $subscription->id,
                'subscription_start' => $startsAt,
                'subscription_end' => $endsAt,
                'subscription_period_monthly' => $isMonthly,
                'subscription_status' => $subscription->status,
            ]);

            SubscriptionLog::create([
                'user_id' => $user->id,
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id' => $priceId,
                'quantity' => $quantity,
                'is_monthly' => $isMonthly,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => $subscription->status,
                'extra' => json_encode([
                    'counties' => $counties,
                ]),
            ]);

            return redirect()->route('profile.edit')->with('success', 'Subscription activated successfully!');
        } catch (\Exception $e) {
            Log::error('Stripe subscription success error: ' . $e->getMessage());
            return redirect()->route('profile.edit')->with('error', 'Something went wrong with your subscription: ' . $e->getMessage());
        }
    }

    public function cancelSubscription(Request $request)
    {
        $user = auth()->user();
        $subscriptionId = $user->stripe_subscription_id;

        if (empty($subscriptionId)) {
            return response()->json(['error' => 'No active subscription to cancel.'], 400);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $canceledSubscription = Subscription::update($subscriptionId, [
                'cancel_at_period_end' => false, // cancel immediately
            ]);
            $canceledSubscription->cancel();

            // Update user model
            $user->update([
                'stripe_subscription_id' => null,
                'subscription_status' => 'canceled',
                'subscription_start' => null,
                'subscription_end' => now(),
                'user_type' => User::FREE,
            ]);

            // Log the cancellation
            SubscriptionLog::create([
                'user_id' => $user->id,
                'stripe_subscription_id' => $subscriptionId,
                'status' => 'canceled',
                'starts_at' => $user->subscription_start,
                'ends_at' => now(),
                'extra_data' => json_encode(['reason' => 'user-canceled']),
            ]);

            return response()->json(['success' => 'Your subscription has been canceled successfully.']);

        } catch (ApiErrorException $e) {
            Log::error("Stripe subscription cancellation failed: " . $e->getMessage());
            return response()->json(['error' => 'Unable to cancel subscription. Try again later.'], 500);
        }
    }

    public function webHookHandling(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return response('Invalid payload', 400);
        }

        switch ($event->type) {
            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                $subscription = $event->data->object;

                $user = User::where('stripe_customer_id', $subscription->customer)->first();
                if ($user) {
                    $isMonthly = $subscription->items->data[0]->price->id === config('services.stripe.monthly');
                    $quantity = $subscription->items->data[0]->quantity;

                    $user->update([
                        'stripe_subscription_id' => $subscription->id,
                        'subscription_period_monthly' => $isMonthly,
                        'subscription_start' => Carbon::createFromTimestamp($subscription->current_period_start),
                        'subscription_end' => Carbon::createFromTimestamp($subscription->current_period_end),
                        'subscription_status' => $subscription->status,
                    ]);

                    SubscriptionLog::create([
                        'user_id' => $user->id,
                        'stripe_subscription_id' => $subscription->id,
                        'stripe_price_id' => $subscription->items->data[0]->price->id,
                        'quantity' => $quantity,
                        'is_monthly' => $isMonthly,
                        'starts_at' => Carbon::createFromTimestamp($subscription->current_period_start),
                        'ends_at' => Carbon::createFromTimestamp($subscription->current_period_end),
                        'status' => $subscription->status,
                    ]);

                    @(new NotificationService)->createSubscriptionNotification($user, 'Subscription Activated', 'Your subscription has been activated successfully.', 'info');
                }
                break;

            case 'invoice.payment_failed':
                $subscription = $event->data->object->subscription;
                $user = User::where('stripe_subscription_id', $subscription)->first();
                if ($user) {
                    $user->update(['subscription_status' => 'failed']);
                }
                @(new NotificationService)->createSubscriptionNotification($user, 'Subscription Failed', 'Your subscription has failed.', 'error');
                break;

            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                $user = User::where('stripe_subscription_id', $subscription->id)->first();
                if ($user) {
                    $user->update([
                        'stripe_subscription_id' => null,
                        'subscription_status' => 'canceled',
                        'subscription_start' => null,
                        'subscription_end' => now(),
                        'user_type' => User::FREE,
                    ]);

                    SubscriptionLog::create([
                        'user_id' => $user->id,
                        'stripe_subscription_id' => $subscription->id,
                        'status' => 'canceled',
                        'starts_at' => $user->subscription_start,
                        'ends_at' => now(),
                        'extra_data' => json_encode(['reason' => 'stripe-webhook-deleted']),
                    ]);
                    @(new NotificationService)->createSubscriptionNotification($user, 'Subscription Canceled', 'Your subscription has been canceled.', 'error');
                }
                break;

            // Optional: Handle cancel, delete, etc.
        }

        return response('Webhook Handled', 200);
    }
}
