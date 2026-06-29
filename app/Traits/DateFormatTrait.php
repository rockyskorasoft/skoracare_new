<?php

namespace App\Traits;

use App\Helpers\DateHelper;

trait DateFormatTrait
{
    /**
     * function to format the updated at value
     *
     * @param mixed $value
     * @return mixed
     */
    public function getCreatedAtAttribute($value)
    {
        return DateHelper::formatDateTime($value, 'd-m-Y H:i:s');
    }

    /**
     * function to formatted the updated-at value
     *
     * @param mixed $value
     * @return mixed
     */
    public function getUpdatedAtAttribute($value)
    {
        return DateHelper::formatDateTime($value, 'd-m-Y H:i:s');
    }
}
