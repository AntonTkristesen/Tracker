<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request) {

        // Validate
        $fields = $request->validate([
            'avatar' => ['file', 'nullable', 'max:2048'],
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed'],
        ]);

        if ($request->hasFile('avatar')) {
            $fields['avatar'] = Storage::disk('public')->put('avatars', $request->avatar);
        }

       $user = User::create($fields);

        Auth::login($user);

        return redirect()->route('home');

    }
    
    public function login(Request $request) {

        $fields = $request->validate([

            'email' => ['required', 'email'],

            'password' => ['required'],

        ]);

        

        if (Auth::attempt($fields, $request->remember)) {

            $request->session()->regenerate();

 

            return redirect()->intended('dashboard');

        }

 

        return back()->withErrors([

            'email' => 'The provided credentials do not match our records.',

        ])->onlyInput('email');

    }



    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
    
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect('/');
    }

    public function update(Request $request): RedirectResponse
    {
        Log:info('update user', $request->all());
        $user = Auth::user();

        $fields = $request->validate([
            'avatar' => ['file', 'nullable', 'max:2048'],
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        if ($request->hasFile('avatar')) {
            Storage::disk('public')->delete($user->avatar);
            $fields['avatar'] = Storage::disk('public')->put('avatars', $request->avatar);
        }

        $user->update($fields);

        return redirect()->route('home');
    }   
}

