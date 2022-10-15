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
            'height' => intval($request->get('height')),
            'width' => intval($request->get('width')),
            'limit' => intval($request->get('limit')),
            'bounds' => json_decode($request->get('bounds'), true),
            'full' => $request->get('full') === 'true',
            'diffuse' => $request->get('diffuse') === 'true',
            'direct' => $request->get('direct') === 'true',
        ];

        return new JsonResponse(app(SolarInsolationServiceContract::class)->getHeatmap($params),);
    }
}
