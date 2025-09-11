<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MonitorHttpErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:http-errors {--hours=24 : Number of hours to look back} {--type=all : Type of errors to show (all, api, network, timeout)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor HTTP errors from Laravel logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $type = $this->option('type');
        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            $this->error('Log file not found: ' . $logPath);
            return 1;
        }

        $this->info("Monitoring HTTP errors from the last {$hours} hours...");
        $this->newLine();

        $logContent = File::get($logPath);
        $lines = explode("\n", $logContent);
        
        $cutoffTime = now()->subHours($hours);
        $errors = [];

        foreach ($lines as $line) {
            if (empty($line)) continue;

            // Parse log timestamp
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                $logTime = \Carbon\Carbon::parse($matches[1]);
                
                if ($logTime->lt($cutoffTime)) {
                    continue;
                }

                // Check for HTTP-related errors
                if (str_contains($line, 'ERROR') && $this->isHttpError($line, $type)) {
                    $errors[] = [
                        'time' => $logTime,
                        'message' => $this->extractErrorMessage($line),
                        'type' => $this->determineErrorType($line)
                    ];
                }
            }
        }

        if (empty($errors)) {
            $this->info('No HTTP errors found in the specified time period.');
            return 0;
        }

        // Group errors by type
        $groupedErrors = collect($errors)->groupBy('type');

        foreach ($groupedErrors as $errorType => $typeErrors) {
            $this->warn("=== {$errorType} Errors (" . count($typeErrors) . ") ===");
            
            foreach ($typeErrors as $error) {
                $this->line("Time: " . $error['time']->format('Y-m-d H:i:s'));
                $this->line("Message: " . $error['message']);
                $this->newLine();
            }
        }

        $this->info("Total HTTP errors found: " . count($errors));
        return 0;
    }

    private function isHttpError($line, $type)
    {
        $httpKeywords = [
            'DB API', 'HTTP', 'request failed', 'Network error', 'timeout',
            'connection', 'curl', 'GuzzleHttp', 'ClientException'
        ];

        if ($type === 'all') {
            return collect($httpKeywords)->contains(function ($keyword) use ($line) {
                return stripos($line, $keyword) !== false;
            });
        }

        if ($type === 'api') {
            return stripos($line, 'DB API') !== false;
        }

        if ($type === 'network') {
            return stripos($line, 'Network error') !== false || stripos($line, 'connection') !== false;
        }

        if ($type === 'timeout') {
            return stripos($line, 'timeout') !== false;
        }

        return false;
    }

    private function extractErrorMessage($line)
    {
        // Extract the actual error message from the log line
        if (preg_match('/\] local\.ERROR: (.+)$/', $line, $matches)) {
            return $matches[1];
        }

        return substr($line, 0, 200) . (strlen($line) > 200 ? '...' : '');
    }

    private function determineErrorType($line)
    {
        if (stripos($line, 'DB API') !== false) {
            return 'API Request';
        }

        if (stripos($line, 'Network error') !== false) {
            return 'Network';
        }

        if (stripos($line, 'timeout') !== false) {
            return 'Timeout';
        }

        if (stripos($line, 'connection') !== false) {
            return 'Connection';
        }

        return 'HTTP';
    }
} 