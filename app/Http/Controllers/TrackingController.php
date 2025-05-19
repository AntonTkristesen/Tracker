<?php

namespace App\Http\Controllers;

use App\Models\Tracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    public function index()
    {
        $trackings = Auth::check() ? Auth::user()->trackings()->latest()->get() : [];
        
        return inertia('Tracking', [
            'trackings' => $trackings
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'time' => 'required|integer|min:0',
            'description' => 'nullable|string|max:255',
            'project_key' => 'nullable|string',
            'task' => 'nullable|string',
        ]);

        // Create a new tracking record
        $tracking = new Tracking();
        $tracking->user_id = Auth::id();
        $tracking->time = $validated['time'];
        $tracking->project_key = $request->project_key ?? null;
        $tracking->task = $request->task ?? null;
        $tracking->description = $request->description ?? null;
        $tracking->save();


        return response()->json([
            'message' => 'Time saved successfully',
            'tracking' => $tracking
        ]);
    }
    
    public function getUserTrackings()
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        $trackings = Auth::user()->trackings()->latest()->get();
        
        return response()->json([
            'trackings' => $trackings
        ]);
    }
}