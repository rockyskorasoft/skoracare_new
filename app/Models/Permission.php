<?php

namespace App\Models;

use App\Helpers\DateHelper;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Permission as ModelsPermission;

class Permission extends ModelsPermission
{
    use NodeTrait, LogsActivity;

    protected static $logName = 'Permission';

    protected static $logAttributes = ['*'];

    protected $fillable = [
        'name',
        'guard_name',
        'parent_id',
        '_lft',
        '_rgt'
    ];

    /**
     * function to handle the activity log option
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly([
            'name',
            'created_at',
            'updated_at',
        ])->useLogName('Permission');
    }

    /**
     * function to get the attribute name value
     *
     * @return array
     */
    public function attributeNames(): array
    {
        return [
            'name' => 'Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At'
        ];
    }

    /**
     * function to get the description attribute value
     *
     * @param string $eventName
     * @return string
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        return ucfirst($eventName) . " Permission";
    }

    /**
     * function to format the updated at value
     *
     * @param mixed $value
     * @return mixed
     */
    public function getCreatedAtAttribute($value)
    {
        return DateHelper::formatDateTime($value);
    }

    /**
     * function to formatted the updated-at value
     *
     * @param mixed $value
     * @return mixed
     */
    public function getUpdatedAtAttribute($value)
    {
        return DateHelper::formatDateTime($value);
    }
}
