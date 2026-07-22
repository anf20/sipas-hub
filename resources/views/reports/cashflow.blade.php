<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 40px 30px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2d3748;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            color: #1a202c;
            font-size: 16px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #4a5568;
            font-size: 10px;
        }
        .summary-box {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .summary-box td {
            width: 33.33%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            text-align: center;
            background-color: #f8fafc;
        }
        .summary-box .title {
            font-size: 9px;
            text-transform: uppercase;
            color: #718096;
            margin-bottom: 3px;
            display: block;
        }
        .summary-box .amount {
            font-size: 14px;
            font-weight: bold;
            color: #2d3748;
            display: block;
        }
        .summary-box .subtext {
            font-size: 8px;
            color: #a0aec0;
            margin-top: 2px;
            display: block;
        }
        .amount.green { color: #059669; }
        .amount.red { color: #dc2626; }
        .amount.blue { color: #2563eb; }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-size: 8pt;
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: left;
            color: #475569;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .page-break {
            page-break-before: always;
        }
        
        #footer {
            position: fixed;
            bottom: -20px;
            left: 0px;
            right: 0px;
            height: 20px;
            color: #94a3b8;
            font-size: 8px;
        }
        .page-number { text-align: right; }
        .page-number:before {
            content: "Halaman " counter(page) " dari " counter(pages);
        }
        
        .signature-box {
            width: 100%;
            margin-top: 40px;
        }
        .signature-box td {
            width: 33.33%;
            text-align: center;
        }
        .signature-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <div id="footer">
        <table width="100%">
            <tr>
                <td width="50%">Dicetak pada: {{ now()->format('d/m/Y H:i') }} oleh Admin</td>
                <td width="50%" class="page-number"></td>
            </tr>
        </table>
    </div>

    <!-- HALAMAN 1: RINGKASAN REKAPITULASI -->
    <div class="header">
        <h2>{{ $title }}</h2>
        <p>
            Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}</strong> s.d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</strong>
        </p>
    </div>

    <table class="summary-box">
        <tr>
            <td>
                <span class="title">Total Pemasukan Kas</span>
                <span class="amount green">Rp {{ number_format($totalInflow, 0, ',', '.') }}</span>
                <span class="subtext">Dari {{ $totalInflowCount }} transaksi</span>
            </td>
            <td>
                <span class="title">Tunggakan Baru Tercipta</span>
                <span class="amount red">Rp {{ number_format($totalNewDebt, 0, ',', '.') }}</span>
                <span class="subtext">Dari {{ $totalNewDebtCount }} tagihan</span>
            </td>
            <td>
                <span class="title">Kolektabilitas</span>
                <span class="amount blue">{{ $collectionRate }}%</span>
                <span class="subtext">Untuk tagihan periode ini</span>
            </td>
        </tr>
    </table>

    <h4 style="margin-bottom: 5px; margin-top: 20px; color: #334155; font-size: 11pt;">Rincian Pemasukan Kas per Kategori</h4>
    
    @if(count($breakdown) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th width="20%">Kategori</th>
                    <th width="20%" class="text-right">Total Tagihan (Target)</th>
                    <th width="20%" class="text-right">Pemasukan (Lunas)</th>
                    <th width="20%" class="text-right">Sisa Tunggakan</th>
                    <th width="20%" class="text-right">Rate (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($breakdown as $row)
                    <tr>
                        <td><strong>{{ $row['category'] }}</strong></td>
                        <td class="text-right">Rp {{ number_format($row['target'], 0, ',', '.') }}</td>
                        <td class="text-right" style="color: #059669; font-weight: bold;">Rp {{ number_format($row['paid'], 0, ',', '.') }}</td>
                        <td class="text-right" style="color: #dc2626;">Rp {{ number_format($row['unpaid'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ $row['rate'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: #94a3b8; padding: 20px;">Tidak ada data pada periode ini.</p>
    @endif

    <table class="signature-box">
        <tr>
            <td>
                <p>Mengetahui,</p>
                <p><strong>Kepala Sekolah</strong></p>
                <div class="signature-space"></div>
                <p>_______________________</p>
            </td>
            <td></td>
            <td>
                <p>Dibuat Oleh,</p>
                <p><strong>Admin Keuangan</strong></p>
                <div class="signature-space"></div>
                <p>_______________________</p>
            </td>
        </tr>
    </table>

    <!-- HALAMAN 2: LAMPIRAN RINCIAN TRANSAKSI -->
    <div class="page-break"></div>

    <div class="header">
        <h2>Lampiran Rincian Transaksi</h2>
        <p>
            Daftar Pembayaran Kas Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}</strong> s.d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</strong>
        </p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="3%" class="text-center">No</th>
                <th width="12%">Waktu</th>
                <th width="13%">No. Kwitansi</th>
                <th width="20%">Nama Siswa (NIS)</th>
                <th width="7%">Kelas</th>
                <th width="25%">Rincian Tagihan</th>
                <th width="10%">Metode</th>
                <th width="10%" class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @forelse($payments as $index => $payment)
                @php $totalAmount += $payment->amount; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $payment->receipt_number ?? '-' }}</td>
                    <td>
                        <strong>{{ $payment->invoice->student->name ?? 'N/A' }}</strong><br>
                        <span style="color: #64748b; font-size: 7pt;">{{ $payment->invoice->student->nis ?? '-' }}</span>
                    </td>
                    <td>{{ $payment->invoice->student->class ?? '-' }}</td>
                    <td>
                        {{ $payment->invoice->feeType->name ?? 'Tagihan' }}
                    </td>
                    <td>{{ strtoupper($payment->method) }}</td>
                    <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="color: #94a3b8; padding: 15px;">Tidak ada transaksi pembayaran pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($payments) > 0)
        <tfoot>
            <tr>
                <th colspan="7" class="text-right" style="font-size: 9pt; background-color: #e2e8f0;">TOTAL KAS MASUK</th>
                <th class="text-right" style="font-size: 9pt; background-color: #e2e8f0; color: #059669;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
        @endif
    </table>

</body>
</html>
