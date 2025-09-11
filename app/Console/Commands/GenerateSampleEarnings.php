<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserReferralEarning;
use App\Services\ReferralEarningService;
use Illuminate\Console\Command;

class GenerateSampleEarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referrals:generate-sample-earnings {--count=10 : Number of sample earnings to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sample referral earnings for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        $earningService = new ReferralEarningService();

        // Get users with affiliates
        $referrers = User::whereNotNull('affiliate_slug')
            ->whereHas('affiliates')
            ->with('affiliates')
            ->get();

        if ($referrers->isEmpty()) {
            $this->error('No users with affiliates found. Please create some referral relationships first.');
            return 1;
        }

        $this->info("Generating {$count} sample earnings...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $generated = 0;

        for ($i = 0; $i < $count; $i++) {
            // Randomly select a referrer
            $referrer = $referrers->random();
            
            // Get a random affiliate of this referrer
            $affiliate = $referrer->affiliates->random();
            
            // Randomly select earning type
            $types = [
                UserReferralEarning::TYPE_SUBSCRIPTION_UPGRADE,
                UserReferralEarning::TYPE_FIRST_PAYMENT,
                UserReferralEarning::TYPE_RECURRING_PAYMENT,
                UserReferralEarning::TYPE_BONUS,
            ];
            $type = $types[array_rand($types)];
            
            // Generate random amount based on type
            $amount = $this->generateRandomAmount($type);
            
            // Generate the earning
            $earning = $earningService->generateEarning(
                $affiliate,
                $referrer,
                $amount,
                $type,
                "Sample {$type} earning for testing"
            );
            
            if ($earning) {
                $generated++;
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully generated {$generated} sample earnings!");

        // Show summary
        $this->newLine();
        $this->info('Earnings Summary:');
        $this->table(
            ['Status', 'Count', 'Total Amount'],
            [
                ['Pending', UserReferralEarning::pending()->count(), '$' . number_format(UserReferralEarning::pending()->sum('amount'), 2)],
                ['Approved', UserReferralEarning::approved()->count(), '$' . number_format(UserReferralEarning::approved()->sum('amount'), 2)],
                ['Paid', UserReferralEarning::paid()->count(), '$' . number_format(UserReferralEarning::paid()->sum('amount'), 2)],
                ['Total', UserReferralEarning::count(), '$' . number_format(UserReferralEarning::sum('amount'), 2)],
            ]
        );

        return 0;
    }

    /**
     * Generate random amount based on earning type
     */
    private function generateRandomAmount(string $type): float
    {
        return match($type) {
            UserReferralEarning::TYPE_SUBSCRIPTION_UPGRADE => rand(50, 200),
            UserReferralEarning::TYPE_FIRST_PAYMENT => rand(100, 500),
            UserReferralEarning::TYPE_RECURRING_PAYMENT => rand(25, 100),
            UserReferralEarning::TYPE_BONUS => rand(10, 50),
            default => rand(25, 100),
        };
    }
}
