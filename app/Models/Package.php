<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'monthly_price',
        'yearly_price',
        'clinic_limit',
        'user_limit',
        'status',
        'is_popular',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'clinic_limit' => 'integer',
        'user_limit' => 'integer',
        'is_popular' => 'boolean',
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
