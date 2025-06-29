<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'last_login_at',
        'login_count',
        // Admin fields
        'is_admin',
        'is_active',
        'avatar',
        'phone',
        'bio',
        'preferences',
        'email_verified',
        'two_factor_enabled',
        'password_changed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'login_count' => 'integer',
            // Admin field casts
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'preferences' => 'array',
            'email_verified' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'password_changed_at' => 'datetime',
        ];
    }

    /**
     * AdminLTE required methods
     */

    /**
     * Get the profile URL for AdminLTE
     */
    public function adminlte_profile_url()
    {
        return route('account.dashboard');
    }

    /**
     * Get the profile image URL for AdminLTE
     */
    public function adminlte_image()
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the user description for AdminLTE
     */
    public function adminlte_desc()
    {
        return 'KKP Security Team Member';
    }

    /**
     * Get the full name for AdminLTE
     */
    public function adminlte_full_name()
    {
        return $this->name;
    }
}
