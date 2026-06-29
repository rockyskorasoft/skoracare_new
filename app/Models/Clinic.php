<?php

namespace App\Models;

use App\Traits\CommonTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Clinic extends Model
{
    use HasFactory, LogsActivity, SoftDeletes, CommonTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'doctor_id',
        'phone_no',
        'consultation_fee',
        'address',
        'logo',
        'created_by',
        'updated_by',
    ];

    /**
     * Define Spatie Activitylog options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'id',
                'name',
                'doctor_id',
                'phone_no',
                'consultation_fee',
                'address',
            ])->useLogName('Clinic');
    }

    /**
     * Get the doctor that owns the clinic.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
