<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk {{ $shipment->tracking_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            color: #000;
            font-size: 10px;
            margin: 0;
            padding: 5px;
            width: 210pt; /* fits in paper container width */
        }
        .text-center {
            text-align: center;
        }
        .header {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 5px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .label {
            width: 40%;
            font-weight: bold;
        }
        .value {
            width: 60%;
        }
        .qr-code {
            margin: 10px auto;
            text-align: center;
        }
        .qr-code img {
            width: 90px;
            height: 90px;
        }
        .footer {
            font-size: 9px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="text-center header">
        {{ strtoupper($companyName) }}
    </div>
    <div class="text-center font-size: 8px;">
        Struk Bukti Pengiriman Paket
    </div>
    
    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td class="label">NO RESI:</td>
            <td class="value">{{ $shipment->tracking_number }}</td>
        </tr>
        <tr>
            <td class="label">BOOKING:</td>
            <td class="value">{{ $shipment->booking_code }}</td>
        </tr>
        <tr>
            <td class="label">TANGGAL:</td>
            <td class="value">{{ $shipment->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">STATUS:</td>
            <td class="value" style="font-weight: bold;">{{ strtoupper($shipment->status) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td class="label">PENGIRIM:</td>
            <td class="value">{{ $shipment->sender_name }}<br>({{ $shipment->sender_phone }})</td>
        </tr>
        <tr>
            <td class="label">ASAL:</td>
            <td class="value">{{ $shipment->origin_city }}</td>
        </tr>
        <tr>
            <td class="label">PENERIMA:</td>
            <td class="value">{{ $shipment->receiver_name }}<br>({{ $shipment->receiver_phone }})</td>
        </tr>
        <tr>
            <td class="label">TUJUAN:</td>
            <td class="value">{{ $shipment->receiver_address }}, {{ $shipment->destination_city }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td class="label">BERAT AKTUAL:</td>
            <td class="value" style="font-weight: bold;">{{ number_format($shipment->actual_weight ?: $shipment->estimated_weight, 2) }} kg</td>
        </tr>
        <tr>
            <td class="label">LAYANAN:</td>
            <td class="value">{{ strtoupper($shipment->service_type) }}</td>
        </tr>
        <tr>
            <td class="label">TOTAL BIAYA:</td>
            <td class="value" style="font-weight: bold; font-size: 11px;">Rp {{ number_format($shipment->total_price, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">PEMBAYARAN:</td>
            <td class="value">{{ strtoupper($shipment->payment->payment_status ?? 'Cash') }} ({{ strtoupper($shipment->payment->payment_method ?? 'Tunai') }})</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="qr-code">
        <img src="{{ $qrCodeBase64 }}" alt="QR Booking">
        <div style="font-size: 8px; margin-top: 3px;">Scan QR untuk tracking & drop-off</div>
    </div>

    <div class="divider"></div>

    <div class="text-center footer">
        Terima kasih atas kunjungan Anda.<br>
        BAZMA Express - Cepat & Aman Terpercaya.
    </div>
</body>
</html>
