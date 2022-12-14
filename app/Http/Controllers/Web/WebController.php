<?php


namespace App\Http\Controllers\Web;

use App\Contracts\Services\CoordinateServiceContract;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class WebController extends Controller
{
    public function mainPage()
    {
        $countryPolygonCoordinates = app(CoordinateServiceContract::class)->getCountryPolygonCoordinates();
        $mapBounds = app(CoordinateServiceContract::class)->getCountryBounds();
        return Inertia::render('MainPage', compact(
            'countryPolygonCoordinates',
            'mapBounds',
        ));
    }
}