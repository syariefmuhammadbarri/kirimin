<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService
{
    /**
     * Generate raw SVG QR Code
     */
    public function generate(string $data, int $size = 200): string
    {
        return QrCode::size($size)->generate($data);
    }

    /**
     * Generate inline base64 SVG QR Code for embedding in images/PDFs/emails
     */
    public function generateBase64(string $data, int $size = 200): string
    {
        $svg = QrCode::format('svg')
            ->size($size)
            ->errorCorrection('M')
            ->generate($data);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
