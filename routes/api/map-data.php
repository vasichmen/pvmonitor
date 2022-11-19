<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('map-data')->group(function () {
    Route::get('heatmap', [ApiController::class, 'heatmap']);
    Route::get('point-data', [ApiController::class, 'getPointData']);
});
