<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrackingController;
use App\Models\User;


Route::inertia('/', 'Home', ['users' => User::paginate(5)])->name('home');


Route::inertia('register/', 'Auth/Register')->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::inertia('login', 'Auth/Login')->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::inertia('/dashboard', 'Dashboard')->name('dashboard');

Route::inertia('/tracking', 'Tracking')->name('tracking');
Route::post('/tracking', [TrackingController::class, 'store']);
