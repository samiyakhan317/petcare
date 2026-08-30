<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    protected $table = 'loyalty_transaction';

    protected $primaryKey = 'Transaction_ID';

    public $timestamps = false;

    protected $fillable = [
        'Appointment_ID',
        'Customer_ID',
        'Points',
        'Transaction_Date',
        'Transaction_Type',
    ];


    /*
    |--------------------------------------------------------------------------
    | Loyalty Transaction belongs to Customer
    |--------------------------------------------------------------------------
    */

    public function customer()
    {
        return $this->belongsTo(
            Customer::class,
            'Customer_ID',
            'ID'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Loyalty Transaction belongs to Appointment
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