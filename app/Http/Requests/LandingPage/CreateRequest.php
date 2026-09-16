<?php

namespace App\Http\Requests\LandingPage;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clinic_name'              => 'required|string|max:255',
            'slug'                     => 'required|string|max:100|unique:clinic_landing_pages,slug|regex:/^[a-z0-9\-]+$/',
            'status'                   => 'required|in:active,inactive',
            'about_clinic'             => 'nullable|string',
            'logo'                     => 'nullable|image|mimes:png,jpg,jpeg,webp|max:10240',
            'hero_media_type'          => 'nullable|in:image,video,color',
            'hero_media'               => 'nullable|file|mimes:png,jpg,jpeg,webp,mp4,webm,mov,ogg,avi,mkv|max:204800', // up to 200MB
            'hero_overlay_opacity'     => 'nullable|numeric|min:0|max:1',
            'timings'                  => 'nullable|string|max:255',
            'address'                  => 'nullable|string',
            'services'                 => 'nullable|string',
            'clinic_rating'            => 'nullable|numeric|min:0|max:5',
            'booking_slots'            => 'nullable|string',
            'booking_start_time'       => 'nullable|string|max:10',
            'booking_end_time'         => 'nullable|string|max:10',
            'slot_interval_minutes'    => 'nullable|integer|min:5|max:120',
            'is_appointment_enabled'   => 'nullable|boolean',
            'whatsapp_number'          => 'nullable|string|max:20',
            'notification_email'       => 'nullable|email|max:255',
            'smtp_host'                => 'nullable|string|max:255',
            'smtp_port'                => 'nullable|string|max:10',
            'smtp_username'            => 'nullable|string|max:255',
            'smtp_password'            => 'nullable|string|max:255',
            'smtp_encryption'          => 'nullable|in:tls,ssl,none',
            'smtp_from_address'        => 'nullable|email|max:255',
            'smtp_from_name'           => 'nullable|string|max:255',

            // Nested — doctors
            'doctors'                  => 'nullable|array',
            'doctors.*.doctor_name'    => 'required_with:doctors.*|string|max:255',
            'doctors.*.email'          => 'nullable|email|max:255',
            'doctors.*.specialization' => 'nullable|string|max:255',
            'doctors.*.consultation_fee' => 'nullable|string|max:50',
            'doctors.*.experience'     => 'nullable|string|max:100',
            'doctors.*.photo'          => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',

            // Nested — testimonials
            'testimonials'             => 'nullable|array',
            'testimonials.*.patient_name' => 'required_with:testimonials.*|string|max:255',
            'testimonials.*.rating'    => 'nullable|integer|min:1|max:5',
            'testimonials.*.story'     => 'nullable|string',

            // Gallery images
            'gallery_images'           => 'nullable|array',
            'gallery_images.*'         => 'nullable|image|mimes:png,jpg,jpeg,webp|max:3072',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex'  => 'Slug may only contain lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already taken. Please choose a different one.',
        ];
    }
}
