<?php

namespace App\Traits;

use Barryvdh\DomPDF\Facade\Pdf;

trait GeneratesInvoicePdf
{
    /**
     * Generate invoice PDF for the booking
     */
    public function generateInvoicePdf()
    {
        $pdf = Pdf::loadView('emails.bookings.invoice-pdf', [
            'booking' => $this,
        ]);
        
        return $pdf;
    }
    
    /**
     * Download invoice PDF
     */
    public function downloadInvoice(string $filename = null)
    {
        $filename = $filename ?? 'invoice-' . $this->code . '.pdf';
        
        return $this->generateInvoicePdf()->download($filename);
    }
    
    /**
     * Get invoice PDF as string
     */
    public function getInvoicePdfAsString()
    {
        return $this->generateInvoicePdf()->output();
    }
    
    /**
     * Save invoice PDF to storage
     */
    public function saveInvoicePdf(string $path = null)
    {
        $path = $path ?? 'invoices/' . $this->code . '.pdf';
        
        $pdf = $this->generateInvoicePdf();
        
        // Ensure directory exists
        $directory = dirname($path);
        if (!storage_path("app/public/{$directory}")) {
            mkdir(storage_path("app/public/{$directory}"), 0755, true);
        }
        
        $pdf->save(storage_path("app/public/{$path}"));
        
        return $path;
    }
}