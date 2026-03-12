<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Booking Code</th>
            <th>Event Name</th>
            <th>Category</th>
            <th>Venue</th>
            <th>Customer Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Payment Status</th>
            <th>Check-in Status</th>
            <th>Subtotal (Rp)</th>
            <th>Tax (Rp)</th>
            <th>Discount (Rp)</th>
            <th>Total (Rp)</th>
            <th>Booking Date</th>
            <th>Event Date</th>
            <th>Expiry Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bookings as $index => $booking)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $booking->code }}</td>
            <td>{{ $booking->event->title }}</td>
            <td>{{ $booking->event->category->name ?? '-' }}</td>
            <td>{{ $booking->event->venue->name ?? '-' }}</td>
            <td>{{ $booking->name }}</td>
            <td>{{ $booking->email }}</td>
            <td>{{ $booking->phone }}</td>
            <td>{{ $booking->payment_status?->label() ?? ucfirst($booking->payment_status) }}</td>
            <td>{{ $booking->is_checked_in ? 'Checked In' : 'Not Checked In' }}</td>
            <td>{{ number_format($booking->subtotal, 0, ',', '.') }}</td>
            <td>{{ number_format($booking->tax, 0, ',', '.') }}</td>
            <td>{{ number_format($booking->discount ?? 0, 0, ',', '.') }}</td>
            <td>{{ number_format($booking->total, 0, ',', '.') }}</td>
            <td>{{ $booking->created_at->format('d M Y, H:i') }}</td>
            <td>{{ $booking->event->date?->format('d M Y, H:i') ?? '-' }}</td>
            <td>{{ $booking->expires_at?->format('d M Y, H:i') ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10" style="text-align: right; font-weight: bold;">Total Bookings:</td>
            <td colspan="4" style="font-weight: bold;">{{ $bookings->count() }}</td>
        </tr>
        <tr>
            <td colspan="10" style="text-align: right; font-weight: bold;">Total Revenue:</td>
            <td colspan="4" style="font-weight: bold;">Rp {{ number_format($bookings->sum('total'), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="10" style="text-align: right; font-weight: bold;">Total Paid:</td>
            <td colspan="4" style="font-weight: bold;">Rp {{ number_format($bookings->where('payment_status', 'success')->sum('total'), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="10" style="text-align: right; font-weight: bold;">Total Pending:</td>
            <td colspan="4" style="font-weight: bold;">Rp {{ number_format($bookings->where('payment_status', 'pending')->sum('total'), 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
