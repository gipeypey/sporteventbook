<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #f97316;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #f97316;
            margin: 0;
            font-size: 24px;
        }
        .booking-details {
            background-color: #f9fafb;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #6b7280;
        }
        .detail-value {
            color: #111827;
        }
        .cta-button {
            display: inline-block;
            background-color: #f97316;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            margin-top: 20px;
            font-weight: 600;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            background-color: #fef3c7;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Booking Confirmed!</h1>
            <p>Thank you for your booking</p>
        </div>

        <p>Hi <strong>{{ $booking->name }}</strong>,</p>

        <p>Your booking for the following event has been confirmed:</p>

        <div class="booking-details">
            <div class="detail-row">
                <span class="detail-label">Event</span>
                <span class="detail-value">{{ $booking->event->title }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date</span>
                <span class="detail-value">{{ $booking->event->date->format('d F Y, H:i') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Venue</span>
                <span class="detail-value">{{ $booking->event->venue->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Booking Code</span>
                <span class="detail-value"><strong>{{ $booking->code }}</strong></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Status</span>
                <span class="status-badge">{{ $booking->payment_status?->label() ?? ucfirst($booking->payment_status) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Amount</span>
                <span class="detail-value">Rp {{ number_format($booking->total, 0, ',', '.') }}</span>
            </div>
            @if($booking->discount > 0)
            <div class="detail-row">
                <span class="detail-label">Discount</span>
                <span class="detail-value" style="color: #10b981;">- Rp {{ number_format($booking->discount, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>

        <p>Please complete your payment to secure your spot. You will receive your ticket once payment is confirmed.</p>

        <div style="text-align: center;">
            <a href="{{ route('bookings.ticket', $booking->code) }}" class="cta-button">View Your Booking</a>
        </div>

        <div class="footer">
            <p>If you have any questions, please don't hesitate to contact us.</p>
            <p>&copy; {{ date('Y') }} SportEventBook. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
