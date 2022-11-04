<?php


namespace App\Http\Controllers\Api;

use App\Contracts\Services\MapDataServiceContract;
use App\Contracts\Services\PVGisServiceContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        return new JsonResponse(app(MapDataServiceContract::class)->getHeatmap($params),);
    }

    public function exportPvgisData(Request $request): StreamedResponse
    {
        return app(PVGisServiceContract::class)->exportPvgisData($request->get('lat'), $request->get('lon'));
    }

    public function getElevation(Request $request): JsonResponse
    {
        return new JsonResponse(['elevation' => app(MapDataServiceContract::class)->getElevation($request->get('lat'), $request->get('lon'))]);
    }
}
