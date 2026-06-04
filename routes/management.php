<?php

use App\Livewire\Pages\Admin\RecoveryCenter;
use App\Livewire\Pages\Management\UserCreate;
use App\Livewire\Pages\Management\UserEdit;
use App\Livewire\Pages\Management\UserIndex;
use App\Livewire\Pages\Management\UserShow;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:Super Admin'])->group(function () {
    Route::get('management/users', UserIndex::class)->name('management.users.index');
    Route::get('management/users/create', UserCreate::class)->name('management.users.create');
    Route::get('management/users/{user}', UserShow::class)->name('management.users.show');
    Route::get('management/users/{user}/edit', UserEdit::class)->name('management.users.edit');

    Route::get('management/recovery', RecoveryCenter::class)->name('management.recovery');
});
