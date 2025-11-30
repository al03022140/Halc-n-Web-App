<?php

use App\Http\Controllers\Api\OrderStatusController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function () {
    Route::post('/orders/track', OrderStatusController::class)->name('api.orders.track');
});
