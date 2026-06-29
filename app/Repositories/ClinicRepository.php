<?php

namespace App\Repositories;

use App\Models\Clinic;

class ClinicRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(Clinic $model)
    {
        parent::__construct($model);
    }

    /**
     * Extract clinic fields from the request.
     */
    public function getDataFromRequest($request): array
    {
        return $request->only([
            'name',
            'doctor_id',
            'phone_no',
            'consultation_fee',
            'address',
        ]);
    }
}
