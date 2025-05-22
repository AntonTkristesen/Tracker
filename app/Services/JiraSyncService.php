<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\JiraIssue;

class JiraSyncService
{
    protected $email;
    protected $token;
    protected $domain;

    public function __construct()
    {
        $this->email = env('JIRA_EMAIL');
        $this->token = env('JIRA_API_TOKEN');
        $this->domain = env('JIRA_DOMAIN');
    }

    public function syncAllProjectsWithIssues()
    {
        $projectsResponse = Http::withBasicAuth($this->email, $this->token)
            ->get("https://{$this->domain}/rest/api/3/project");

        if (!$projectsResponse->successful()) {
            return;
        }

        foreach ($projectsResponse->json() as $project) {
            $this->syncIssuesForProject($project);
        }
    }

    protected function syncIssuesForProject($project)
    {
        $response = Http::withBasicAuth($this->email, $this->token)
            ->get("https://{$this->domain}/rest/api/3/search", [
                'jql' => 'project = ' . $project['key'],
                'fields' => 'summary,key',
                'maxResults' => 100,
            ]);

        if (!$response->successful()) return;

        foreach ($response->json()['issues'] as $issue) {
            JiraIssue::updateOrCreate(
                ['issue_id' => $issue['id']],
                [
                    'project_id'   => $project['id'],
                    'project_key'  => $project['key'],
                    'issue_key'    => $issue['key'],
                    'summary'      => $issue['fields']['summary'] ?? '',
                    'raw_data'     => json_encode($issue),
                ]
            );
        }
    }
}

