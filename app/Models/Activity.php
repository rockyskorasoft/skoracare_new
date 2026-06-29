<?php

namespace App\Models;

use App\Helpers\DateHelper;
use App\Helpers\UserHelper;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Activity as ModelsActivity;

#[Fillable(['log_name', 'description', 'subject', 'causer', 'attribute_changes', 'properties', 'batch_uuid', 'ip_address', 'created_by'])]
class Activity extends ModelsActivity
{
    /**
     * Set audit fields before creating activity log.
     */
    protected static function booted()
    {
        static::creating(function ($activity) {
            $user = UserHelper::getLoggedInUser();
            $subject = $activity->subject;

            $activity->ip_address = request()?->ip();

            $activity->created_by = $activity->created_by
                ?: ($subject->created_by ?? null)
                ?: $user?->id;
        });
    }

    /**
     * Get the user who created the activity.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get formatted change lines from activity properties.
     */
    public function changeLines(): array
    {
        $changes = [];

        foreach ($this->properties['attributes'] ?? [] as $key => $newValue) {
            $oldValue = $this->properties['old'][$key] ?? null;

            if ($oldValue !== $newValue) {
                $changes[] = sprintf(
                    "%s changed from '%s' to '%s'",
                    $key,
                    $this->displayValue($oldValue),
                    $this->displayValue($newValue)
                );
            }
        }

        return $changes;
    }

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
     * Format a value for activity display.
     */
    private function displayValue($value): string
    {
        if ($value === null || $value === '') {
            return 'N/A';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        return (string) $value;
    }
}
