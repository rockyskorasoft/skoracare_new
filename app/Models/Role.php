<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role as ModelsRole;

class Role extends ModelsRole
{
    use LogsActivity;

    protected $fillable = ['name', 'guard_name'];

    protected static $logName = 'Role';

    protected static $logAttributes = ['*'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly([
            'name',
            'guard_name',
            'created_at',
            'updated_at',
        ])->useLogName('Role');
    }

    public function attributeNames()
    {
        return [
            'name' => 'Name',
            'guard_name' => 'Guard Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


    /**
     * function to get the description data
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        return ucfirst($eventName).' Role';
    }

    /**
     * function to format the updated at value
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getCreatedAtAttribute($value)
    {
        return DateHelper::formatDateTime($value);
    }

    /**
     * function to formatted the updated-at value
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getUpdatedAtAttribute($value)
    {
        return DateHelper::formatDateTime($value);
    }
}
