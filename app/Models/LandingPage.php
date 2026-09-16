<?php

namespace App\Models;

use App\Traits\CommonTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LandingPage extends Model
{
    use HasFactory, LogsActivity, SoftDeletes, CommonTrait;

    protected $table = 'clinic_landing_pages';

    protected $fillable = [
        'created_by',
        'clinic_name',
        'slug',
        'about_clinic',
        'logo',
        'hero_media_type',
        'hero_media',
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
        'updated_by',
    ];

    protected $casts = [
        'is_appointment_enabled' => 'boolean',
        'clinic_rating'          => 'float',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id', 'clinic_name', 'slug', 'status'])
            ->useLogName('LandingPage');
    }

    /**
     * Doctors listed on this landing page.
     */
    public function doctors(): HasMany
    {
        return $this->hasMany(LandingPageDoctor::class, 'landing_page_id')->orderBy('sort_order');
    }

    /**
     * Patient testimonials / stories.
     */
    public function testimonials(): HasMany
    {
        return $this->hasMany(LandingPageTestimonial::class, 'landing_page_id')->orderBy('sort_order');
    }

    /**
     * Gallery images.
     */
    public function gallery(): HasMany
    {
        return $this->hasMany(LandingPageGallery::class, 'landing_page_id')->orderBy('sort_order');
    }

    /**
     * Creator user.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
