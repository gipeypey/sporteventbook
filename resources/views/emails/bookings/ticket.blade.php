<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Ticket</title>
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
            border-bottom: 2px solid #10b981;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #10b981;
            margin: 0;
            font-size: 24px;
        }
        .ticket {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 20px 0;
            color: white;
        }
        .ticket-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .ticket-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .ticket-code {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
            background-color: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 6px;
            display: inline-block;
            margin: 10px 0;
        }
        .ticket-details {
            background-color: rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }
        .ticket-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }
        .qr-section {
            text-align: center;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 8px;
            margin: 20px 0;
        }
        .qr-section img {
            max-width: 200px;
            height: auto;
        }
        .info-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background-color: #10b981;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            margin-top: 15px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎫 Your Ticket is Ready!</h1>
            <p>Payment confirmed - You're all set!</p>
        </div>

        <p>Hi <strong>{{ $booking->name }}</strong>,</p>

        <p>Great news! Your payment has been confirmed. Here's your ticket:</p>

        <div class="ticket">
            <div class="ticket-header">
                <div class="ticket-title">{{ $booking->event->title }}</div>
                <div>{{ $booking->event->date->format('d F Y, H:i') }}</div>
            </div>
            
            <div style="text-align: center;">
                <div class="ticket-code">{{ $booking->code }}</div>
            </div>

            <div class="ticket-details">
                <div class="ticket-row">
                    <span>📍 Venue</span>
                    <span>{{ $booking->event->venue->name }}</span>
                </div>
                <div class="ticket-row">
                    <span>👤 Name</span>
                    <span>{{ $booking->name }}</span>
                </div>
                <div class="ticket-row">
                    <span>📧 Email</span>
                    <span>{{ $booking->email }}</span>
                </div>
            </div>
        </div>

        <div class="qr-section">
            <p style="margin-bottom: 15px; color: #6b7280;">Show this code at the event check-in:</p>
            <div style="font-size: 48px; font-weight: bold; color: #10b981;">
                {{ $booking->code }}
            </div>
        </div>

        <div class="info-box">
            <strong>📌 Important Information:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <li>Please arrive at least 30 minutes before the event starts</li>
                <li>Bring a valid ID for verification</li>
                <li>Show your booking code at the check-in counter</li>
                <li>This ticket is non-transferable</li>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('bookings.ticket', $booking->code) }}" class="btn">View Ticket Online</a>
        </div>

        <div class="footer">
            <p>Good luck and enjoy the event! 🍀</p>
            <p>&copy; {{ date('Y') }} SportEventBook. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
