<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect('/dashboard.php');
        }

        return view('auth.login', ['pageTitle' => 'Login']);
    }

    public function login(Request $request): RedirectResponse
    {
        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return back()->with('error', 'Invalid email or password.')->withInput(['email' => $email]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard.php');
    }

    public function showRegister(): View
    {
        return view('auth.register', ['pageTitle' => 'Register']);
    }

    public function register(Request $request): RedirectResponse
    {
        $name = trim((string) $request->input('name', ''));
        $email = trim((string) $request->input('email', ''));
        $role = (string) $request->input('role', 'student');
        $password = (string) $request->input('password', '');

        if (!$name || !$email || strlen($password) < 8 || !in_array($role, ['student', 'faculty'], true)) {
            return back()->with('error', 'Enter valid details. Password must be at least 8 characters.')->withInput();
        }

        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'password' => Hash::make($password),
            ]);
        } catch (\Throwable) {
            return back()->with('error', 'That email is already registered.')->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard.php');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login.php');
    }
}
