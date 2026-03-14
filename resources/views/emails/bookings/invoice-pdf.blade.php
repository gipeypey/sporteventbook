<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $booking->code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background-color: #fff;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #552BFF;
            margin-bottom: 20px;
        }
        
        .header h1 {
            color: #552BFF;
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #666;
            font-size: 14px;
        }
        
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .invoice-info-left, .invoice-info-right {
            width: 48%;
        }
        
        .invoice-info h3 {
            font-size: 16px;
            color: #552BFF;
            margin-bottom: 10px;
            border-bottom: 2px solid #552BFF;
            padding-bottom: 5px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            width: 120px;
            color: #555;
        }
        
        .info-value {
            color: #333;
        }
        
        .event-section {
            background: linear-gradient(135deg, #552BFF 0%, #7B5FE8 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .event-section h2 {
            font-size: 20px;
            margin-bottom: 15px;
        }
        
        .event-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .event-detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .event-detail-item .icon {
            width: 20px;
            height: 20px;
        }
        
        .qr-section {
            text-align: center;
            padding: 30px;
            background: #f9f9f9;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .qr-section h3 {
            color: #552BFF;
            margin-bottom: 15px;
        }
        
        .qr-code {
            display: inline-block;
            padding: 15px;
            background: white;
            border: 3px solid #552BFF;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        
        .booking-code {
            font-size: 18px;
            font-weight: bold;
            color: #552BFF;
            font-family: monospace;
            background: #f0f0ff;
            padding: 8px 20px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 10px;
        }
        
        .pricing-section {
            margin-top: 20px;
        }
        
        .pricing-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .pricing-table th {
            background: #552BFF;
            color: white;
            padding: 12px;
            text-align: left;
        }
        
        .pricing-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
        }
        
        .pricing-table tr:last-child td {
            border-bottom: none;
        }
        
        .pricing-table .total-row {
            background: #f0f0ff;
            font-weight: bold;
            font-size: 16px;
        }
        
        .pricing-table .total-row td {
            padding: 15px 12px;
            color: #552BFF;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #FEF3C7;
            color: #D97706;
        }
        
        .status-success {
            background: #D1FAE5;
            color: #059669;
        }
        
        .status-failed {
            background: #FEE2E2;
            color: #DC2626;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        
        .notes {
            background: #fffbeb;
            border-left: 4px solid #F59E0B;
            padding: 15px;
            margin: 20px 0;
        }
        
        .notes h4 {
            color: #D97706;
            margin-bottom: 10px;
        }
        
        .notes ul {
            margin-left: 20px;
            color: #92400E;
        }
        
        .notes li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>INVOICE</h1>
            <p>SportEventBook - Event Booking Platform</p>
        </div>
        
        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="invoice-info-left">
                <h3>Invoice Details</h3>
                <div class="info-row">
                    <span class="info-label">Invoice No:</span>
                    <span class="info-value">{{ $booking->code }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span class="info-value">{{ $booking->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ $booking->payment_status?->value ?? 'pending' }}">
                            {{ $booking->payment_status?->label() ?? 'Pending' }}
                        </span>
                    </span>
                </div>
            </div>
            <div class="invoice-info-right">
                <h3>Participant</h3>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $booking->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $booking->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $booking->phone }}</span>
                </div>
            </div>
        </div>
        
        <!-- Event Section -->
        <div class="event-section">
            <h2>{{ $booking->event->title }}</h2>
            <div class="event-details">
                <div class="event-detail-item">
                    <span>📁</span>
                    <span>Category: {{ $booking->event->category->name ?? 'N/A' }}</span>
                </div>
                <div class="event-detail-item">
                    <span>📅</span>
                    <span>Date: {{ $booking->event->date->format('d F Y, H:i') }}</span>
                </div>
                <div class="event-detail-item">
                    <span>📍</span>
                    <span>Venue: {{ $booking->event->venue->name ?? 'N/A' }}</span>
                </div>
                @if($booking->event->venue->address)
                <div class="event-detail-item">
                    <span>🏠</span>
                    <span>Address: {{ $booking->event->venue->address }}</span>
                </div>
                @endif
            </div>
        </div>
        
        <!-- QR Code Section -->
        <div class="qr-section">
            <h3 style="color: #552BFF; margin-bottom: 15px;">Your Entry Ticket</h3>
            <div class="qr-code">
                @php
                    // Generate QR code using Endroid QR Code library (base64 PNG - compatible with dompdf)
                    try {
                        echo \App\Helpers\QrCodeHelper::generate($booking->code, 150);
                    } catch (\Exception $e) {
                        // Fallback: show booking code only if QR generation fails
                        echo '<div style="font-size: 24px; font-weight: bold; color: #552BFF; padding: 20px;">QR Code: ' . $booking->code . '</div>';
                    }
                @endphp
            </div>
            <br>
            <div class="booking-code">{{ $booking->code }}</div>
            <p style="margin-top: 15px; color: #666; font-size: 13px;">
                Show this QR code at the check-in counter on the event day
            </p>
        </div>
        
        <!-- Pricing Section -->
        <div class="pricing-section">
            <table class="pricing-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th style="text-align: right;">Amount (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Event Registration Fee</td>
                        <td style="text-align: right;">{{ number_format($booking->subtotal ?? $booking->event->price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Tax ({{ config('pricing.tax_rate', 11) }}%)</td>
                        <td style="text-align: right;">{{ number_format($booking->tax ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Insurance</td>
                        <td style="text-align: right;">{{ number_format($booking->insurance ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @if($booking->discount && $booking->discount > 0)
                    <tr>
                        <td>Discount ({{ $booking->promo_code ?? 'Promo' }})</td>
                        <td style="text-align: right; color: #DC2626;">-{{ number_format($booking->discount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>TOTAL</td>
                        <td style="text-align: right;">Rp {{ number_format($booking->total ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Notes -->
        <div class="notes">
            <h4>⚠️ Important Notes:</h4>
            <ul>
                <li>Please arrive at least 30 minutes before the event starts</li>
                <li>This ticket is non-transferable and valid for one-time use only</li>
                <li>Bring a valid ID that matches the participant name</li>
                <li>For any questions, please contact our support team</li>
                <li>Ticket expires at: {{ $booking->expires_at?->format('d M Y, H:i') ?? 'N/A' }}</li>
            </ul>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>SportEventBook</strong> - Your Gateway to Amazing Sports Events</p>
            <p>Generated on {{ now()->format('d M Y, H:i') }}</p>
        </div>
    </div>
</body>
</html>