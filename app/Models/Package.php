<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = [
        'name',
        'clinic_limit',
        'user_limit',
    ];

    /**
     * The permissions associated with this package.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'package_permissions',
            'package_id',
            'permission_id'
        );
    }

    /**
     * The users assigned to this package.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'package_id');
    }
}
