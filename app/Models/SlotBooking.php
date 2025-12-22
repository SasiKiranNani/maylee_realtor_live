<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SlotBooking extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'time_slot_id', 'is_booked'];

    protected $casts = [
        'date' => 'date',
        'is_booked' => 'boolean',
    ];

    public function slot()
    {
        return $this->belongsTo(TimeSlot::class, 'time_slot_id');
    }
}
