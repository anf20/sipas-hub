<?php

namespace App\Http\Controllers;

use App\Models\Payment;
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

    public function exportCashflowPdf(\Illuminate\Http\Request $request)
    {
        $startDate = $request->query('start', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end', now()->endOfMonth()->format('Y-m-d'));
        $category = $request->query('category', 'all');
        $paymentMethod = $request->query('method', 'all');
        $search = $request->query('search', '');

        $start = $startDate . ' 00:00:00';
        $end = $endDate . ' 23:59:59';

        // 1. FILTERED PAYMENTS QUERY
        $paymentsQuery = Payment::whereBetween('paid_at', [$start, $end])
            ->where(function($q) {
                $q->where('status', 'success')->orWhereNull('status');
            });

        if ($paymentMethod !== 'all') {
            if ($paymentMethod === 'midtrans') {
                $paymentsQuery->where('method', '!=', 'manual')->where('method', '!=', 'cash');
            } else {
                $paymentsQuery->whereIn('method', ['manual', 'cash']);
            }
        }

        if ($category !== 'all') {
            $paymentsQuery->whereHas('invoice.feeType', function ($q) use ($category) {
                if ($category === 'SPP') {
                    $q->where('category', 'SPP');
                } else {
                    $q->where('category', '!=', 'SPP');
                }
            });
        }

        if (!empty($search)) {
            $paymentsQuery->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', '%' . $search . '%')
                  ->orWhereHas('invoice.student', function ($q2) use ($search) {
                      $q2->where('name', 'like', '%' . $search . '%')
                         ->orWhere('nis', 'like', '%' . $search . '%');
                  });
            });
        }

        // 2. FILTERED INVOICES QUERY
        $invoicesQuery = \App\Models\Invoice::whereBetween('created_at', [$start, $end]);
        if ($category !== 'all') {
            $invoicesQuery->whereHas('feeType', function ($q) use ($category) {
                if ($category === 'SPP') {
                    $q->where('category', 'SPP');
                } else {
                    $q->where('category', '!=', 'SPP');
                }
            });
        }

        // 3. KPIs
        $totalInflow = (float) (clone $paymentsQuery)->sum('amount');
        $totalInflowCount = (clone $paymentsQuery)->count();

        $totalNewDebt = (float) (clone $invoicesQuery)->where('status', 'unpaid')->sum('amount');
        $totalNewDebtCount = (clone $invoicesQuery)->where('status', 'unpaid')->count();

        $totalInvoicesCount = (clone $invoicesQuery)->where('status', '!=', 'inactive')->count();
        $paidInvoicesCount = (clone $invoicesQuery)->where('status', 'paid')->count();
        $collectionRate = $totalInvoicesCount > 0 ? round(($paidInvoicesCount / $totalInvoicesCount) * 100, 1) : 0;

        // 4. BREAKDOWN KATEGORI
        $invoices = (clone $invoicesQuery)->where('status', '!=', 'inactive')->with('feeType')->get();
        $grouped = $invoices->groupBy(function ($inv) {
            return $inv->feeType ? $inv->feeType->name : 'Lainnya';
        });

        $breakdown = [];
        foreach ($grouped as $categoryName => $items) {
            $catTarget = $items->sum('amount');
            $catPaid = $items->where('status', 'paid')->sum('amount');
            $catUnpaid = $items->where('status', 'unpaid')->sum('amount');
            $catRate = $catTarget > 0 ? round(($catPaid / $catTarget) * 100, 1) : 0;

            $breakdown[] = [
                'category' => $categoryName,
                'target' => $catTarget,
                'paid' => $catPaid,
                'unpaid' => $catUnpaid,
                'rate' => $catRate,
            ];
        }
        usort($breakdown, fn($a, $b) => $b['target'] <=> $a['target']);

        // 5. LEDGER TRANSAKSI (LAMPIRAN)
        $payments = (clone $paymentsQuery)->with(['invoice.student', 'invoice.feeType', 'recorder'])
            ->orderBy('paid_at', 'asc')
            ->get();

        $data = [
            'title' => 'Laporan Keuangan',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalInflow' => $totalInflow,
            'totalInflowCount' => $totalInflowCount,
            'totalNewDebt' => $totalNewDebt,
            'totalNewDebtCount' => $totalNewDebtCount,
            'collectionRate' => $collectionRate,
            'breakdown' => $breakdown,
            'payments' => $payments,
        ];

        $pdf = Pdf::loadView('reports.cashflow', $data);

        return $pdf->download('Laporan-Keuangan-'.$startDate.'-sd-'.$endDate.'.pdf');
    }
}
