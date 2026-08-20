<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function download(Payment $payment)
    {
        // Security: Ensure parent can only download receipts for their students
        $user = Auth::user();
        $studentIds = $user->students->pluck('id')->toArray();

        if (! in_array($payment->invoice->student_id, $studentIds) && ! $user->hasAnyRole(['Super Admin', 'Admin Keuangan', 'Admin Akademik'])) {
            abort(403);
        }

        $payment->load(['invoice.student.schoolClass', 'invoice.student.parent', 'invoice.feeType', 'recorder']);

        $data = [
            'payment' => $payment,
            'title' => 'Kwitansi Pembayaran - Pesantren Modern As-Sakienah',
            'date' => $payment->paid_at ? $payment->paid_at->format('d / m / Y') : date('d / m / Y'),
        ];

        $pdf = Pdf::loadView('reports.receipt', $data);

        // Set paper size for receipt (A5 Landscape)
        $pdf->setPaper('a5', 'landscape');

        return $pdf->stream('kwitansi-'.$payment->receipt_number.'.pdf');
    }
}
