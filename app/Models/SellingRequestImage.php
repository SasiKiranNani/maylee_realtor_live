<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellingRequestImage extends Model
{
    protected $fillable = ['selling_request_id', 'image_path'];

    public function sellingRequest()
    {
        return $this->belongsTo(SellingRequest::class);
    }
}
