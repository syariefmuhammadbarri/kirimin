<?php

namespace App\Services;

use App\Models\Shipment;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    /**
     * Generate customer digital invoice PDF
     */
    public function generateInvoice(Shipment $shipment)
    {
        $companyName = \App\Models\Setting::getValue('company_name', 'BAZMA Express');
        $companyAddress = \App\Models\Setting::getValue('company_address', 'Bogor, Jawa Barat');
        $companyPhone = \App\Models\Setting::getValue('company_phone', '0812-3456-7890');
        $companyEmail = \App\Models\Setting::getValue('company_email', 'support@bazma-express.com');

        $pdf = Pdf::loadView('pdf.invoice', compact(
            'shipment', 
            'companyName', 
            'companyAddress', 
            'companyPhone', 
            'companyEmail'
        ));

        return $pdf;
    }

    /**
     * Generate thermal physical receipt PDF (narrow width for receipt printers)
     */
    public function generateReceipt(Shipment $shipment)
    {
        $companyName = \App\Models\Setting::getValue('company_name', 'BAZMA Express');
        
        $qrCodeService = new QRCodeService();
        $qrCodeBase64 = $qrCodeService->generateBase64($shipment->booking_code, 120);

        // Customize paper size to thermal receipt dimensions: 80mm width (approx 226pt) by variable height
        $pdf = Pdf::loadView('pdf.receipt', compact('shipment', 'companyName', 'qrCodeBase64'))
            ->setPaper([0, 0, 226, 500], 'portrait');

        return $pdf;
    }

    /**
     * Generate Owner Strategic PDF Summary Report
     */
    public function generateStrategicReport(array $metrics)
    {
        $companyName = \App\Models\Setting::getValue('company_name', 'BAZMA Express');
        
        $pdf = Pdf::loadView('pdf.strategic_report', compact('metrics', 'companyName'));
        
        return $pdf;
    }
}
