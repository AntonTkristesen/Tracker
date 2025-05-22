<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Tracking;
use App\Services\JiraSyncService;
use App\Models\JiraIssue;

class JiraController extends Controller
{
    protected $jira;

    public function __construct(JiraSyncService $jira)
    {
        $this->jira = $jira;
    }

    public function trackingView()
    {
        $projects = JiraIssue::all()
            ->groupBy('project_key')
            ->map(function ($issues, $key) {
                return [
                    'key' => $key,
                    'name' => $issues->first()->project_key, // Optional: Use separate projects table
                    'id' => $issues->first()->project_id,
                    'issues' => $issues->map(function ($issue) {
                        return [
                            'id' => $issue->issue_id,
                            'key' => $issue->issue_key,
                            'summary' => $issue->summary,
                        ];
                    })->values()
                ];
            })->values();
    
        $trackings = Tracking::latest()->get();
    
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
