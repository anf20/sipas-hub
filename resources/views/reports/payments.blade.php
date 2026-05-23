<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            text-transform: uppercase;
            font-size: 18px;
        }
        .info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .total-box {
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            float: right;
            width: 250px;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SIPAS-Hub</h1>
        <p>Sistem Informasi Pembayaran Sekolah — Laporan Transaksi</p>
    </div>

    <div class="info">
        <strong>Laporan:</strong> {{ $title }}<br>
        <strong>Tanggal Cetak:</strong> {{ $date }}<br>
        <strong>Dicetak Oleh:</strong> {{ auth()->user()->name }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Siswa</th>
                <th>Tagihan</th>
                <th>Metode</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $index => $payment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $payment->paid_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $payment->invoice->student->name }}</td>
                    <td>{{ $payment->invoice->feeType->name }}</td>
                    <td>{{ ucfirst($payment->method) }}</td>
                    <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="clear"></div>

    <div class="total-box">
        <table style="border: none; margin: 0;">
            <tr style="border: none;">
                <td style="border: none;"><strong>Total Pemasukan:</strong></td>
                <td style="border: none;" class="text-right"><strong>Rp {{ number_format($total_revenue, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>

    <div class="footer">
        <p>Dicetak otomatis melalui sistem SIPAS-Hub pada {{ $date }}</p>
    </div>
</body>
</html>
