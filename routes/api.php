<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/posts')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/', [ApiController::class, 'index']);
    Route::post('/', [ApiController::class, 'store']);
    Route::put('/{id}', [ApiController::class, 'store'])->whereNumber('id');
    Route::put('/{id}/translations/{locale}', [ApiController::class, 'translate']);
    Route::post('/{id}/translations/{locale}/publish', [ApiController::class, 'publish']);
});
