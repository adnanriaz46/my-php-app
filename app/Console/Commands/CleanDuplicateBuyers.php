<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MyBuyerListBuyer;
use Illuminate\Support\Facades\DB;

class CleanDuplicateBuyers extends Command
{
    protected $signature = 'buyers:clean-duplicates';
    protected $description = 'Clean duplicate buyer records within the same list (keep only first occurrence of each investor_identifier per list)';

    public function handle()
    {
        $this->info('Cleaning duplicate buyers within the same list...');
        
        // Find duplicates within the same list (my_buyer_list_id + investor_identifier)
        $duplicates = DB::table('my_buyer_list_buyers')
            ->select('my_buyer_list_id', 'investor_identifier', DB::raw('COUNT(*) as duplicate_count'))
            ->groupBy('my_buyer_list_id', 'investor_identifier')
            ->havingRaw('COUNT(*) > 1')
            ->get();
        
        $totalDeleted = 0;
        
        foreach ($duplicates as $duplicate) {
            $this->info("Found {$duplicate->duplicate_count} records for investor '{$duplicate->investor_identifier}' in list ID {$duplicate->my_buyer_list_id}");
            
            // Get all records for this investor_identifier in this specific list, ordered by ID (keep the first one)
            $records = MyBuyerListBuyer::where('my_buyer_list_id', $duplicate->my_buyer_list_id)
                ->where('investor_identifier', $duplicate->investor_identifier)
                ->orderBy('id')
                ->get();
            
            // Keep the first one, delete the rest
            for ($i = 1; $i < count($records); $i++) {
                $records[$i]->delete();
                $this->info("Deleted duplicate record ID: {$records[$i]->id} (List ID: {$records[$i]->my_buyer_list_id})");
                $totalDeleted++;
            }
        }
        
        if ($totalDeleted === 0) {
            $this->info('No duplicates found!');
        } else {
            $this->info("Duplicate cleaning completed! Deleted {$totalDeleted} duplicate records.");
        }
    }
} 