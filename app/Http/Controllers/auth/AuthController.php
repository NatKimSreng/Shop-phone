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
        $credentials = $req->only('email', 'password');
        $remember = $req->has('remember') ? true : false;
        if (auth()->attempt($credentials, $remember)) {
            // regenerate session
            $req->session()->regenerate();
            return redirect('/products')->with('success', 'Welcome back, ' . Auth::user()->name . '!');
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

        return redirect()->route('auth.login')->with('success', 'You have been logged out successfully.');
    }
}
