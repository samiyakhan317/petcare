<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPhone extends Model
{
    protected $table = 'Customer_Phone';

    protected $primaryKey = 'Phone_ID';

    public $timestamps = false;

    protected $fillable = [
        'Phone_ID',
        'Customer_ID',
        'Phone_Number',
    ];


    /*
    |--------------------------------------------------------------------------
    | Phone belongs to Customer
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
}