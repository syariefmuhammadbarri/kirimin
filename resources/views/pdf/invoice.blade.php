<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $shipment->tracking_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
        }
        .header {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            text-transform: uppercase;
        }
        .company-info {
            text-align: right;
            font-size: 11px;
            color: #666;
        }
        .details-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 5px;
            vertical-align: top;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 5px;
        }
        .section-title {
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1px solid #ddd;
            margin-top: 15px;
            margin-bottom: 10px;
            padding-bottom: 3px;
            font-size: 12px;
            text-transform: uppercase;
        }
        .route-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .route-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
        }
        .route-table td {
            padding: 8px;
            border: 1px solid #e5e7eb;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #1e3a8a;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            font-size: 11px;
        }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .summary-table {
            width: 40%;
            margin-left: 60%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .summary-table td {
            padding: 5px;
            font-size: 12px;
        }
        .summary-table .total {
            font-weight: bold;
            font-size: 15px;
            color: #3b82f6;
            border-top: 1px solid #ddd;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table class="header">
            <tr>
                <td>
                    <div class="logo">{{ $companyName }}</div>
                    <div>Sistem Informasi Ekspedisi Online</div>
                </td>
                <td class="company-info">
                    <strong>{{ $companyName }} HQ</strong><br>
                    {{ $companyAddress }}<br>
                    Telp: {{ $companyPhone }}<br>
                    Email: {{ $companyEmail }}
                </td>
            </tr>
        </table>

        <table class="details-table">
            <tr>
                <td width="50%">
                    <div class="title">INVOICE PENGIRIMAN</div>
                    <strong>Nomor Resi:</strong> {{ $shipment->tracking_number }}<br>
                    <strong>Kode Booking:</strong> {{ $shipment->booking_code }}<br>
                    <strong>Tanggal:</strong> {{ $shipment->created_at->format('d M Y H:i') }}<br>
                    <strong>Status:</strong> <span style="text-transform: uppercase; color: #10b981; font-weight: bold;">{{ str_replace('_', ' ', $shipment->status) }}</span>
                </td>
                <td width="50%" style="text-align: right;">
                    <strong>Metode Pembayaran:</strong> {{ strtoupper($shipment->payment->payment_method ?? 'Transfer') }}<br>
                    <strong>Status Pembayaran:</strong> {{ strtoupper($shipment->payment->payment_status ?? 'Pending') }}<br>
                    <strong>ID Transaksi:</strong> {{ $shipment->payment->order_id ?? '-' }}
                </td>
            </tr>
        </table>

        <div class="section-title">Alur Pengiriman & Layanan</div>
        <table class="route-table">
            <tr>
                <th width="30%">Kota Asal</th>
                <th width="30%">Kota Tujuan</th>
                <th width="20%">Layanan</th>
                <th width="20%">Estimasi Pengiriman</th>
            </tr>
            <tr>
                <td>{{ $shipment->origin_city }}</td>
                <td>{{ $shipment->destination_city }}</td>
                <td>{{ strtoupper($shipment->service_type) }}</td>
                <td>{{ $shipment->payment ? '2-3 Hari' : 'Estimasi Tergantung Outlet' }}</td>
            </tr>
        </table>

        <table class="details-table">
            <tr>
                <td width="50%">
                    <div class="section-title">Pengirim</div>
                    <strong>{{ $shipment->sender_name }}</strong><br>
                    Telp: {{ $shipment->sender_phone }}<br>
                    Alamat: {{ $shipment->sender_address }}
                </td>
                <td width="50%">
                    <div class="section-title">Penerima</div>
                    <strong>{{ $shipment->receiver_name }}</strong><br>
                    Telp: {{ $shipment->receiver_phone }}<br>
                    Alamat: {{ $shipment->receiver_address }}
                </td>
            </tr>
        </table>

        <div class="section-title">Rincian Paket</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th style="text-align: right;">Berat (Kg)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shipment->items as $item)
                    <tr>
                        <td>{{ $item->item_name }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">{{ number_format($item->weight, 2) }} kg</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary-table">
            <tr>
                <td>Berat Total:</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($shipment->actual_weight ?: $shipment->estimated_weight, 2) }} kg</td>
            </tr>
            @if($shipment->actual_weight && $shipment->actual_weight != $shipment->estimated_weight)
            <tr>
                <td style="font-size: 11px; color: #666;">Estimasi Awal:</td>
                <td style="text-align: right; font-size: 11px; color: #666;">{{ number_format($shipment->estimated_weight, 2) }} kg</td>
            </tr>
            @endif
            <tr>
                <td>Tarif per Kg:</td>
                <td style="text-align: right;">Rp {{ number_format($shipment->total_price / ($shipment->actual_weight ?: $shipment->estimated_weight), 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td>Total Ongkir:</td>
                <td style="text-align: right;">Rp {{ number_format($shipment->total_price, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="footer">
            Terima kasih telah mempercayakan pengiriman paket Anda bersama kami.<br>
            Lacak posisi paket Anda kapan saja di website BAZMA Express menggunakan nomor resi di atas.
        </div>
    </div>
</body>
</html>
