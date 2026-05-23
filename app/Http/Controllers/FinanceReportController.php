<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceReportController extends Controller
{
    public function exportPaymentsPdf()
    {
        $payments = Payment::with(['invoice.student', 'invoice.feeType'])
            ->latest()
            ->limit(100) // Ambil 100 transaksi terbaru
            ->get();

        $data = [
            'title' => 'Laporan Transaksi Pembayaran',
            'date' => date('d/m/Y H:i'),
            'payments' => $payments,
            'total_revenue' => $payments->sum('amount'),
        ];

        $pdf = Pdf::loadView('reports.payments', $data);

        return $pdf->download('laporan-pembayaran-'.date('YmdHis').'.pdf');
    }
}
