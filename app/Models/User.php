<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'email_verified_at',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'last_login_at',
        'login_count',
        'active_sessions',
        'admin_notes',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'login_count' => 'integer',
        'active_sessions' => 'integer',
        'two_factor_recovery_codes' => 'array',
    ];

    /**
     * AdminLTE: Get the URL to the user's profile photo
     */
    public function adminlte_image()
    {
        return 'https://picsum.photos/300/300';
    }

    /**
     * AdminLTE: Get the user's full name
     */
    public function adminlte_desc()
    {
        return $this->email;
    }

    /**
     * AdminLTE: Get the URL to the user's profile
     */
    public function adminlte_profile_url()
    {
        return route('account.dashboard');
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->is_active ?? true;
    }

    /**
     * Check if user has verified email
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Get user's role for display
     */
    public function getRoleAttribute()
    {
        return $this->id === 1 ? 'Administrator' : 'User';
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for users with verified email
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope for users with 2FA enabled
     */
    public function scopeTwoFactorEnabled($query)
    {
        return $query->where('two_factor_enabled', true);
    }
}
