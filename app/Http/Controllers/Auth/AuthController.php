<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return Inertia::render('Home');
    }

    /**
     * Show the registration form.
     */
    public function showRegisterForm()
    {
        return Inertia::render('Auth/SignUp');
    }

    /**
     * Handle registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'firstname'             => 'required|string|max:255',
            'surname'               => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'department'            => 'nullable|string|max:255',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'firstname'  => $request->firstname,
            'surname'    => $request->surname,
            'email'      => $request->email,
            'department' => $request->department,
            'password'   => Hash::make($request->password),
        ]);

        UserRole::create([
            'user_id' => $user->id,
            'role'    => "employee",
        ]);

        // if(Auth::user()){
        //     return response()->json([
        //         'message'=> "User created",
        //         'success'=> true
        //     ]);
        // }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/employee/dashboard');
    }

    /**
     * Handle login attempt.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember', false);

        // Attempt to authenticate the user
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check user role and redirect accordingly
            $role = $user->roles()->first()?->role ?? 'employee';

            if ($role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } else {
                return redirect()->intended('/admin/dashboard');
            }
        }else{
            return Inertia::render('Home', [
                'errors' => [
                    'email' => ['The provided credentials do not match our records.']],
            ]);
        }

        // Authentication failed
        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Get current authenticated user.
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * Check if user is authenticated.
     */
    public function check(Request $request)
    {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::user(),
        ]);
    }
}
