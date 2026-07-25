<?php

namespace App\Traits;

use App\Helpers\UserHelper;
use Illuminate\Database\Eloquent\Builder;

trait ClinicTrait
{
    /**
     * Boot the ClinicTrait to automatically handle clinic scoping.
     *
     * - On creating a new model instance, it automatically sets the `clinic_id`
     *   using the currently selected clinic ID from the UserHelper.
     *
     * - It also adds a global scope to ensure that all queries on the model
     *   are filtered by the `clinic_id`, effectively scoping data access
     *   to the currently selected clinic.
     */
    public static function bootClinicTrait()
    {
        static::creating(function ($model) {
            if (empty($model->clinic_id)) {
                $selectedClinicId = UserHelper::getSelectedClinicId();
                if (!empty($selectedClinicId)) {
                    $model->clinic_id = $selectedClinicId;
                }
            }
        });

        static::addGlobalScope('clinic', function (Builder $builder) {
            if (auth()->check()) {
                $selectedClinicId = UserHelper::getSelectedClinicId();
                if (!empty($selectedClinicId)) {
                    $builder->where($builder->getModel()->getTable() . '.clinic_id', $selectedClinicId);
                }
            }
        });
    }
}
