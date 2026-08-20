<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi - {{ $payment->receipt_number }}</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 10mm 15mm 10mm 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11.5px;
            color: #1e293b;
            line-height: 1.4;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Symmetrical outer frame */
        .receipt-card {
            border: 2px solid #1e293b;
            padding: 14px 18px;
            background-color: #ffffff;
            margin: 0;
        }

        /* Header Layout: 3 columns with equal width left & right for perfect center symmetry */
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-logo {
            width: 80px;
            text-align: left;
            vertical-align: middle;
        }

        .header-text {
            text-align: center;
            vertical-align: middle;
        }

        .header-side-space {
            width: 80px;
            text-align: right;
            vertical-align: top;
            font-size: 9.5px;
            color: #64748b;
            font-family: monospace;
        }

        .header-kwitansi {
            color: #dc2626; /* Merah Kwitansi */
            font-family: 'Times New Roman', Times, serif;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .header-pesantren {
            font-size: 17px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .header-alamat {
            font-size: 11px;
            font-weight: bold;
            color: #475569;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Garis batas kop */
        .kop-divider {
            border-bottom: 2px solid #0f172a;
            margin-top: 10px;
            margin-bottom: 16px;
        }

        /* Content Table */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .content-table td {
            padding: 6px 0;
            vertical-align: middle;
        }

        .label-cell {
            width: 160px;
            font-weight: bold;
            color: #334155;
            white-space: nowrap;
        }

        .colon-cell {
            width: 15px;
            text-align: center;
            font-weight: bold;
            color: #334155;
        }

        .value-cell {
            border-bottom: 1px dotted #475569;
            padding-bottom: 2px;
            padding-left: 4px;
        }

        .value-bold {
            font-weight: bold;
            color: #0f172a;
            font-size: 12.5px;
        }

        .value-terbilang {
            font-size: 11px;
            font-style: italic;
            color: #475569;
            margin-left: 8px;
        }

        /* Date Section */
        .date-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 10px;
        }

        .date-cell {
            text-align: right;
            font-weight: bold;
            color: #1e293b;
            font-size: 11.5px;
            padding-right: 15px;
        }

        /* Signature Section */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .sig-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .sig-title {
            font-weight: bold;
            color: #334155;
            font-size: 12px;
            margin-bottom: 40px;
        }

        .sig-name {
            font-weight: bold;
            color: #0f172a;
            font-size: 12px;
        }
    </style>
</head>
<body>

@php
    $student = $payment->invoice->student;
    $className = $student->schoolClass->name ?? ($student->current_grade ? 'Kelas '.$student->current_grade : '');
    $studentDisplay = $student ? $student->name . ($className ? ' ('.$className.')' : '') : '-';

    $billingDetail = $payment->invoice->billing_detail ?? 'Pembayaran Administrasi Sekolah';
    if ($payment->invoice->period_month && $payment->invoice->period_year) {
        $billingDetail .= ' (Periode ' . \Illuminate\Support\Carbon::parse('2026-'.$payment->invoice->period_month.'-01')->translatedFormat('F') . ' ' . $payment->invoice->period_year . ')';
    }

    $amountFormatted = number_format($payment->amount, 0, ',', '.');
    $methodName = ucfirst($payment->method ?? 'Tunai');
@endphp

<div class="receipt-card">
    <!-- Kop Kwitansi Simetris -->
    <table class="header-table">
        <tr>
            <td class="header-logo">
                <svg width="50" height="50" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="46" stroke="#059669" stroke-width="4" fill="#f0fdf4"/>
                    <circle cx="50" cy="50" r="39" stroke="#dc2626" stroke-width="2" fill="#ffffff"/>
                    <rect x="36" y="38" width="28" height="28" fill="#1e293b" rx="2"/>
                    <rect x="36" y="44" width="28" height="4" fill="#f59e0b"/>
                    <path d="M50 20 L52 25 L57 25 L53 28 L55 33 L50 30 L45 33 L47 28 L43 25 L48 25 Z" fill="#16a34a"/>
                    <path d="M28 74 Q50 82 72 74" stroke="#059669" stroke-width="3" fill="none"/>
                </svg>
            </td>
            <td class="header-text">
                <div class="header-kwitansi">KWITANSI PEMBAYARAN</div>
                <div class="header-pesantren">PESANTREN MODERN AS-SAKIENAH</div>
                <div class="header-alamat">TUGU - SLIYEG - INDRAMAYU</div>
            </td>
            <td class="header-side-space">
                No: {{ $payment->receipt_number }}
            </td>
        </tr>
    </table>

    <div class="kop-divider"></div>

    <!-- Isi Formulir Kwitansi -->
    <table class="content-table">
        <tr>
            <td class="label-cell">Telah terima dari</td>
            <td class="colon-cell">:</td>
            <td class="value-cell">
                <span class="value-bold">{{ $studentDisplay }}</span>
            </td>
        </tr>
        <tr>
            <td class="label-cell">{{ $payment->method && strtolower($payment->method) === 'tunai' ? 'Tunai' : ($payment->method ?? 'Tunai') }}</td>
            <td class="colon-cell">:</td>
            <td class="value-cell">
                <span class="value-bold">Rp {{ $amountFormatted }}</span>
                @if($payment->terbilang_amount)
                    <span class="value-terbilang">({{ trim($payment->terbilang_amount) }} Rupiah)</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label-cell">Untuk pembayaran</td>
            <td class="colon-cell">:</td>
            <td class="value-cell">
                <span class="value-bold">{{ $billingDetail }}</span>
            </td>
        </tr>
    </table>

    <!-- Tanggal & Lokasi -->
    <table class="date-table">
        <tr>
            <td class="date-cell">
                As-Sakienah, {{ $payment->paid_at ? $payment->paid_at->format('d / m / Y') : date('d / m / Y') }}
            </td>
        </tr>
    </table>

    <!-- Kolom Tanda Tangan Simetris -->
    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-title">Disetujui</div>
                <div class="sig-name">( {{ $methodName }} )</div>
            </td>
            <td>
                <div class="sig-title">Yang Menerima</div>
                <div class="sig-name">( {{ $payment->recorder->name ?? 'Admin Keuangan' }} )</div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
