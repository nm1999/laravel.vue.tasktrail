<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $user = auth()->user();
        $userSettings = session('user_settings', [
            'theme' => 'light',
            'darkMode' => false,
            'fontSize' => 'medium',
        ]);

        return Inertia::render('Admin/Settings/Index', [
            'user' => $user,
            'settings' => $userSettings,
        ]);
    }

    /**
     * Update user appearance settings.
     */
    public function updateAppearance(Request $request)
    {
        $validated = $request->validate([
            'darkMode' => 'boolean',
            'theme' => 'required|string|in:light,dark,auto',
            'fontSize' => 'required|string|in:small,medium,large',
        ]);

        // Store settings in session or database as needed
        session(['user_settings' => $validated]);

        return back()->with('success', 'Appearance settings updated successfully!');
    }

    /**
     * Update user profile settings.
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'department' => 'nullable|string|max:255',
            'language' => 'nullable|string|in:en,es,fr,de',
        ]);

        auth()->user()->update([
            'firstname' => $validated['firstname'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'department' => $validated['department'] ?? null,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Update notification preferences.
     */
    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'task_reminders' => 'boolean',
            'team_updates' => 'boolean',
            'daily_digest' => 'boolean',
        ]);

        // Store notification preferences
        session(['notification_preferences' => $validated]);

        return back()->with('success', 'Notification preferences updated successfully!');
    }

    /**
     * Update security settings.
     */
    public function updateSecurity(Request $request)
    {
        $validated = $request->validate([
            'two_factor_enabled' => 'boolean',
            'login_alerts' => 'boolean',
        ]);

        // Store security settings
        session(['security_settings' => $validated]);

        return back()->with('success', 'Security settings updated successfully!');
    }
}
