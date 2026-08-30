<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment_Service extends Model
{
    protected $table = 'Appointment_Service';

    public $timestamps = false;

    protected $fillable = [
        'Appointment_ID',
        'Service_ID',
    ];
}