<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Reminder</title>
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
            border-bottom: 2px solid #f59e0b;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #f59e0b;
            margin: 0;
            font-size: 24px;
        }
        .alert-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-box strong {
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
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
        .timer-box {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .timer-box .expires {
            font-size: 32px;
            font-weight: bold;
        }
        .timer-box .label {
            font-size: 14px;
            opacity: 0.9;
        }
        .cta-button {
            display: inline-block;
            background-color: #f59e0b;
            color: #ffffff;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 8px;
            margin-top: 20px;
            font-weight: 700;
            font-size: 16px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .warning-text {
            color: #dc2626;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⏰ Payment Reminder</h1>
            <p>Action required to complete your booking</p>
        </div>

        <p>Hi <strong>{{ $booking->name }}</strong>,</p>

        <p>This is a friendly reminder that your payment for the following booking is still pending:</p>

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
                <span class="detail-label">Booking Code</span>
                <span class="detail-value"><strong>{{ $booking->code }}</strong></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Amount to Pay</span>
                <span class="detail-value" style="font-size: 20px; color: #f59e0b;"><strong>Rp {{ number_format($booking->total, 0, ',', '.') }}</strong></span>
            </div>
        </div>

        @if($booking->expires_at)
        <div class="timer-box">
            <div class="expires">{{ $booking->expires_at->diffForHumans() }}</div>
            <div class="label">Time remaining to complete payment</div>
        </div>
        @endif

        <div class="alert-box">
            <strong>⚠️ Important:</strong>
            Your booking will be automatically cancelled if payment is not received before the expiry time. 
            Slots are limited and will be released to other participants.
        </div>

        <p style="text-align: center;">
            Complete your payment now to secure your spot!
        </p>

        <div style="text-align: center;">
            <a href="{{ route('bookings.ticket', $booking->code) }}" class="cta-button">Pay Now</a>
        </div>

        <div class="footer">
            <p class="warning-text">Don't wait too long - spots are filling up fast!</p>
            <p style="margin-top: 15px;">If you've already made the payment, please ignore this email.</p>
            <p>&copy; {{ date('Y') }} SportEventBook. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
