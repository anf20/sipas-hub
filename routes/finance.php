<?php

use App\Http\Controllers\FinanceReportController;
use App\Livewire\Pages\Finance\FeeTypeCreate;
use App\Livewire\Pages\Finance\FeeTypeEdit;
use App\Livewire\Pages\Finance\FeeTypeShow;
use App\Livewire\Pages\Finance\FinanceHub;
use App\Livewire\Pages\Finance\PaymentManual;
use App\Livewire\Pages\Finance\SppMonthShow;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('/', FinanceHub::class)->name('hub');

    Route::get('spp', \App\Livewire\Pages\Finance\SppIndex::class)->name('spp.index');
    Route::get('spp/months/{month}', SppMonthShow::class)->name('spp.months.show');
    Route::get('fee-types', \App\Livewire\Pages\Finance\FeeTypeIndex::class)->name('fee-types.index');

    Route::get('fee-types/create', FeeTypeCreate::class)->name('fee-types.create');
    Route::get('fee-types/{feeType}', FeeTypeShow::class)->name('fee-types.show');
    Route::get('fee-types/{feeType}/edit', FeeTypeEdit::class)->name('fee-types.edit');

    Route::get('invoices/manual-payment', PaymentManual::class)->name('invoice.manual-payment');
    
    // WA Blast
    Route::get('fee-types/{feeType}/whatsapp-blast', \App\Livewire\Pages\Finance\WhatsappBlastFee::class)->name('fee-types.whatsapp-blast');

    // Reports
    Route::get('reports/payments/pdf', [FinanceReportController::class, 'exportPaymentsPdf'])->name('reports.payments.pdf');
});
