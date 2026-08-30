<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class Pet extends Model
{
    protected $table = 'Pet';

    protected $primaryKey = 'Pet_ID';

    public $timestamps = false;

    protected $fillable = [
        'Customer_ID',
        'Name',
        'Breed',
        'Gender',
        'DOB',
        'Weight',
        'Allergies',
        'Vaccination_Status',
    ];


    /*
    |--------------------------------------------------------------------------
    | Pet belongs to Customer
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
    | Calculate Age from Date of Birth
    |--------------------------------------------------------------------------
    */

    public function getAgeAttribute()
    {
        if (empty($this->DOB)) {
            return 'N/A';
        }

        try {

            $dob = new \DateTime($this->DOB);

            $today = new \DateTime();

            $age = $today->diff($dob);

            return $age->y . ' years';

        } catch (\Exception $e) {

            return 'N/A';
        }
    }
}