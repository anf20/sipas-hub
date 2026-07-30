<?php

use App\Http\Controllers\FinanceReportController;
use App\Livewire\Pages\Finance\FeeTypeCreate;
use App\Livewire\Pages\Finance\FeeTypeEdit;
use App\Livewire\Pages\Finance\FeeTypeIndex;
use App\Livewire\Pages\Finance\FeeTypeShow;
use App\Livewire\Pages\Finance\FinancialReport;
use App\Livewire\Pages\Finance\PaymentManual;
use App\Livewire\Pages\Finance\SppIndex;
use App\Livewire\Pages\Finance\SppMonthShow;
use App\Livewire\Pages\Finance\WhatsappBlastFee;
use App\Livewire\Pages\Finance\WhatsappBroadcastGeneral;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('finance')->name('finance.')->group(function () {
    // Analytics & Dashboard (Sentralisasi)
    Route::get('/', FinancialReport::class)->name('hub');

    Route::get('spp', SppIndex::class)->name('spp.index');
    Route::get('spp/months/{month}', SppMonthShow::class)->name('spp.months.show');
    Route::get('fee-types', FeeTypeIndex::class)->name('fee-types.index');

    Route::get('fee-types/create', FeeTypeCreate::class)->name('fee-types.create');
    Route::get('fee-types/{feeType}', FeeTypeShow::class)->name('fee-types.show');
    Route::get('fee-types/{feeType}/edit', FeeTypeEdit::class)->name('fee-types.edit');

    Route::get('invoices/manual-payment', PaymentManual::class)->name('invoice.manual-payment');

    // WA Blast
    Route::get('fee-types/{feeType}/whatsapp-blast', WhatsappBlastFee::class)->name('fee-types.whatsapp-blast');
    Route::get('whatsapp-broadcast', WhatsappBroadcastGeneral::class)->name('whatsapp-broadcast.general');

    // Reports
    Route::get('reports/financial/pdf', [FinanceReportController::class, 'exportCashflowPdf'])->name('reports.financial.pdf');
});
