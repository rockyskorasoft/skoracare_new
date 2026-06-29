<?php

namespace App\Traits;

use App\Models\User;
use App\Helpers\UserHelper;
use Illuminate\Database\Eloquent\Builder;

trait CommonTrait
{

    use DateFormatTrait;

    /**
     * function to save the logged in user as created-at
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            $userData = UserHelper::getLoggedInUser();
            if (empty($model->created_by) && $userData) {
                $model->created_by = $userData->id;
            }
        });

        static::updating(function ($model) {
            $userData = UserHelper::getLoggedInUser();
            if ($userData) {
                $model->updated_by = $userData->id;
            }
        });
    }

    /**
     * Get the user who last updated this record.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * Get the user who created this record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * function for getting the active scope data
     *
     * @param Builder $query
     * @param [type] $activeValue
     * @return Builder
     */
    public function scopeActiveData(Builder $query, $activeValue): Builder
    {
        return $query->where('status', $activeValue);
    }
}
