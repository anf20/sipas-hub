<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi {{ $payment->receipt_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #222;
            margin: 0;
            padding: 0;
        }
        .receipt-container {
            width: 100%;
            padding: 20px;
            border: 2px solid #333;
            background-color: #fff;
        }
        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .school-info {
            display: table-cell;
            width: 70%;
        }
        .receipt-title {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: middle;
        }
        .receipt-title h1 {
            margin: 0;
            font-size: 24px;
            color: #031636;
        }
        .school-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            color: #031636;
        }
        .content {
            margin-bottom: 30px;
        }
        .row {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }
        .label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            color: #555;
        }
        .value {
            display: table-cell;
            border-bottom: 1px dotted #999;
            padding-left: 10px;
        }
        .amount-box {
            background-color: #f2f2f2;
            padding: 15px;
            margin-top: 20px;
            border: 1px solid #ccc;
            display: inline-block;
            min-width: 250px;
        }
        .amount-text {
            font-size: 18px;
            font-weight: bold;
            color: #031636;
        }
        .footer {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature {
            display: table-cell;
            width: 50%;
            text-align: right;
            padding-right: 50px;
        }
        .date-info {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
            font-style: italic;
            color: #777;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 60px;
            color: rgba(0, 108, 73, 0.1);
            z-index: -1;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="watermark">PAID - LUNAS</div>
        
        <div class="header">
            <div class="school-info">
                <p class="school-name">SIPAS-Hub Academy</p>
                <p>Jl. Pendidikan No. 123, Kota Cerdas</p>
                <p>Telp: (021) 1234567 • Email: info@sipashub.test</p>
            </div>
            <div class="receipt-title">
                <h1>KWITANSI</h1>
                <p>No: <strong>{{ $payment->receipt_number }}</strong></p>
            </div>
        </div>

        <div class="content">
            <div class="row">
                <div class="label">Telah Diterima Dari</div>
                <div class="value">{{ $payment->invoice->student->parent->name ?? 'Wali Murid' }}</div>
            </div>
            <div class="row">
                <div class="label">Nama Siswa</div>
                <div class="value">{{ $payment->invoice->student->name }} (NIS: {{ $payment->invoice->student->nis }})</div>
            </div>
            <div class="row">
                <div class="label">Untuk Pembayaran</div>
                <div class="value">
                    {{ $payment->invoice->feeType->name }} 
                    @if($payment->invoice->period_month)
                        Periode {{ \Illuminate\Support\Carbon::parse('2026-'.$payment->invoice->period_month.'-01')->translatedFormat('F') }} {{ $payment->invoice->period_year }}
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="label">Metode Pembayaran</div>
                <div class="value uppercase">{{ $payment->method }}</div>
            </div>

            <div class="amount-box">
                <span>Terbilang:</span><br>
                <div class="amount-text">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="footer">
            <div class="date-info">
                Dicetak pada: {{ $date }}
            </div>
            <div class="signature">
                <p>Kota Cerdas, {{ $payment->paid_at->translatedFormat('d F Y') }}</p>
                <p style="margin-bottom: 60px;">Bendahara Sekolah,</p>
                <p><strong>{{ $payment->recorder->name ?? 'Sistem SIPAS-Hub' }}</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
