<?php

namespace App\Helpers;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeHelper
{
    /**
     * Generate QR code as base64 PNG (compatible with dompdf)
     * Uses Endroid QR Code library
     */
    public static function generate(string $data, int $size = 150): string
    {
        try {
            // Create QR code using Endroid
            $qrCode = new QrCode($data);
            $qrCode->setSize($size);
            $qrCode->setMargin(0);
            
            // Write as PNG
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $pngData = $result->getString();
            
            // Return as base64 data URI
            return '<img src="data:image/png;base64,' . base64_encode($pngData) . '" alt="QR Code" style="width: ' . $size . 'px; height: ' . $size . 'px;">';
        } catch (\Exception $e) {
            return self::fallback($data, $size);
        }
    }

    /**
     * Fallback: simple text display
     */
    private static function fallback(string $data, int $size): string
    {
        return '<div style="font-size: 24px; font-weight: bold; color: #552BFF; padding: 20px;">QR Code: ' . $data . '</div>';
    }
}