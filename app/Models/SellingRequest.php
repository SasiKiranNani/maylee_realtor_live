<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SellingRequest extends Model
{
    protected $fillable = [
        'sell_property_address',
        'sell_property_type',
        'sell_property_sqft',
        'sell_property_bedrooms',
        'sell_property_bathrooms',
        'sell_property_condition',
        'sell_property_relocating',
        'house_construct_year',
        'sell_property_service',
        'sell_property_mortgage_balance',
        'sell_property_user_name',
        'sell_property_user_email',
        'sell_property_user_phone',
    ];

    public function images()
    {
        return $this->hasMany(SellingRequestImage::class);
    }

    public function getFirstImageUrlAttribute()
    {
        return $this->images->first()
            ? Storage::url($this->images->first()->image_path)
            : null;
    }
}
