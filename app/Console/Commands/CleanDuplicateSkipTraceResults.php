<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SkipTraceResult;
use Illuminate\Support\Facades\DB;

class CleanDuplicateSkipTraceResults extends Command
{
    protected $signature = 'skip-trace:clean-duplicates';
    protected $description = 'Clean duplicate skip trace results (keep only first occurrence of each investor_identifier)';

    public function handle()
    {
        $this->info('Cleaning duplicate skip trace results...');
        
        // Find duplicates by investor_identifier
        $duplicates = DB::table('skip_trace_results')
            ->select('investor_identifier', DB::raw('COUNT(*) as duplicate_count'))
            ->groupBy('investor_identifier')
            ->havingRaw('COUNT(*) > 1')
            ->get();
        
        $totalDeleted = 0;
        
        foreach ($duplicates as $duplicate) {
            $this->info("Found {$duplicate->duplicate_count} records for investor '{$duplicate->investor_identifier}'");
            
            // Get all records for this investor_identifier, ordered by ID (keep the first one)
            $records = SkipTraceResult::where('investor_identifier', $duplicate->investor_identifier)
                ->orderBy('id')
                ->get();
            
            // Keep the first one, delete the rest
            for ($i = 1; $i < count($records); $i++) {
                $records[$i]->delete();
                $this->info("Deleted duplicate skip trace record ID: {$records[$i]->id}");
                $totalDeleted++;
            }
        }
        
        if ($totalDeleted === 0) {
            $this->info('No duplicate skip trace results found!');
        } else {
            $this->info("Duplicate cleaning completed! Deleted {$totalDeleted} duplicate skip trace records.");
        }
    }
} 