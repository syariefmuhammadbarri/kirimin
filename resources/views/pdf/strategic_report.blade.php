<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Strategis Eksekutif</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.6;
            margin: 0;
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #1e3a8a;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 14px;
            color: #555;
            margin-top: 5px;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 20px;
            font-size: 11px;
            color: #666;
        }
        .widget-row {
            width: 100%;
            margin-bottom: 25px;
        }
        .widget {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
        }
        .widget-title {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .widget-value {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1px solid #cbd5e1;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 5px;
            text-transform: uppercase;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .data-table th {
            background-color: #1e3a8a;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        .data-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 9px;
            font-weight: bold;
            color: white;
            background-color: #10b981;
            border-radius: 4px;
        }
        .footer {
            margin-top: 80px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $companyName }}</div>
        <div class="subtitle">Laporan Ringkasan Kinerja Strategis Bisnis</div>
    </div>

    <table class="meta-info">
        <tr>
            <td><strong>Tanggal Cetak:</strong> {{ now()->format('d F Y H:i') }}</td>
            <td style="text-align: right;"><strong>Periode Laporan:</strong> Bulan Terkait ({{ now()->format('F Y') }})</td>
        </tr>
    </table>

    <table width="100%" class="widget-row" style="border-spacing: 15px 0;">
        <tr>
            <td width="33%">
                <div class="widget">
                    <div class="widget-title">Total Pendapatan (Lunas)</div>
                    <div class="widget-value">Rp {{ number_format($metrics['total_revenue'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
            <td width="33%">
                <div class="widget">
                    <div class="widget-title">Volume Pengiriman</div>
                    <div class="widget-value">{{ $metrics['total_shipments'] ?? 0 }} Paket</div>
                </div>
            </td>
            <td width="33%">
                <div class="widget">
                    <div class="widget-title">Tingkat Sukses Pengiriman</div>
                    <div class="widget-value">{{ number_format($metrics['delivery_success_rate'] ?? 100.0, 1) }}%</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Kinerja Cabang Teraktif</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="40%">Nama Cabang</th>
                <th width="30%" style="text-align: center;">Jumlah Paket</th>
                <th width="30%" style="text-align: right;">Total Omset (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($metrics['branches'] as $branch)
                <tr>
                    <td>{{ $branch['name'] }}</td>
                    <td style="text-align: center;">{{ $branch['shipments_count'] }}</td>
                    <td style="text-align: right;">Rp {{ number_format($branch['revenue'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Peringkat Kurir Berdasarkan Sukses Rate</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Kurir</th>
                <th>Cabang</th>
                <th style="text-align: center;">Total Tugas</th>
                <th style="text-align: center;">Terkirim</th>
                <th style="text-align: center;">Success Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($metrics['couriers'] as $courier)
                <tr>
                    <td>{{ $courier['name'] }}</td>
                    <td>{{ $courier['branch_name'] }}</td>
                    <td style="text-align: center;">{{ $courier['total_jobs'] }}</td>
                    <td style="text-align: center;">{{ $courier['delivered_jobs'] }}</td>
                    <td style="text-align: center; font-weight: bold; color: #10b981;">
                        {{ number_format($courier['success_rate'], 1) }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Statistik Pengiriman Berdasarkan Status</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Status Paket</th>
                <th style="text-align: center;">Volume</th>
                <th style="text-align: center;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($metrics['status_distribution'] as $status => $count)
                <tr>
                    <td style="text-transform: uppercase;">{{ str_replace('_', ' ', $status) }}</td>
                    <td style="text-align: center;">{{ $count }}</td>
                    <td style="text-align: center;">
                        {{ $metrics['total_shipments'] > 0 ? number_format(($count / $metrics['total_shipments']) * 100, 1) : 0 }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dibuat secara otomatis oleh Sistem Informasi Ekspedisi Online BAZMA Express.<br>
        © {{ date('Y') }} {{ $companyName }}. Hak Cipta Dilindungi. Confidential.
    </div>
</body>
</html>
