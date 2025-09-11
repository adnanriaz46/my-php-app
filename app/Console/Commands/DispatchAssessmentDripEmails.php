<?php

namespace App\Console\Commands;

use App\Models\AssessmentDripEmail;
use App\Models\AssessmentResponse;
use App\Models\CronProcessLog;
use App\Models\User;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class DispatchAssessmentDripEmails extends Command
{
    protected $signature = 'assessments:dispatch-drip {--limit=200} {--memory=512} {--timeout=600}';
    protected $description = 'Send daily assessment drip emails: one step per day per assessment response, observing 24h spacing';

    public function handle(): int
    {
        $startedAt = Carbon::now();
        $log = CronProcessLog::create([
            'command' => 'assessments:dispatch-drip',
            'context' => [
                'limit' => (int) $this->option('limit'),
                'memory' => (int) $this->option('memory'),
                'timeout' => (int) $this->option('timeout'),
            ],
            'started_at' => $startedAt,
            'status' => 'running',
        ]);

        @ini_set('memory_limit', ((int) $this->option('memory')) . 'M');
        @set_time_limit((int) $this->option('timeout'));

        $processed = 0;
        $success = 0;
        $failed = 0;
        $errors = [];

        try {
            $now = Carbon::now();
            $limit = (int) $this->option('limit');

            // Eligible assessments: last drip sent at least 24h ago and has a pending next step
            AssessmentResponse::query()
                ->where(function ($query) use ($now) { // filter out assessments that have not sent any drip email yet or that have sent the drip email more than 24h ago
                    $query->whereNull('drip_mail_sent_at')
                        ->orWhere('drip_mail_sent_at', '<=', $now->copy()->subDay());
                })
                ->where('drip_mail_completed_step', '<=', 20) // filter out assessments that have completed all steps
                ->whereHas('dripEmails', function ($q) { // filter out assessments that have no pending steps
                    $q->where('status', AssessmentDripEmail::STATUS_PENDING);
                })
                ->orderBy('drip_mail_sent_at')
                ->limit($limit)
                ->chunkById(50, function ($assessments) use (&$processed, &$success, &$failed, &$errors) {
                    foreach ($assessments as $assessment) {
                        $processed++;

                        $lockKey = 'assessment:drip:' . $assessment->id;
                        $lock = Cache::lock($lockKey, 300); // 5 minutes
    
                        if (!$lock->get()) {
                            $this->info("Skipped assessment {$assessment->id} due to existing lock");
                            continue;
                        }

                        try {
                            $assessment->refresh();

                            // Determine next pending step greater than completed step
                            $nextEmail = $assessment->dripEmails()
                                ->pending()
                                ->where('step', '>', (int) ($assessment->drip_mail_completed_step ?? 0))
                                ->orderBy('step')
                                ->first();

                            if (!$nextEmail) {
                                continue; // nothing to send
                            }

                            // Send email using EmailService
                            $emailService = app(EmailService::class);
                            $config = config('user_email_config.categories_description.assessment_drip');

                            $data = [
                                'subject' => $nextEmail->subject,
                                'body' => $nextEmail->body,
                            ];

                            $sent = false;

                            $user = User::where('email', $assessment->email)->first();
                            if ($user) {
                                $data['unsubscribe'] = url('unsubscribe/' . $user->uuid);
                                $sent = $emailService->send($user, 'assessment_drip', $config['template_id'], $data);
                            } else {
                                $sent = $emailService->sendCustom(
                                    $assessment->email,
                                    trim(($assessment->first_name ?? '') . ' ' . ($assessment->last_name ?? '')),
                                    'assessment_drip',
                                    $config['template_id'],
                                    $data
                                );
                            }

                            if ($sent) {
                                // Update statuses
                                $nextEmail->markAsSent();
                                $assessment->update([
                                    'drip_mail_completed_step' => (int) $nextEmail->step,
                                    'drip_mail_sent_at' => now(),
                                ]);
                                $success++;
                            } else {
                                $failed++;
                                $errors[] = [
                                    'assessment_id' => $assessment->id,
                                    'message' => 'Email send returned false',
                                ];
                            }
                        } catch (\Throwable $e) {
                            $failed++;
                            $errors[] = [
                                'assessment_id' => $assessment->id,
                                'message' => $e->getMessage(),
                            ];
                            $this->error("Assessment {$assessment->id} failed: {$e->getMessage()}");
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


