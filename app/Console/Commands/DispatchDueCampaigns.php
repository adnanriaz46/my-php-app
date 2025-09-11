<?php

namespace App\Console\Commands;

use App\Helper\EmailMarketingHelper;
use App\Models\Campaign;
use App\Models\CronProcessLog;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class DispatchDueCampaigns extends Command
{
	protected $signature = 'campaigns:dispatch-due {--limit=50} {--memory=2048} {--timeout=1200}';
	protected $description = 'Process due scheduled email campaigns and send emails without overlap';

	public function handle(): int
	{
		$startedAt = Carbon::now();
		$log = CronProcessLog::create([
			'command' => 'campaigns:dispatch-due',
			'context' => [
				'limit' => (int) $this->option('limit'),
				'memory' => (int) $this->option('memory'),
				'timeout' => (int) $this->option('timeout'),
			],
			'started_at' => $startedAt,
			'status' => 'running',
		]);

		// Raise limits for this process
		@ini_set('memory_limit', ((int) $this->option('memory')) . 'M');
		@set_time_limit((int) $this->option('timeout'));

		$processed = 0;
		$success = 0;
		$failed = 0;
		$errors = [];

		try {
			$limit = (int) $this->option('limit');
			$now = Carbon::now();

			// Query due campaigns
			Campaign::where('status', 'scheduled')
				->with('user')
				->whereNotNull('scheduled_at')
				->where('scheduled_at', '<=', $now)
				->orderBy('scheduled_at')
				->limit($limit)
				->chunkById(50, function ($campaigns) use (&$processed, &$success, &$failed, &$errors) {
					foreach ($campaigns as $campaign) {
						$processed++;

						// Per-campaign lock to avoid overlap
						$lockKey = 'campaign:send:' . $campaign->id;
						$lock = Cache::lock($lockKey, 600); // 10 minutes
	
						if (!$lock->get()) {
							$this->info("Skipped campaign {$campaign->id} due to existing lock");
							continue;
						}

						try {
							// Double-check status to avoid racing
							$campaign->refresh();
							if ($campaign->status !== 'scheduled') {
								$lock->release();
								$this->info("Campaign {$campaign->id} no longer scheduled");
								continue;
							}

							// Send emails
							$recipients = $campaign->recipients()->where('status', 'pending')->get();
							if ($recipients->count() > 0) {
								EmailMarketingHelper::sendMarketingEmail($campaign, $recipients);
							}

							$campaign->update([
								'status' => 'sent',
								'sent_at' => Carbon::now(),
							]);
							$user = $campaign->user;
							@(new NotificationService)->createEmailMarketingNotification($user, 'Campaign Sent', 'Your campaign has been sent successfully.', 'success', $campaign->id);
							$success++;
						} catch (\Throwable $e) {
							$failed++;
							$errors[] = [
								'campaign_id' => $campaign->id,
								'message' => $e->getMessage(),
							];
							$this->error("Campaign {$campaign->id} failed: {$e->getMessage()}");
						} finally {
							optional($lock)->release();
						}
					}
				});

			$log->update([
				'status' => 'success',
				'ended_at' => Carbon::now(),
				'processed_count' => $processed,
				'success_count' => $success,
				'failure_count' => $failed,
				'response' => ['errors' => $errors],
			]);

			$this->info("Processed: {$processed}, success: {$success}, failed: {$failed}");
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


