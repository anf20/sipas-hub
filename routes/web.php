<?php

use App\Http\Controllers\MidtransCallbackController;
use App\Livewire\Pages\Admin\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::post('/midtrans/callback', [MidtransCallbackController::class, 'handle'])->name('midtrans.callback');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        if (auth()->user()->hasRole('Orang Tua')) {
            return redirect()->route('parent.dashboard');
        }

        return app(Dashboard::class)();
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/academic.php';
require __DIR__.'/finance.php';
require __DIR__.'/management.php';
require __DIR__.'/parent.php';
