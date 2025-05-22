<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\JiraSyncService;

class SyncJiraIssues extends Command
{
    protected $signature = 'sync:jira';
    protected $description = 'Sync Jira issues and store them in the database';

    protected $jiraService;

    public function __construct(JiraSyncService $jiraService)
    {
        parent::__construct();
        $this->jiraService = $jiraService;
    }

    public function handle()
    {
        $this->info('Starting Jira Sync...');

        $this->jiraService->syncAllProjectsWithIssues();

        $this->info('Jira Sync completed!');
    }
}