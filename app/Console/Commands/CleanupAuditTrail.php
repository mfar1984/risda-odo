<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AuditTrailService;

class CleanupAuditTrail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit-trail:cleanup 
                            {--days=30 : Number of days to keep records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old audit trail records';

    /**
     * Execute the console command.
     */
    public function handle(AuditTrailService $auditTrailService): int
    {
        $days = (int) $this->option('days');

        $this->info("Cleaning up audit trail records older than {$days} days...");

        $deletedCount = $auditTrailService->cleanupOldRecords($days);

        $this->info("Deleted {$deletedCount} old audit trail records.");

        return Command::SUCCESS;
    }
}
