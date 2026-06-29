<?php

namespace App\Enums;

/**
 * UserType Enum
 *
 * Represents the type of a self-registering user.
 * Follows the same pattern as CommonStatus.
 * Used during signup to assign the correct role automatically.
 */
enum UserType: string
{
    case DOCTOR  = 'doctor';
    case PATIENT = 'patient';

    /**
     * Human-readable label (uses lang/en/labels.php).
     */
    public function label(): string
    {
        return __('labels.user_type_' . $this->value);
    }

    /**
     * Returns all cases as [{id, label}] array for select dropdowns.
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn($case) => [
                'id'    => $case->value,
                'label' => $case->label(),
            ])
            ->toArray();
    }
}
