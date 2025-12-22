<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourBooking extends Model
{
    use HasFactory;

    protected $table = 'tour_bookings';

    protected $fillable = [
        'user_id',
        'listing_key',
        'transaction_type',
        'date',
        'slot_booking_id',
        'name',
        'email',
        'phone',
        'message',
        'consent'
    ];

    protected $casts = [
        'date' => 'date',
        'consent' => 'boolean',
    ];

    public function slotBooking()
    {
        return $this->belongsTo(SlotBooking::class, 'slot_booking_id');
    }
}
