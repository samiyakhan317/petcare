<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroomingReport extends Model
{
    protected $table = 'grooming_report';

    protected $primaryKey = 'Report_ID';

    public $timestamps = false;

    protected $fillable = [
        'Appointment_ID',
        'Coat_Condition',
        'Skin_Condition',
        'Ear_Cleaning',
        'Nail_Trimming',
        'Recommendation',
        'Groomer_Notes',
        'Created_At',
    ];

    protected $casts = [
        'Created_At' => 'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | Grooming Report belongs to Appointment
    |--------------------------------------------------------------------------
    */

    public function appointment()
    {
        return $this->belongsTo(
            Appointment::class,
            'Appointment_ID',
            'Appointment_ID'
        );
    }
}