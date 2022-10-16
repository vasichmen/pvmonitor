<?php

use App\Http\Controllers\Api\ApiController;

Route::prefix('data-export')->group(function () {
    Route::get('pvgis', [ApiController::class, 'exportPvgisData']);
});