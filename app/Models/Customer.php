<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'Customer';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'First_name',
        'Last_name',
        'Address',
        'Loyalty_Points',
    ];


    /*
    |--------------------------------------------------------------------------
    | Customer belongs to User
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'ID',
            'ID'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Customer has many Pets
    |--------------------------------------------------------------------------
    */

    public function pets()
    {
        return $this->hasMany(
            Pet::class,
            'Customer_ID',
            'ID'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Customer has many Phone Numbers
    |--------------------------------------------------------------------------
    */

    public function phoneNumbers()
    {
        return $this->hasMany(
            CustomerPhone::class,
            'Customer_ID',
            'ID'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Customer has many Loyalty Transactions
    |--------------------------------------------------------------------------
    */

    public function loyaltyTransactions()
    {
        return $this->hasMany(
            LoyaltyTransaction::class,
            'Customer_ID',
            'ID'
        );
    }
}