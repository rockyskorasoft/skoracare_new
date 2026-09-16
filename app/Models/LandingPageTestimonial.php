<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingPageTestimonial extends Model
{
    use HasFactory;

    protected $table = 'clinic_landing_page_testimonials';

    protected $fillable = [
        'landing_page_id',
        'patient_name',
        'rating',
        'story',
        'sort_order',
    ];

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class, 'landing_page_id');
    }
}
