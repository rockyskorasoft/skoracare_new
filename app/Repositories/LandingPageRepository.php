<?php

namespace App\Repositories;

use App\Models\LandingPage;

class LandingPageRepository extends BaseRepository
{
    public function __construct(LandingPage $model)
    {
        parent::__construct($model);
    }

    /**
     * Extract scalar landing page fields from the request.
     */
    public function getDataFromRequest($request): array
    {
        $data = $request->only([
            'clinic_name',
            'slug',
            'about_clinic',
            'hero_media_type',
            'hero_overlay_opacity',
            'timings',
            'address',
            'services',
            'clinic_rating',
            'booking_slots',
            'booking_start_time',
            'booking_end_time',
            'slot_interval_minutes',
            'is_appointment_enabled',
            'whatsapp_number',
            'notification_email',
            'smtp_host',
            'smtp_port',
            'smtp_username',
            'smtp_password',
            'smtp_encryption',
            'smtp_from_address',
            'smtp_from_name',
            'status',
        ]);

        // Clean plain-text fields so no unwanted HTML tags get stored
        if (isset($data['services'])) {
            $cleaned = preg_replace('/<\s*(?:br|p|li)[^>]*>/i', "\n", $data['services']);
            $cleaned = strip_tags(html_entity_decode($cleaned, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $lines = array_filter(array_map('trim', explode("\n", $cleaned)));
            $data['services'] = implode("\n", $lines);
        }

        if (isset($data['address'])) {
            $data['address'] = trim(strip_tags(html_entity_decode($data['address'], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        }

        if (isset($data['timings'])) {
            $data['timings'] = trim(strip_tags(html_entity_decode($data['timings'], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        }

        if (isset($data['booking_slots'])) {
            $cleanedSlots = preg_replace('/<\s*(?:br|p|li)[^>]*>/i', "\n", $data['booking_slots']);
            $cleanedSlots = strip_tags(html_entity_decode($cleanedSlots, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $slotLines = array_filter(array_map('trim', explode("\n", $cleanedSlots)));
            $data['booking_slots'] = implode("\n", $slotLines);
        }

        return $data;
    }
}
