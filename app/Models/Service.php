<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'Service';

    protected $primaryKey = 'Service_ID';

    public $timestamps = false;

    protected $fillable = [
        'Service_Name',
        'Duration',
        'Price',
        'Description',
        'status',
    ];
}