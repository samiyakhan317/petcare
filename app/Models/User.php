<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['Email', 'Password', 'Role'])]
#[Hidden(['Password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'User';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'Password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Customer Relationship
    |--------------------------------------------------------------------------
    */

    public function customer()
    {
        return $this->hasOne(Customer::class, 'ID', 'ID');
    }

    /*
    |--------------------------------------------------------------------------
    | Groomer Relationship
    |--------------------------------------------------------------------------
    */

    public function groomer()
    {
        return $this->hasOne(Groomer::class, 'ID', 'ID');
    }

    /*
    |--------------------------------------------------------------------------
    | Pet Relationship
    |--------------------------------------------------------------------------
    */

    public function pets()
    {
        return $this->hasMany(Pet::class, 'Customer_ID', 'ID');
    }
}