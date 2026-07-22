<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi {{ $payment->receipt_number }}</title>
    <style>
        /* Pengaturan Halaman PDF */
        @page {
            size: A4 portrait;
            margin: 20px 30px 20px 30px;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }

        /* Utility Table Formatting */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        td {
            padding: 4px;
            vertical-align: top;
        }

        /* Header / Kop Surat */
        .kop-surat td {
            vertical-align: middle;
        }
        .logo {
            width: 70px;
            height: auto;
        }
        .nama-lembaga {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a365d; /* Warna Navy */
        }
        .info-lembaga {
            font-size: 10px;
            color: #555;
        }

        /* Garis Pembatas Kop */
        .border-kop {
            border-bottom: 2px solid #1a365d;
            margin-bottom: 20px;
        }

        /* Judul Kwitansi */
        .judul-kwitansi {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 5px;
            color: #1a365d;
        }
        .nomor-kwitansi {
            text-align: center;
            font-size: 11px;
            margin-bottom: 25px;
            color: #555;
        }

        /* Konten Utama Kwitansi */
        .label-konten {
            width: 25%;
            font-weight: bold;
            color: #4a5568;
        }
        .titik-dua {
            width: 2%;
            text-align: center;
        }
        .isi-konten {
            width: 73%;
            border-bottom: 1px dotted #cbd5e0;
            padding-bottom: 3px;
        }
        .terbilang-box {
            background-color: #edf2f7;
            padding: 8px;
            font-style: italic;
            font-weight: bold;
            border-left: 3px solid #1a365d;
        }

        /* Tabel Rincian Pembayaran */
        .tabel-rincian th {
            background-color: #1a365d;
            color: #ffffff;
            text-align: left;
            padding: 6px 8px;
            font-size: 11px;
            text-transform: uppercase;
        }
        .tabel-rincian td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .text-right {
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
            background-color: #f7fafc;
            border-top: 2px solid #1a365d;
            border-bottom: 2px solid #1a365d;
        }

        /* Bagian Tanda Tangan & Total Besar */
        .footer-kwitansi {
            margin-top: 30px;
        }
        .box-nominal {
            border: 2px solid #1a365d;
            background-color: #f7fafc;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #1a365d;
            text-align: center;
            display: block;
        }
        .ruang-ttd {
            height: 60px;
        }
        .nama-ttd {
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <table class="kop-surat">
        <tr>
            <td style="width: 12%;">
                {{-- Logo can be replaced with a real path or base64 if available --}}
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==" class="logo" alt="Logo">
            </td>
            <td style="width: 88%;">
                <div class="nama-lembaga">ASLIMU</div>
                <div class="info-lembaga">
                    Jl. Nyilapda Kriya Ds. Tugu Kec. Sliyeg Indramayu
                </div>
            </td>
        </tr>
    </table>

    <div class="border-kop"></div>

    <div class="judul-kwitansi">KWITANSI PEMBAYARAN</div>
    <div class="nomor-kwitansi">Nomor: {{ $payment->receipt_number }}</div>

    <table>
        <tr>
            <td class="label-konten">Telah Diterima Dari</td>
            <td class="titik-dua">:</td>
            <td class="isi-konten">{{ $payment->invoice->student->parent->name ?? 'Wali Murid' }}</td>
        </tr>
        <tr>
            <td class="label-konten">Uang Sejumlah</td>
            <td class="titik-dua">:</td>
            <td class="isi-konten">
                <div class="terbilang-box">{{ trim($payment->terbilang_amount) }} Rupiah</div>
            </td>
        </tr>
        <tr>
            <td class="label-konten">Untuk Pembayaran</td>
            <td class="titik-dua">:</td>
            <td class="isi-konten">
                {{ $payment->invoice->billing_detail }} 
                @if($payment->invoice->period_month)
                    - Periode {{ \Illuminate\Support\Carbon::parse('2026-'.$payment->invoice->period_month.'-01')->translatedFormat('F') }} {{ $payment->invoice->period_year }}
                @endif
            </td>
        </tr>
    </table>

    <br>

    <table class="tabel-rincian">
        <thead>
            <tr>
                <th style="width: 10%;">No.</th>
                <th style="width: 65%;">Deskripsi Komponen</th>
                <th style="width: 25%; text-align: right;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1.</td>
                <td>{{ $payment->invoice->billing_detail }} (Siswa: {{ $payment->invoice->student->name }})</td>
                <td class="text-right">{{ number_format($payment->invoice->amount, 0, ',', '.') }}</td>
            </tr>
            @php
                $serviceFee = (float) $payment->amount - (float) $payment->invoice->amount;
            @endphp
            @if($serviceFee > 0)
            <tr>
                <td>2.</td>
                <td>Biaya Layanan Online ({{ $payment->method }})</td>
                <td class="text-right">{{ number_format($serviceFee, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="2" class="text-right">TOTAL:</td>
                <td class="text-right">{{ number_format($payment->amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="footer-kwitansi">
        <tr>
            <td style="width: 45%; padding-top: 15px;">
                <div class="box-nominal">
                    JUMLAH: Rp {{ number_format($payment->amount, 0, ',', '.') }},-
                </div>
            </td>
            <td style="width: 15%;"></td>
            <td style="width: 40%; text-align: center;">
                <div>Indramayu, {{ $payment->paid_at->translatedFormat('d F Y') }}</div>
                <div style="font-weight: bold; margin-top: 5px;">Bendahara Lembaga,</div>
                <div class="ruang-ttd"></div> 
                <div class="nama-ttd">{{ $payment->recorder->name ?? 'Sistem SIPAS-Hub' }}</div>
                @if($payment->recorder && $payment->recorder->nip)
                    <div style="font-size: 10px; color: #777;">NIP. {{ $payment->recorder->nip }}</div>
                @endif
            </td>
        </tr>
    </table>

</body>
</html>
