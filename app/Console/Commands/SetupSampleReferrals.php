<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SetupSampleReferrals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referrals:setup-sample {--count=5 : Number of sample referrals to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up sample referral relationships for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->option('count');

        // Get existing users
        $users = User::all();

        if ($users->count() < 2) {
            $this->error('Need at least 2 users to create referral relationships.');
            return 1;
        }

        $this->info("Setting up {$count} sample referral relationships...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $created = 0;

        for ($i = 0; $i < $count; $i++) {
            // Get a random user as referrer
            $referrer = $users->random();
            
            // Get a different user as affiliate
            $affiliate = $users->where('id', '!=', $referrer->id)->random();
            
            // Update affiliate to have referrer
            if (!$affiliate->affiliated_user) {
                $affiliate->update([
                    'affiliated_user' => $referrer->id,
                    'affiliate_eligible' => true,
                    'affiliate_slug' => (string) Str::uuid(),
                ]);
                $created++;
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully created {$created} referral relationships!");

        // Show summary
        $this->newLine();
        $this->info('Referral Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Users with affiliate_slug', User::whereNotNull('affiliate_slug')->count()],
                ['Users with affiliated_user', User::whereNotNull('affiliated_user')->count()],
                ['Total Users', User::count()],
            ]
        );

        return 0;
    }
}
