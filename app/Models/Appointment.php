<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pet;
use App\Models\Groomer;
use App\Models\LoyaltyTransaction;
use App\Models\Service;
use App\Models\Appointment_Service;
use App\Models\Payment;

class Appointment extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Database Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'Appointment';

    /*
    |--------------------------------------------------------------------------
    | Primary Key
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'Appointment_ID';

    /*
    |--------------------------------------------------------------------------
    | Timestamps
    |--------------------------------------------------------------------------
    */

    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Fields
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'Pet_ID',
        'Groomer_ID',
        'Appointment_Date',
        'Appointment_Time',
        'Status',
    ];


    /*
    |--------------------------------------------------------------------------
    | Appointment belongs to Pet
    |--------------------------------------------------------------------------
    */

    public function pet()
    {
        return $this->belongsTo(
            Pet::class,
            'Pet_ID',
            'Pet_ID'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Appointment belongs to Groomer
    |--------------------------------------------------------------------------
    */

    public function groomer()
    {
        return $this->belongsTo(
            Groomer::class,
            'Groomer_ID',
            'ID'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Appointment has Loyalty Transaction
    |--------------------------------------------------------------------------
    */

    public function loyaltyTransaction()
    {
        return $this->hasOne(
            LoyaltyTransaction::class,
            'Appointment_ID',
            'Appointment_ID'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Appointment has Appointment_Service records
    |--------------------------------------------------------------------------
    */

    public function appointmentServices()
    {
        return $this->hasMany(
            Appointment_Service::class,
            'Appointment_ID',
            'Appointment_ID'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Appointment belongs to many Services
    |--------------------------------------------------------------------------
    */

    public function services()
    {
        return $this->belongsToMany(
            Service::class,
            'Appointment_Service',
            'Appointment_ID',
            'Service_ID',
            'Appointment_ID',
            'Service_ID'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Appointment has one Payment
    |--------------------------------------------------------------------------
    */

    public function payment()
    {
        return $this->hasOne(
            Payment::class,
            'Appointment_ID',
            'Appointment_ID'
        );
    }
}