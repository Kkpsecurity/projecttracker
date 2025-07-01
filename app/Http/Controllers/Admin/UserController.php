<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

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
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($status === 'admin') {
                $query->where('is_admin', true);
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
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
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'phone' => ['nullable', 'string', 'max:20'],
                'bio' => ['nullable', 'string', 'max:1000'],
                'is_admin' => ['boolean'],
                'is_active' => ['boolean'],
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please correct the validation errors below.');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'bio' => $request->bio,
                'is_admin' => $request->boolean('is_admin'),
                'is_active' => $request->boolean('is_active', true),
                'email_verified' => true,
                'password_changed_at' => now(),
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User "' . $user->name . '" created successfully.');

        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create user. Please try again.');
        }
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
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'is_admin' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'bio' => $request->bio,
            'is_admin' => $request->boolean('is_admin'),
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
            $updateData['password_changed_at'] = now();
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
                        ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()
                           ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                        ->with('success', 'User deleted successfully.');
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ]);

        return response()->json(['message' => 'Password reset successfully.']);
    }

    /**
     * Toggle email verification status
     */
    public function toggleEmailVerification(User $user)
    {
        $user->update([
            'email_verified' => !$user->email_verified,
            'email_verified_at' => $user->email_verified ? null : now(),
        ]);

        $status = $user->email_verified ? 'verified' : 'unverified';
        return response()->json(['message' => "Email {$status} successfully."]);
    }

    /**
     * Disable two-factor authentication
     */
    public function disableTwoFactor(User $user)
    {
        $user->update([
            'two_factor_enabled' => false,
        ]);

        return response()->json(['message' => 'Two-factor authentication disabled successfully.']);
    }

    /**
     * Handle bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => ['required', 'in:activate,deactivate,delete'],
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userIds = $request->user_ids;
        $action = $request->action;

        if (in_array(auth()->id(), $userIds)) {
            return response()->json(['error' => 'You cannot perform bulk actions on your own account.'], 422);
        }

        switch ($action) {
            case 'activate':
                User::whereIn('id', $userIds)->update(['is_active' => true]);
                $message = 'Users activated successfully.';
                break;
            case 'deactivate':
                User::whereIn('id', $userIds)->update(['is_active' => false]);
                $message = 'Users deactivated successfully.';
                break;
            case 'delete':
                User::whereIn('id', $userIds)->delete();
                $message = 'Users deleted successfully.';
                break;
        }

        return response()->json(['message' => $message]);
    }
}
