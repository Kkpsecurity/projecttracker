<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Get the post-login redirect path.
     */
    protected function redirectTo()
    {
        return '/admin/dashboard';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Ensure session is started and CSRF token is available
        if (! session()->has('_token')) {
            session()->regenerateToken();
        }

        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(\Illuminate\Http\Request $request)
    {
        // Log basic login attempt info (without sensitive debug data)
        Log::info('Login attempt', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
            'user_agent' => substr($request->userAgent(), 0, 100),
        ]);

        try {
            $this->validateLogin($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Login validation failed', [
                'email' => $request->input('email'),
                'errors' => $e->errors(),
            ]);

            return back()->withErrors($e->errors())->withInput($request->only('email', 'remember'));
        }

        // Check for too many login attempts
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            Log::warning('Login attempt blocked - too many attempts', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
            ]);

            return back()->with('error', 'Too many login attempts. Please try again later.')->withInput($request->only('email', 'remember'));
        }

        // Attempt to authenticate
        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            $this->clearLoginAttempts($request);

            Log::info('Login successful', [
                'user_id' => Auth::id(),
                'email' => $request->input('email'),
            ]);

            return $this->sendLoginResponse($request);
        }

        // Login failed - increment attempts and show error
        $this->incrementLoginAttempts($request);

        Log::warning('Login failed - invalid credentials', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
        ]);

        return back()->with('error', 'These credentials do not match our records. Please check your email and password and try again.')->withInput($request->only('email', 'remember'));
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user_email = Auth::user() ? Auth::user()->email : 'Unknown';

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        Log::info('User logged out', ['email' => $user_email]);

        return redirect()->route('admin.login')->with('status', 'You have been successfully logged out.');
    }
}
