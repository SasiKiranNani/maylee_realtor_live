<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>{{ $isAdmin ? 'New Booking Received' : 'Booking Confirmation' }}</title>
    <style>
        /* Basic mobile responsiveness */
        @media only screen and (max-width: 600px) {
            .inner {
                width: 100% !important;
                padding: 18px !important;
            }

            .two-column .column {
                display: block !important;
                width: 100% !important;
            }

            .btn {
                padding: 12px 18px !important;
                font-size: 16px !important;
            }
        }
    </style>
</head>

<body style="margin:0;padding:0;background:#f4f6f9;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding:30px 12px;">
                <!-- Outer container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="680"
                    style="max-width:680px;background:#ffffff;border-radius:10px;overflow:hidden;">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="background:linear-gradient(90deg,#06294b,#0b4f84);padding:26px;">
                            <img src="{{ $logoUrl }}" alt="{{ config('app.name') }} logo" width="160"
                                style="display:block;border:0;line-height:0;">
                        </td>
                    </tr>

                    <!-- Body / Intro -->
                    <tr>
                        <td class="inner" style="padding:28px 40px 18px;background:#ffffff;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td
                                        style="font-family:Arial,Helvetica,sans-serif;color:#0b3a60;font-size:20px;font-weight:700;padding-bottom:8px;">
                                        {{ $isAdmin ? 'New Booking Request Received' : '🎉 Your Booking Request is Confirmed' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="font-family:Arial,Helvetica,sans-serif;color:#556575;font-size:14px;line-height:1.6;padding-bottom:18px;">
                                        @if ($isAdmin)
                                            Hello <strong>Admin</strong>,<br>
                                            A new property booking was scheduled — details below.
                                        @else
                                            Hi <strong>{{ $booking->name }}</strong>,<br>
                                            Thanks — your booking was confirmed. Below are your appointment details.
                                        @endif
                                    </td>
                                </tr>

                                <!-- Customer info (admin) or summary (user) -->
                                @if ($isAdmin)
                                    <tr>
                                        <td>
                                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                                style="border-collapse:separate;">
                                                <tr>
                                                    <td
                                                        style="padding:10px 12px;border-left:4px solid #f1c40f;background:#fbfdff;border-radius:6px;">
                                                        <strong
                                                            style="color:#0b3a60;font-family:Arial,Helvetica,sans-serif;">Customer
                                                            Information</strong>
                                                        <div
                                                            style="padding-top:8px;color:#475b6a;font-size:14px;font-family:Arial,Helvetica,sans-serif;">
                                                            <div><strong>Name:</strong> {{ $booking->name }}</div>
                                                            <div><strong>Email:</strong> <a
                                                                    href="mailto:{{ $booking->email }}"
                                                                    style="color:#0b4f84;text-decoration:none;">{{ $booking->email }}</a>
                                                            </div>
                                                            <div><strong>Phone:</strong> {{ $booking->phone }}</div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                @endif

                                <!-- Booking details (both) -->
                                <tr>
                                    <td style="padding-top:18px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                            style="border-collapse:separate;">
                                            <tr>
                                                <td
                                                    style="padding:12px;border-left:4px solid #f1c40f;background:#fbfdff;border-radius:6px;">
                                                    <strong
                                                        style="color:#0b3a60;font-family:Arial,Helvetica,sans-serif;">Booking
                                                        Details</strong>
                                                    <div
                                                        style="padding-top:8px;color:#475b6a;font-size:14px;font-family:Arial,Helvetica,sans-serif;line-height:1.6;">
                                                        <div><strong>Date:</strong>
                                                            {{ \Carbon\Carbon::parse($booking->date)->format('l, F j, Y') }}
                                                        </div>
                                                        
                                                        @php
                                                            $start = $booking->slotBooking->slot->start_time ?? null;
                                                            $end = $booking->slotBooking->slot->end_time ?? null;
                                                        @endphp

                                                        <div>
                                                            <strong>Time Slot:</strong>
                                                            {{ $start ? \Carbon\Carbon::parse($start)->format('g:i A') : 'N/A' }}
                                                            -
                                                            {{ $end ? \Carbon\Carbon::parse($end)->format('g:i A') : 'N/A' }}
                                                        </div>

                                                        <div><strong>Transaction Type:</strong>
                                                            {{ ucfirst($booking->transaction_type) }}</div>
                                                        <div><strong>Reference:</strong> {{ $booking->listing_key }}
                                                        </div>
                                                        @if ($booking->message)
                                                            <div><strong>Message:</strong> {{ $booking->message }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Action button (Admin only) -->
                                @if ($isAdmin)
                                    <tr>
                                        <td align="center" style="padding-top:20px;">
                                            <a href="{{ url('/admin/tour-bookings') }}" class="btn"
                                                style="display:inline-block;background:#0b4f84;color:#ffffff;text-decoration:none;padding:12px 22px;border-radius:6px;font-weight:700;font-family:Arial,Helvetica,sans-serif;">View
                                                in Admin Panel</a>
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <td
                                        style="padding-top:24px;color:#556575;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.6;">
                                        @if ($isAdmin)
                                            Please confirm the booking in the admin panel when you're ready.
                                        @else
                                            We look forward to meeting you — please arrive 10 minutes early.
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="padding-top:28px;font-size:14px;color:#556575;font-family:Arial,Helvetica,sans-serif;">
                                        Thanks,<br>
                                        <strong style="color:#0b3a60;">{{ config('app.name') }} Team</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background:#0b4f84;padding:14px 20px;">
                            <table role="presentation" width="100%">
                                <tr>
                                    <td
                                        style="font-family:Arial,Helvetica,sans-serif;color:#cfe6ff;font-size:12px;text-align:center;">
                                        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                                        <a href="{{ config('app.url') }}"
                                            style="color:#f6d365;text-decoration:none;">{{ config('app.url') }}</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table><!-- /outer container -->
            </td>
        </tr>
    </table>
</body>

</html>
