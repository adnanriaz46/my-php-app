<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\CronProcessLog;
use App\Services\EmailService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckUserSubscriptions extends Command
{
    protected $signature = 'subscriptions:check {--limit=1000} {--dry-run}';
    protected $description = 'Check user subscriptions: email on expiry day; after 5 days set to FREE; immediately set FREE on failed status. Skips admin users.';

    public function handle(): int
    {
        $startedAt = Carbon::now();
        $log = CronProcessLog::create([
            'command' => 'subscriptions:check',
            'context' => [
                'limit' => (int) $this->option('limit'),
                'dry_run' => (bool) $this->option('dry-run'),
            ],
            'started_at' => $startedAt,
            'status' => 'running',
        ]);

        $limit = (int) $this->option('limit');
        $dryRun = (bool) $this->option('dry-run');

        $processed = 0;
        $emailed = 0;
        $setFree = 0;
        $failed = 0;
        $errors = [];

        try {
            $today = Carbon::today();

            // 1) Send expiry email on the day subscription ends (non-admins only)
            User::query()
                ->where('user_type', '!=', User::ADMIN)
                ->where('user_type', '!=', User::FREE)
                ->whereNotNull('subscription_end')
                ->whereDate('subscription_end', '=', $today)
                ->limit($limit)
                ->chunkById(100, function ($users) use (&$processed, &$emailed, &$failed, &$errors, $dryRun) {
                    $emailService = app(EmailService::class);
                    $config = config('user_email_config.account.subscription_expired');

                    foreach ($users as $user) {
                        $processed++;
                        if ($dryRun) {
                            continue;
                        }
                        try {

                            $upgradeUrl = url('pricing');
                            $content = <<<HTML
<div>
  <p><strong>Your subscription has expired.</strong></p>
  <p>To regain full access, please <a href="{$upgradeUrl}" style="color: #FFC107; text-decoration: underline;">renew or upgrade your subscription</a>.</p>
  <p>Thank you for being with us.</p>
</div>
HTML;
                            $sent = $emailService->send($user, 'account', $config['template_id'] ?? '', [
                                'subject' => 'Your subscription has expired',
                                'body' => $content,
                            ]);
                            if ($sent) {
                                $emailed++;
                            }
                            @(new NotificationService)->createSubscriptionNotification($user, 'Subscription Expired', 'Your subscription has expired.', 'error');
                        } catch (\Throwable $e) {
                            $failed++;
                            $errors[] = ['user_id' => $user->id, 'message' => $e->getMessage()];
                        }
                    }
                });

            // 2) After 5 days past expiration, set user to FREE (non-admins only)
            User::query()
                ->where('user_type', '!=', User::ADMIN)
                ->where('user_type', '!=', User::FREE)
                ->whereNotNull('subscription_end')
                ->where('subscription_end', '<=', Carbon::now()->subDays(5))
                ->limit($limit)
                ->chunkById(100, function ($users) use (&$processed, &$setFree, &$failed, &$errors, $dryRun) {
                    foreach ($users as $user) {
                        $processed++;
                        if ($dryRun) {
                            continue;
                        }
                        try {
                            $user->update(['user_type' => User::FREE]);
                            $setFree++;
                        } catch (\Throwable $e) {
                            $failed++;
                            $errors[] = ['user_id' => $user->id, 'message' => $e->getMessage()];
                        }
                    }
                });

            // 3) Any user with failed subscription status becomes FREE immediately (non-admins only)
            User::query()
                ->where('user_type', '!=', User::ADMIN)
                ->where('user_type', '!=', User::FREE)
                ->where('subscription_status', 'failed')
                ->limit($limit)
                ->chunkById(100, function ($users) use (&$processed, &$setFree, &$failed, &$errors, $dryRun) {
                    foreach ($users as $user) {
                        $processed++;
                        if ($dryRun) {
                            continue;
                        }
                        try {
                            $user->update(['user_type' => User::FREE]);
                            $setFree++;
                        } catch (\Throwable $e) {
                            $failed++;
                            $errors[] = ['user_id' => $user->id, 'message' => $e->getMessage()];
                        }
                    }
                });

            $log->update([
                'status' => 'success',
                'ended_at' => Carbon::now(),
                'processed_count' => $processed,
                'success_count' => $emailed + $setFree,
                'failure_count' => $failed,
                'response' => [
                    'emailed' => $emailed,
                    'set_free' => $setFree,
                    'errors' => $errors,
                ],
            ]);

            $this->info("Processed: {$processed}, emailed: {$emailed}, set_free: {$setFree}, failed: {$failed}");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $log->update([
                'status' => 'failed',
                'ended_at' => Carbon::now(),
                'error_message' => $e->getMessage(),
            ]);
            $this->error('Command failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}


