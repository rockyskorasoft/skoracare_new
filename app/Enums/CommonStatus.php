<?php

namespace App\Enums;

enum CommonStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    /**
     * Get the human-readable label for the enum value.
     *
     * @return string
     */
    public function label(): string
    {
        return __('labels.' . $this->value);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn($case) => [
                'id' => $case->value,
                'label' => $case->label(),
            ])
            ->toArray();
    }
}