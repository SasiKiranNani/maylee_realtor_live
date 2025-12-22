<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TourBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $isAdmin;

    public function __construct($booking, $isAdmin = false)
    {
        $this->booking = $booking;
        $this->isAdmin = $isAdmin;
    }

    public function build()
    {
        $subject = $this->isAdmin
            ? 'New Booking Request Received'
            : 'Your Booking Request is Confirmed';

        return $this->subject($subject)
            ->markdown('emails.tour_booking')
            ->with([
                'booking' => $this->booking,
                'isAdmin' => $this->isAdmin,
                'logoUrl' => asset('frontend/assets/images/maylee-realtor-logo-v2.png'),
            ]);
    }
}
