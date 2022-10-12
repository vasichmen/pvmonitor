<?php

use App\Http\Controllers\Web\WebController;
use Illuminate\Support\Facades\Route;

Route::group([],
    function () {
        Route::get('/', [WebController::class, 'mainPage']);
    }
);

