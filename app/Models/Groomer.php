<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Groomer extends Model
{
    protected $table = 'Groomer';

    protected $primaryKey = 'ID';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Name',
        'Phone',
        'Experience',
        'Specialization',
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'ID',
            'ID'
        );
    }
}