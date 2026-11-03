<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
        $validated = $request->validate([
            'account_type'          => 'required|in:organization,employee',
            'organization_name'     => 'required_if:account_type,organization|nullable|string|max:255',
            'organization_id'       => 'required_if:account_type,employee|nullable|string|exists:workspaces,public_id',
            'firstname'             => 'required|string|max:255',
            'surname'               => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'department'            => 'nullable|string|max:255',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        [$user, $role] = DB::transaction(function () use ($validated) {
            $workspace = null;
            $role = 'employee';

            if ($validated['account_type'] === 'organization') {
                $role = 'admin';
            } else {
                $workspace = Workspace::where('public_id', $validated['organization_id'])->firstOrFail();
            }

            $user = User::create([
                'firstname'  => $validated['firstname'],
                'surname'    => $validated['surname'],
                'email'      => $validated['email'],
                'department' => $validated['department'] ?? null,
                'password'   => Hash::make($validated['password']),
            ]);

            if ($role === 'admin') {
                $workspace = $user->workspaces()->create([
                    'name' => $validated['organization_name'],
                    'slug' => Str::slug($validated['organization_name']).'-'.Str::lower(Str::random(8)),
                    'public_id' => (string) Str::uuid(),
                ]);
            }

            $user->workspace()->associate($workspace);
            $user->save();
            UserRole::create([
                'user_id' => $user->id,
                'role' => $role,
            ]);

            return [$user, $role];
        });

        if(Auth::user()){
            return redirect('/admin/employees')->with('success',"User created");
        }else{
            Auth::login($user);
            $request->session()->regenerate();
            return redirect($role === 'admin' ? '/admin/dashboard' : '/employee/dashboard');
        }
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
                return redirect()->intended('/employee/dashboard');
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
