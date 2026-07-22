<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$advancePayment = \App\Models\Payment::with('invoice')
    ->whereBetween('paid_at', ['2026-07-01 00:00:00', '2026-07-31 23:59:59'])
    ->where(function($q) {
        $q->where('status', 'success')->orWhereNull('status');
    })
    ->whereHas('invoice', function ($q) {
        $q->where('due_date', '>', '2026-07-31');
    })->sum('amount');

echo "Advance payment: " . $advancePayment . "\n";

$payments = \App\Models\Payment::with('invoice')
    ->whereBetween('paid_at', ['2026-07-01 00:00:00', '2026-07-31 23:59:59'])
    ->where(function($q) {
        $q->where('status', 'success')->orWhereNull('status');
    })
    ->whereHas('invoice', function ($q) {
        $q->where('due_date', '>', '2026-07-31');
    })->get();

foreach($payments as $p) {
    echo "Payment ID: " . $p->id . " Amount: " . $p->amount . " Paid At: " . $p->paid_at . " Invoice Due: " . $p->invoice->due_date . "\n";
}
