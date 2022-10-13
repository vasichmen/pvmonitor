<?php


namespace App\Http\Controllers\Api;

use App\Contracts\Services\SolarInsolationServiceContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function heatmap(Request $request)
    {
        $params = [
            'latitude' => floatval($request->get('latitude')),
            'longitude' => floatval($request->get('longitude')),
            'pixLatitude' => intval($request->get('pixLatitude')),
            'pixLongitude' => intval($request->get('pixLongitude')),
            'bounds' => json_decode($request->get('bounds'), true),
            'full' => $request->get('full') === 'true',
            'diffuse' => $request->get('diffuse') === 'true',
            'direct' => $request->get('direct') === 'true',
        ];

        return new JsonResponse(app(SolarInsolationServiceContract::class)->getHeatmap($params),);
    }
}
