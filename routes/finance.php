<?php

use App\Http\Controllers\FinanceReportController;
use App\Livewire\Pages\Finance\FeeTypeCreate;
use App\Livewire\Pages\Finance\FeeTypeEdit;
use App\Livewire\Pages\Finance\FeeTypeShow;
use App\Livewire\Pages\Finance\FinanceHub;
use App\Livewire\Pages\Finance\PaymentManual;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('/', FinanceHub::class)->name('hub');

    // Backward compatibility redirects
    Route::get('spp', fn () => redirect()->route('finance.hub', ['tab' => 'spp']))->name('spp.index');
    Route::get('fee-types', fn () => redirect()->route('finance.hub', ['tab' => 'fees']))->name('fee-types.index');

    Route::get('fee-types/create', FeeTypeCreate::class)->name('fee-types.create');
    Route::get('fee-types/{feeType}', FeeTypeShow::class)->name('fee-types.show');
    Route::get('fee-types/{feeType}/edit', FeeTypeEdit::class)->name('fee-types.edit');

    Route::get('invoices/manual-payment', PaymentManual::class)->name('invoice.manual-payment');

    // Reports
    Route::get('reports/payments/pdf', [FinanceReportController::class, 'exportPaymentsPdf'])->name('reports.payments.pdf');
});
