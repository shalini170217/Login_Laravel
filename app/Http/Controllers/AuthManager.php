<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthManager extends Controller
{
    // Show login form
    function login() {
        return view('login');
    }

    // Show registration form
    function registration() {
        return view('registration');
    }

    // Handle login request
    function loginPost(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended(route('home'));
        }

        return redirect(route('login'))->with("error", "Invalid login details");
    }

    // Handle registration request
    function registrationPost(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        $data = $request->only('name', 'email', 'password');
        $data['password'] = Hash::make($request->password);

        $user = User::create($data);

        if (!$user) {
            return redirect(route('registration'))->with("error", "Registration failed, please try again.");
        }

        // Log in the user automatically after registration
        Auth::login($user);

        // Redirect to home page with success message
        return redirect(route('home'))->with("success", "Registration successful! You are now logged in.");
    }

    // Handle logout request
    function logout() {
        Session::flush();
        Auth::logout();
        return redirect(route("login"));
    }
}
