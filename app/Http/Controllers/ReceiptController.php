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

        if (! in_array($payment->invoice->student_id, $studentIds) && ! $user->hasRole('Super Admin')) {
            abort(403);
        }

        $payment->load(['invoice.student', 'invoice.feeType', 'recorder']);

        $data = [
            'payment' => $payment,
            'title' => 'Kwitansi Pembayaran Resmi',
            'date' => date('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('reports.receipt', $data);

        // Set paper size for receipt (e.g., A5 or half A4)
        $pdf->setPaper('a5', 'landscape');

        return $pdf->stream('kwitansi-'.$payment->receipt_number.'.pdf');
    }
}
