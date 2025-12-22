<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';

    protected $fillable = [
        'city',
        'description',
        'latitude',
        'longitude',
        'status',
        'is_home_active',
        'is_neighbourhood_active',
        'image',
    ];

    public $casts = [
        'status' => 'boolean',
        'is_home_active' => 'boolean',
        'is_neighbourhood_active' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'city';
    }
}
