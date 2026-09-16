<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingPageDoctor extends Model
{
    use HasFactory;

    protected $table = 'clinic_landing_page_doctors';

    protected $fillable = [
        'landing_page_id',
        'doctor_name',
        'email',
        'specialization',
        'consultation_fee',
        'experience',
        'photo',
        'sort_order',
    ];

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class, 'landing_page_id');
    }
}
