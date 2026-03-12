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
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\DefaultValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BookingExport extends DefaultValueBinder implements FromView, ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles
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

        // Apply filters
        if (!empty($this->filters['event_id'])) {
            $query->where('event_id', $this->filters['event_id']);
        }

        if (!empty($this->filters['payment_status'])) {
            $paymentStatus = $this->filters['payment_status'];
            // Handle array or single value
            if (is_array($paymentStatus)) {
                $query->whereIn('payment_status', $paymentStatus);
            } else {
                $query->where('payment_status', $paymentStatus);
            }
        }

        if (isset($this->filters['is_checked_in']) && $this->filters['is_checked_in'] !== '') {
            $isCheckedIn = $this->filters['is_checked_in'];
            // Handle string boolean values
            if ($isCheckedIn === 'true' || $isCheckedIn === true) {
                $query->where('is_checked_in', true);
            } elseif ($isCheckedIn === 'false' || $isCheckedIn === false) {
                $query->where('is_checked_in', false);
            }
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['search'])) {
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
            0 => 15,  // No
            1 => 20,  // Booking Code
            2 => 30,  // Event Name
            3 => 25,  // Customer Name
            4 => 25,  // Email
            5 => 20,  // Phone
            6 => 15,  // Payment Status
            7 => 15,  // Check-in Status
            8 => 20,  // Subtotal
            9 => 20,  // Tax
            10 => 20, // Discount
            11 => 20, // Total
            12 => 25, // Booking Date
            13 => 25, // Event Date
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
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Style header row
                $sheet->getStyle('A1:N1')->applyFromArray([
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

                // Add title row
                $sheet->insertNewRowBefore(1);
                $sheet->mergeCells('A1:N1');
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

                // Style data rows
                if ($highestRow > 1) {
                    $sheet->getStyle('A3:N' . $highestRow + 1)->applyFromArray([
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
                    for ($row = 3; $row <= $highestRow + 1; $row++) {
                        $color = $row % 2 == 0 ? 'FFF6F8FA' : 'FFFFFFFF';
                        $sheet->getStyle('A' . $row . ':N' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => $color],
                            ],
                        ]);
                    }
                }

                // Format payment status column with colors
                for ($row = 3; $row <= $highestRow + 1; $row++) {
                    $status = $sheet->getCell('G' . $row)->getValue();
                    $color = $this->getStatusColor($status);
                    $sheet->getStyle('G' . $row)->applyFromArray([
                        'font' => [
                            'color' => ['argb' => $color],
                            'bold' => true,
                        ],
                    ]);
                }

                // Auto-filter
                $sheet->setAutoFilter('A2:N' . $highestRow + 1);
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

    /**
     * Apply styles to worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Set default font
        $sheet->getDefaultStyle()->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'size' => 11,
            ],
        ]);

        // Center align some columns
        $sheet->getStyle('A3:A' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('G3:H' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Right align currency columns
        $sheet->getStyle('I3:L' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberformat' => ['format' => '#,##0'],
        ]);
    }
}
