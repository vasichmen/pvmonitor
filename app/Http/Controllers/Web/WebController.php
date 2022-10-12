<?php


namespace App\Http\Controllers\Web;

use App\Contracts\Services\CoordinateServiceContract;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class WebController extends Controller
{
    public function mainPage()
    {
        $mapInitCoordinates = [41, 75];
        $mapInitZoom = 6;
        $countryPolygonCoordinates = app(CoordinateServiceContract::class)->getCountryPolygonCoordinates();
        return Inertia::render('MainPage', compact('mapInitCoordinates', 'countryPolygonCoordinates', 'mapInitZoom'));
    }
}