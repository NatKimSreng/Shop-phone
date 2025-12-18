<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Authenticate user credentials.
     */
    public function authenticate(Request $req)
    {
        $validated = $req->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $validated;
        $remember = $req->has('remember') ? true : false;
        
        if (auth()->attempt($credentials, $remember)) {
            // regenerate session
            $req->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirect admin users to admin dashboard
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Welcome back, ' . $user->name . '!');
            }
            
            // Redirect regular users to home
            return redirect('/')->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials. Please try again.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form.
     */
    public function register()
    {
        return view('auth.register');
    }

    /**
     * Store a new user.
     */
    public function store(Request $req)
    {
        $validated = $req->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect('/login')->with('success', 'Account created successfully. Please login.');
    }

    /**
     * Logout user.
     */
    // public function logout(Request $req)
    // {
    //     auth()->logout();

    //     $req->session()->invalidate();
    //     $req->session()->regenerateToken();

    //     return redirect('/login')->with('success', 'You have logged out successfully.');
    // }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully.');
    }
}
