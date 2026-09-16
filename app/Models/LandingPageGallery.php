<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingPageGallery extends Model
{
    use HasFactory;

    protected $table = 'clinic_landing_page_gallery';

    protected $fillable = [
        'landing_page_id',
        'image',
        'caption',
        'sort_order',
    ];

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class, 'landing_page_id');
    }
}
