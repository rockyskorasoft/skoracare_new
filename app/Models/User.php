<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\CommonTrait;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['first_name', 'last_name', 'email', 'password', 'profile_pic', 'date_of_birth', 'phone_no', 'status', 'address', 'qualification', 'registration_number', 'package_id', 'max_clinics', 'max_users', 'created_by'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, CommonTrait, SoftDeletes, LogsActivity {
        HasRoles::hasPermissionTo as traitHasPermissionTo;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     *  Log Activity
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'id',
                'name',
                'email',
                'phone_no',
                'status',
            ])->useLogName('User');
    }

    /**
     * Accessor for the "name" attribute.
     *
     * This method combines the first_name and last_name attributes,
     * trims any extra whitespace, and returns the full name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return trim(" {$this->first_name} {$this->last_name}");
    }

    /**
     * Get the clinics for the doctor.
     */
    public function clinics(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Clinic::class, 'doctor_id');
    }

    /**
     * Get the clinics assigned to this user / staff member.
     */
    public function assignedClinics(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Clinic::class, 'clinic_user', 'user_id', 'clinic_id')->withTimestamps();
    }

    /**
     * Check if user's subscription package allows access to a specific feature.
     *
     * @param string $feature
     * @return bool
     */
    public function hasPackageFeature(string $feature): bool
    {
        $package = $this->package_name ?: 'Basic';

        $packageFeatures = [
            'Basic' => ['appointment', 'clinic'],
            'Gold' => ['appointment', 'clinic', 'ask-skoracare', 'patients', 'follow-up'],
            'Platinum' => ['appointment', 'clinic', 'ask-skoracare', 'patients', 'follow-up', 'pharmacy', 'analytics', 'messages'],
        ];

        $allowed = $packageFeatures[$package] ?? [];

        return in_array($feature, $allowed);
    }

    /**
     * Check if user has both the permission and package feature access for a sidebar item.
     *
     * @param string $feature
     * @param string $permission
     * @return bool
     */
    public function hasSidebarAccess(string $feature, string $permission): bool
    {
        // Admin and Super Admin roles override all package/permission restrictions on doctor panel
        if ($this->hasRole(config('constants.super_admin_role_name')) || $this->hasRole(config('constants.admin_role_name'))) {
            return true;
        }

        return $this->can($permission);
    }

    /**
     * Override hasPermissionTo to check both Spatie roles/direct permissions and Package permissions.
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        // 1. Check direct or role permissions (Spatie standard)
        try {
            if ($this->traitHasPermissionTo($permission, $guardName)) {
                return true;
            }
        } catch (\Exception $e) {
            // Ignore Spatie exceptions (e.g. permission doesn't exist in DB) so we can check packages
        }

        // 2. Check if the assigned package has this permission
        $permissionName = $permission instanceof \Spatie\Permission\Contracts\Permission 
            ? $permission->name 
            : $permission;

        if ($this->package && $this->package->permissions()->where('name', $permissionName)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Get the subscription package for the doctor.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    /**
     * Get the users/staff created by this doctor.
     */
    public function createdUsers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class, 'created_by');
    }

    /**
     * Get the doctor/parent user who created this staff member.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if doctor can create another clinic based on assigned limit or package limit.
     */
    public function canCreateClinic(): bool
    {
        if ($this->hasRole(config('constants.super_admin_role_name')) || $this->hasRole(config('constants.admin_role_name'))) {
            return true;
        }

        // 1. Check custom assigned limit if set directly on user
        if (isset($this->max_clinics) && $this->max_clinics !== null) {
            if ((int)$this->max_clinics === -1) {
                return true;
            }
            return $this->clinics()->count() < (int)$this->max_clinics;
        }

        // 2. Check active package limit
        $package = $this->package;
        if (!$package) {
            // Allow 1 clinic if no package is assigned
            return $this->clinics()->count() < 1;
        }

        if ((int)$package->clinic_limit === -1) {
            return true;
        }

        return $this->clinics()->count() < (int)$package->clinic_limit;
    }

    /**
     * Check if doctor can create another user/staff based on assigned limit or package limit.
     */
    public function canCreateUser(): bool
    {
        if ($this->hasRole(config('constants.super_admin_role_name')) || $this->hasRole(config('constants.admin_role_name'))) {
            return true;
        }

        // 1. Check custom assigned limit if set on user
        if (isset($this->max_users) && $this->max_users !== null) {
            if ((int)$this->max_users === -1) {
                return true;
            }
            return $this->createdUsers()->count() < (int)$this->max_users;
        }

        // 2. Check Package limit
        $package = $this->package;
        if (!$package) {
            return $this->createdUsers()->count() < 1;
        }

        if ((int)$package->user_limit === -1) {
            return true;
        }

        return $this->createdUsers()->count() < (int)$package->user_limit;
    }
}
