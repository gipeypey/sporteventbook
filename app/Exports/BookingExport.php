<?php

namespace App\Exports;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\DefaultValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BookingExport extends DefaultValueBinder implements FromView, ShouldAutoSize, WithColumnWidths, WithEvents
{
    use Exportable;

    protected $filters;

    public function __construct(?array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Get bookings with filters applied
     */
    protected function getBookings(): Collection
    {
        $query = Booking::with(['event.venue', 'event.category']);

        // Apply filters - use isset() to avoid "Undefined array key" errors
        if (isset($this->filters['event_id']) && !empty($this->filters['event_id'])) {
            $query->where('event_id', $this->filters['event_id']);
        }

        if (isset($this->filters['payment_status']) && !empty($this->filters['payment_status'])) {
            $paymentStatus = $this->filters['payment_status'];
            // Handle array or single value
            if (is_array($paymentStatus)) {
                $query->whereIn('payment_status', $paymentStatus);
            } else {
                $query->where('payment_status', $paymentStatus);
            }
        }

        if (isset($this->filters['is_checked_in']) && $this->filters['is_checked_in'] !== '' && $this->filters['is_checked_in'] !== null) {
            $isCheckedIn = $this->filters['is_checked_in'];
            // Handle string boolean values
            if ($isCheckedIn === 'true' || $isCheckedIn === true || $isCheckedIn === '1') {
                $query->where('is_checked_in', true);
            } elseif ($isCheckedIn === 'false' || $isCheckedIn === false || $isCheckedIn === '0') {
                $query->where('is_checked_in', false);
            }
        }

        if (isset($this->filters['date_from']) && !empty($this->filters['date_from'])) {
            $dateFrom = $this->filters['date_from'];
            // Handle Carbon objects or strings
            if (is_object($dateFrom) && method_exists($dateFrom, 'format')) {
                $dateFrom = $dateFrom->format('Y-m-d');
            }
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if (isset($this->filters['date_to']) && !empty($this->filters['date_to'])) {
            $dateTo = $this->filters['date_to'];
            // Handle Carbon objects or strings
            if (is_object($dateTo) && method_exists($dateTo, 'format')) {
                $dateTo = $dateTo->format('Y-m-d');
            }
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if (isset($this->filters['search']) && !empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Export from view for better formatting control
     */
    public function view(): View
    {
        $bookings = $this->getBookings();

        return view('exports.bookings', [
            'bookings' => $bookings,
            'exportedAt' => Carbon::now(),
            'filters' => $this->filters,
        ]);
    }

    /**
     * Column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 10,  // No
            'B' => 20,  // Booking Code
            'C' => 30,  // Event Name
            'D' => 20,  // Category
            'E' => 25,  // Venue
            'F' => 25,  // Customer Name
            'G' => 25,  // Email
            'H' => 20,  // Phone
            'I' => 15,  // Payment Status
            'J' => 15,  // Check-in Status
            'K' => 20,  // Subtotal
            'L' => 20,  // Tax
            'M' => 20,  // Discount
            'N' => 20,  // Total
            'O' => 25,  // Booking Date
            'P' => 25,  // Event Date
            'Q' => 25,  // Expiry Date
        ];
    }

    /**
     * Register event listeners for styling
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                
                // Get the highest column from the view - we have 17 columns (A-Q)
                $highestColumn = 'Q';
                
                // Add title row first
                $sheet->insertNewRowBefore(1);
                $sheet->mergeCells("A1:{$highestColumn}1");
                $sheet->setCellValue('A1', 'BOOKING REPORT - SPORTEVENTBOOK');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['argb' => 'FF552BFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Style header row (now at row 2 after inserting title row)
                $sheet->getStyle("A2:{$highestColumn}2")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF552BFF'],
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFFFF'],
                        ],
                    ],
                ]);

                // Get highest row after title is added
                $highestRow = $sheet->getHighestRow();
                
                // Style data rows only if there is data
                if ($highestRow > 2) {
                    $sheet->getStyle("A3:{$highestColumn}{$highestRow}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);

                    // Alternate row colors
                    for ($row = 3; $row <= $highestRow; $row++) {
                        $color = $row % 2 == 0 ? 'FFF6F8FA' : 'FFFFFFFF';
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => $color],
                            ],
                        ]);
                    }

                    // Format payment status column (column I) with colors
                    for ($row = 3; $row <= $highestRow; $row++) {
                        $status = $sheet->getCell('I' . $row)->getValue();
                        if ($status !== null && $status !== '') {
                            $color = $this->getStatusColor((string) $status);
                            $sheet->getStyle('I' . $row)->applyFromArray([
                                'font' => [
                                    'color' => ['argb' => $color],
                                    'bold' => true,
                                ],
                            ]);
                        }
                    }

                    // Auto-filter
                    $sheet->setAutoFilter("A2:{$highestColumn}{$highestRow}");
                }
            },
        ];
    }

    /**
     * Get color for payment status
     */
    protected function getStatusColor(string $status): string
    {
        return match (strtolower($status)) {
            'success' => 'FF10B981',  // Green
            'pending' => 'FFF59E0B',  // Yellow/Orange
            'failed' => 'FFEF4444',   // Red
            'expired' => 'FF6B7280',  // Gray
            'canceled' => 'FF6B7280', // Gray
            default => 'FF000000',    // Black
        };
    }

}
