<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        // Statistics
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'users_with_2fa' => User::where('two_factor_enabled', true)->count(),
        ];

        return view('admin.users.index', compact('stats'));
    }

    /**
     * Get users data for DataTables
     */
    public function getData(Request $request)
    {
        $query = User::query();

        return DataTables::of($query)
            ->addColumn('checkbox', function ($user) {
                return '<input type="checkbox" name="user_ids[]" value="' . $user->id . '" class="form-check-input user-checkbox">';
            })
            ->addColumn('user', function ($user) {
                $avatar = $user->adminlte_image();
                $youBadge = $user->id == auth()->id() ? '<span class="badge badge-primary badge-sm ml-1">You</span>' : '';
                return '
                    <div class="d-flex align-items-center">
                        <div class="user-avatar mr-2">
                            <img src="' . $avatar . '" alt="' . $user->name . '" class="img-circle elevation-2" style="width: 30px; height: 30px;">
                        </div>
                        <div>
                            <strong>' . e($user->name) . '</strong>
                            ' . $youBadge . '
                        </div>
                    </div>';
            })
            ->addColumn('email', function ($user) {
                $verified = $user->email_verified_at
                    ? '<i class="fas fa-check-circle text-success ml-1" title="Email Verified"></i>'
                    : '<i class="fas fa-exclamation-circle text-warning ml-1" title="Email Not Verified"></i>';
                return e($user->email) . ' ' . $verified;
            })
            ->addColumn('status', function ($user) {
                return ($user->is_active ?? true)
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';
            })
            ->addColumn('two_factor', function ($user) {
                return $user->two_factor_enabled
                    ? '<span class="badge badge-success"><i class="fas fa-shield-alt mr-1"></i>Enabled</span>'
                    : '<span class="badge badge-secondary">Disabled</span>';
            })
            ->addColumn('last_login', function ($user) {
                return $user->last_login_at
                    ? '<span title="' . $user->last_login_at->format('M d, Y H:i:s') . '">' . $user->last_login_at->diffForHumans() . '</span>'
                    : '<span class="text-muted">Never</span>';
            })
            ->addColumn('login_count', function ($user) {
                return '<span class="badge badge-info">' . ($user->login_count ?? 0) . '</span>';
            })
            ->addColumn('created', function ($user) {
                return '<span title="' . $user->created_at->format('M d, Y H:i:s') . '">' . $user->created_at->diffForHumans() . '</span>';
            })
            ->addColumn('actions', function ($user) {
                $actions = '
                    <div class="btn-group">
                        <a href="' . route('admin.users.show', $user) . '" class="btn btn-info btn-sm" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="' . route('admin.users.edit', $user) . '" class="btn btn-primary btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" onclick="resetPassword(' . $user->id . ')">
                                    <i class="fas fa-key mr-1"></i> Reset Password
                                </a>';

                if (!$user->email_verified_at) {
                    $actions .= '
                                <a class="dropdown-item" href="#" onclick="toggleEmailVerification(' . $user->id . ')">
                                    <i class="fas fa-envelope-check mr-1"></i> Verify Email
                                </a>';
                }

                if ($user->two_factor_enabled) {
                    $actions .= '
                                <a class="dropdown-item" href="#" onclick="disableTwoFactor(' . $user->id . ')">
                                    <i class="fas fa-shield-alt mr-1"></i> Disable 2FA
                                </a>';
                }

                if ($user->id != auth()->id()) {
                    $actions .= '
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#" onclick="deleteUser(' . $user->id . ')">
                                    <i class="fas fa-trash mr-1"></i> Delete User
                                </a>';
                }

                $actions .= '
                            </div>
                        </div>
                    </div>';

                return $actions;
            })
            ->rawColumns(['checkbox', 'user', 'email', 'status', 'two_factor', 'last_login', 'login_count', 'created', 'actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => $request->has('email_verified') ? now() : null,
            'is_active' => $request->has('is_active'),
            'admin_notes' => $request->admin_notes,
        ]);

        // Send welcome email if requested
        if ($request->has('send_welcome_email')) {
            // TODO: Send welcome email
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->email_verified_at = $request->has('email_verified') ?
            ($user->email_verified_at ?? now()) : null;

        // Don't allow users to deactivate themselves
        if ($user->id != auth()->user()->id) {
            $user->is_active = $request->has('is_active');
        }

        $user->admin_notes = $request->admin_notes;
        $user->save();

        // Send notification if requested
        if ($request->has('send_notification')) {
            // TODO: Send notification email
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent users from deleting themselves
        if ($user->id == auth()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.'
        ]);
    }

    /**
     * Reset user password
     */
    public function resetPassword(User $user)
    {
        $newPassword = $this->generateRandomPassword();
        $user->password = Hash::make($newPassword);
        $user->save();

        // TODO: Send email with new password

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. User will receive an email with the new password.'
        ]);
    }

    /**
     * Toggle email verification status
     */
    public function toggleEmailVerification(User $user)
    {
        $user->email_verified_at = $user->email_verified_at ? null : now();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Email verification status updated successfully.'
        ]);
    }

    /**
     * Disable two-factor authentication
     */
    public function disableTwoFactor(User $user)
    {
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Two-factor authentication disabled successfully.'
        ]);
    }

    /**
     * Handle bulk actions
     */
    public function bulkAction(Request $request)
    {
        $action = $request->bulk_action;
        $userIds = $request->user_ids ?? [];

        if (empty($userIds)) {
            return redirect()->back()->with('error', 'No users selected.');
        }

        // Prevent actions on current user
        $userIds = array_filter($userIds, function($id) {
            return $id != auth()->user()->id;
        });

        $users = User::whereIn('id', $userIds);

        switch ($action) {
            case 'reset_password':
                foreach ($users->get() as $user) {
                    $this->resetPassword($user);
                }
                $message = 'Passwords reset for selected users.';
                break;

            case 'send_verification':
                $users->whereNull('email_verified_at')->update([
                    'email_verified_at' => now()
                ]);
                $message = 'Email verification sent for selected users.';
                break;

            case 'disable_2fa':
                $users->update([
                    'two_factor_enabled' => false,
                    'two_factor_secret' => null,
                    'two_factor_recovery_codes' => null,
                ]);
                $message = 'Two-factor authentication disabled for selected users.';
                break;

            case 'delete':
                $users->delete();
                $message = 'Selected users deleted successfully.';
                break;

            default:
                return redirect()->back()->with('error', 'Invalid bulk action.');
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Generate a random password
     */
    private function generateRandomPassword($length = 12)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';

        // Ensure at least one character from each required type
        $password .= substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 1);
        $password .= substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 1);
        $password .= substr(str_shuffle('0123456789'), 0, 1);
        $password .= substr(str_shuffle('!@#$%^&*'), 0, 1);

        // Fill the rest randomly
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }

        return str_shuffle($password);
    }
}
