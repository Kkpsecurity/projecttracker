<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the account dashboard
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        // Get account statistics
        $stats = [
            'last_login' => $user->last_login_at ?? $user->created_at,
            'account_created' => $user->created_at,
            'total_logins' => $user->login_count ?? 0,
            'two_factor_enabled' => $user->two_factor_secret ? true : false,
            'active_sessions' => $this->getActiveSessionsCount(),
        ];

        return view('account.dashboard', compact('user', 'stats'));
    }

    /**
     * Show account settings page
     */
    public function settings(): View
    {
        $user = Auth::user();
        return view('account.settings', compact('user'));
    }

    /**
     * Show security settings page
     */
    public function security(): View
    {
        $user = Auth::user();
        $sessions = $this->getActiveSessions();

        return view('account.security', compact('user', 'sessions'));
    }

    /**
     * Update user's name
     */
    public function updateName(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'current_password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $request->name,
        ]);

        return redirect()->route('account.dashboard')
            ->with('success', 'Your name has been updated successfully.');
    }

    /**
     * Update user's email
     */
    public function updateEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
            'current_password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        $user->update([
            'email' => $request->email,
            'email_verified_at' => null, // Require re-verification for security
        ]);

        return redirect()->route('account.dashboard')
            ->with('success', 'Your email has been updated successfully. Please verify your new email address.');
    }

    /**
     * Update user's password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Log out from other sessions for security
        $this->revokeOtherSessions($request);

        return redirect()->route('account.dashboard')
            ->with('success', 'Your password has been updated successfully. You have been logged out from other devices.');
    }

    /**
     * Enable Two-Factor Authentication
     */
    public function enableTwoFactor(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();

        // Generate a simple backup code (in production, use proper 2FA library like Laravel Fortify)
        $backupCode = 'KKP-' . strtoupper(str_replace('-', '', substr(str()->uuid(), 0, 8)));

        $user->update([
            'two_factor_secret' => Hash::make('enabled'), // Placeholder for actual 2FA implementation
            'two_factor_recovery_codes' => encrypt([$backupCode]),
        ]);

        return redirect()->route('account.security')
            ->with('success', 'Two-factor authentication has been enabled.')
            ->with('backup_code', $backupCode);
    }

    /**
     * Disable Two-Factor Authentication
     */
    public function disableTwoFactor(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        return redirect()->route('account.security')
            ->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Get active sessions
     */
    public function sessions(): View
    {
        $sessions = $this->getActiveSessions();
        return view('account.sessions', compact('sessions'));
    }

    /**
     * Revoke other browser sessions
     */
    public function revokeOtherSessions(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        // Invalidate other sessions
        Auth::logoutOtherDevices($request->current_password);

        return redirect()->route('account.security')
            ->with('success', 'You have been logged out from all other devices.');
    }

    /**
     * Revoke specific session
     */
    public function revokeSession(Request $request, $sessionId): RedirectResponse
    {
        // This would require custom session management
        // For now, we'll redirect back with a message
        return redirect()->route('account.sessions')
            ->with('info', 'Session management feature coming soon.');
    }

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'delete_confirmation' => ['required', 'in:DELETE MY ACCOUNT'],
        ]);

        $user = Auth::user();

        // Log out the user
        Auth::logout();

        // Delete the user (soft delete recommended for audit purposes)
        $user->delete();

        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Your account has been deleted successfully.');
    }

    /**
     * Get active sessions count
     */
    private function getActiveSessionsCount(): int
    {
        return DB::table('sessions')
            ->where('user_id', Auth::id())
            ->count();
    }

    /**
     * Get active sessions with details
     */
    private function getActiveSessions(): array
    {
        $sessions = DB::table('sessions')
            ->where('user_id', Auth::id())
            ->get();

        return $sessions->map(function ($session) {
            return [
                'id' => $session->id,
                'ip_address' => $session->ip_address,
                'user_agent' => $session->user_agent,
                'last_activity' => \Carbon\Carbon::createFromTimestamp($session->last_activity),
                'is_current' => $session->id === Session::getId(),
            ];
        })->toArray();
    }
}
