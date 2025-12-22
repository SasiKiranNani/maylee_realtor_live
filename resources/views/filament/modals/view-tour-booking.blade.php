<div
    style="font-family: 'Inter', sans-serif; color: #1f2937; background: linear-gradient(145deg, #ffffff, #f9fafb); border-radius: 14px; padding: 26px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); border: 1px solid #e5e7eb;">

    <!-- Header -->
    <div
        style="display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #e5e7eb; padding-bottom: 14px; margin-bottom: 24px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0;">Tour Booking Details</h2>
            <p style="font-size: 13px; color: #6b7280; margin-top: 4px;">
                Created on <span style="color:#2563eb;">{{ $booking->created_at?->format('M d, Y h:i A') }}</span>
            </p>
        </div>
        <span
            style="background: linear-gradient(90deg, #2563eb, #1d4ed8); color: white; font-size: 13px; font-weight: 600; border-radius: 9999px; padding: 6px 14px; box-shadow: 0 2px 6px rgba(37,99,235,0.3);">
            {{ ucfirst($booking->transaction_type) }}
        </span>
    </div>

    <!-- Info Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 18px;">
        @foreach ([
        'Name' => $booking->name,
        'Email' => $booking->email,
        'Phone' => $booking->phone,
        'Date' => $booking->date?->format('M j, Y'),
        'Time Slot' => ($booking->slot?->start_time ? \Carbon\Carbon::parse($booking->slot->start_time)->format('g:i A') : '-') . ' – ' . ($booking->slot?->end_time ? \Carbon\Carbon::parse($booking->slot->end_time)->format('g:i A') : '-'),
        'Listing Key' => $booking->listing_key,
        'Consent' => $booking->consent ? 'Yes (Consented)' : 'No (Not Given)',
    ] as $label => $value)
            <div
                style="background: rgba(243,244,246,0.7); border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px 16px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02); transition: all 0.25s ease; cursor: default;">
                <p
                    style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin: 0;">
                    {{ $label }}</p>
                <p
                    style="font-size: 15px; font-weight: 600; color: {{ $label === 'Consent' ? ($booking->consent ? '#15803d' : '#dc2626') : '#111827' }}; margin-top: 4px;">
                    {{ $value }}
                </p>
            </div>
        @endforeach
    </div>

    <!-- Message -->
    @if ($booking->message)
        <div style="margin-top: 28px; border-top: 1px solid #e5e7eb; padding-top: 18px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #111827; margin: 0 0 6px;">Message</h3>
            <div
                style="background: rgba(243,244,246,0.8); border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px 16px; line-height: 1.6;">
                <p style="font-size: 14px; color: #374151; margin: 0;">{{ $booking->message }}</p>
            </div>
        </div>
    @endif
</div>
