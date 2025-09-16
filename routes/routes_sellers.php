<?php

use App\Http\Controllers\SellersController;

Route::middleware(['auth'])->group(function () {
    Route::get('sellers/commissions', [SellersController::class, 'commissions'])->name('sellers.commissions');
    Route::resource('sellers', SellersController::class);
});
