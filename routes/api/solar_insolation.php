<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

;
Route::prefix('solar-insolation')->group(function () {
    Route::get('heatmap', [ApiController::class, 'heatmap']);
});
