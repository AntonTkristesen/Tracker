<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use App\Models\Tracking;

class JiraController extends Controller
{
    public function trackingView()
    {
        $email = env('JIRA_EMAIL');
        $token = env('JIRA_API_TOKEN');
        $domain = env('JIRA_DOMAIN');

        // Fetch Jira projects
        $projectsResponse = Http::withBasicAuth($email, $token)
            ->get("https://{$domain}/rest/api/3/project");

        $projects = collect();

        if ($projectsResponse->successful()) {
            $projects = collect($projectsResponse->json())->map(function ($project) use ($email, $token, $domain) {
                // Fetch issues for each project
                $issuesResponse = Http::withBasicAuth($email, $token)
                    ->get("https://{$domain}/rest/api/3/search", [
                        'jql' => 'project = ' . $project['key'],
                        'fields' => 'summary,key', // Add key to get issue identifiers
                        'maxResults' => 100 // Increase the limit if needed
                    ]);

                $issues = collect();

                if ($issuesResponse->successful()) {
                    // Map issues to a more usable format
                    $issues = collect($issuesResponse->json()['issues'])->map(function ($issue) {
                        return [
                            'id' => $issue['id'],
                            'key' => $issue['key'],
                            'summary' => $issue['fields']['summary']
                        ];
                    });
                }

                // Return the project with its issues
                return [
                    'id' => $project['id'],
                    'name' => $project['name'],
                    'key' => $project['key'],
                    'issues' => $issues,
                ];
            })->values();
        }
        
        $trackings = Tracking::latest()->get();

        // Return view via Inertia
        return Inertia::render('Tracking', [
            'projects' => $projects,
            'trackings' => $trackings,
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'time' => 'required|integer|min:1',
            'project_key' => 'nullable|string',
            'task' => 'nullable|string',
            'description' => 'nullable|string|max:255',
        ]);
        
        $tracking = new Tracking();
        $tracking->user_id = auth()->id();
        $tracking->time = $validated['time'];
        $tracking->project_key = $validated['project_key'] ?? null;
        $tracking->task = $validated['task'] ?? null;
        $tracking->description = $validated['description'] ?? null;
        $tracking->save();
        
        return response()->json(['success' => true, 'tracking' => $tracking]);
    }
    
    public function getUserTrackings()
    {
        $trackings = Tracking::where('user_id', auth()->id())
                             ->latest()
                             ->get();
                             
        return response()->json(['trackings' => $trackings]);
    }
}