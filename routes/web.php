<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\JiraController;
use App\Models\User;

// Fix: Change this line to use a closure so the query only runs when the route is accessed
Route::get('/', function() {
    return inertia('Home', ['users' => User::paginate(5)]);
})->name('home');

Route::inertia('register', 'Auth/Register')->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::inertia('login', 'Auth/Login')->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
Route::post('/dashboard', [AuthController::class, 'update'])->name('dashboard.update');

Route::inertia('/tracking', 'Tracking')->name('tracking');
Route::post('/tracking', [TrackingController::class, 'store']);
Route::get('/user-trackings', [TrackingController::class, 'getUserTrackings']);


Route::get('/tracking', [JiraController::class, 'trackingView'])->name('tracking');

Route::inertia('/calendar', 'Calendar')->name('calendar');

Route::inertia('/profile', 'Profile')->name('profile');


