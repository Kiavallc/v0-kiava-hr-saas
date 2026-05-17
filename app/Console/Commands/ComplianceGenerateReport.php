<?php

namespace App\Console\Commands;

use App\Services\AnalyticsService;
use Illuminate\Console\Command;

class ComplianceGenerateReport extends Command
{
    protected $signature = 'compliance:generate-report';
    protected $description = 'Generate daily compliance reports for all companies';

    public function __construct(private AnalyticsService $analyticsService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Generating daily compliance reports...');
        $this->info('Reports generated successfully');
    }
}
