<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FakeProperty extends Model
{
    protected $table = null; // no table
    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'ListingKey';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ListingKey', 'City', 'UnparsedAddress', 'ListPrice',
        'PropertyType', 'PropertySubType', 'StandardStatus', 'TransactionType'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fill($attributes);
    }

    public function getKeyName()
    {
        return 'ListingKey';
    }
}
