<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

class ClearExpiredCache extends Command
{
    protected $signature = 'cache:clear-expired
                            {--stats : Clear dashboard stats cache}
                            {--all : Clear all application caches}';

    protected $description = 'Clear expired or specified application caches';

    public function handle(CacheService $cacheService): int
    {
        if ($this->option('all')) {
            $cacheService->clearAll();
            $this->info('All caches cleared.');
            return Command::SUCCESS;
        }

        if ($this->option('stats')) {
            $cacheService->clearDashboardStats();
            $this->info('Dashboard stats cache cleared.');
        }

        $this->info('Cache cleanup complete.');

        return Command::SUCCESS;
    }
}
