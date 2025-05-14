<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    public function index()
    {
        return inertia('Tracking');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'time' => 'required|integer|min:0',
        ]);


        \Log::info('Time tracked:', ['time' => $validated['time']]);

        return response()->json(['message' => 'Time saved successfully']);
    }
    
    
}
