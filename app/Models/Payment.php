<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'Payment';

    protected $primaryKey = 'Payment_ID';

    public $timestamps = false;

    protected $fillable = [
        'Appointment_ID',
        'Payment_Status',
        'Total_Amount',
        'Payment_Method',
        'Payment_Date',
    ];

    protected $casts = [
        'Total_Amount' => 'decimal:2',
        'Payment_Date' => 'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | Payment belongs to Appointment
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