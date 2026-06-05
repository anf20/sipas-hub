<?php

use App\Http\Controllers\ReceiptController;
use App\Livewire\Pages\Parent\Dashboard;
use App\Livewire\Pages\Parent\Help;
use App\Livewire\Pages\Parent\History;
use App\Livewire\Pages\Parent\InvoiceDetail;
use App\Livewire\Pages\Parent\Invoices;
use App\Livewire\Pages\Parent\PaymentSuccess;
use App\Livewire\Pages\Parent\Settings;
use App\Livewire\Pages\Parent\Students;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:Orang Tua'])->group(function () {
    Route::get('/parent/dashboard', Dashboard::class)->name('parent.dashboard');
    Route::get('/parent/invoices', Invoices::class)->name('parent.invoices');
    Route::get('/parent/invoices/{invoice}', InvoiceDetail::class)->name('parent.invoices.show');
    Route::get('/parent/history', History::class)->name('parent.history');
    Route::get('/parent/students', Students::class)->name('parent.students');
    Route::get('/parent/settings', Settings::class)->name('parent.settings');
    Route::get('/parent/help', Help::class)->name('parent.help');

    Route::get('/parent/payments/{payment}/success', PaymentSuccess::class)->name('parent.payments.success');
    Route::get('/parent/payments/{payment}/receipt', [ReceiptController::class, 'download'])->name('parent.payments.receipt');
});
