<?php


namespace App\Services;

use App\Contracts\Services\CoordinateServiceContract;

class CoordinateService extends AbstractService implements CoordinateServiceContract
{

    public function getCountryPolygonCoordinates(): array
    {
        return [
            self::COUNTRY_POLYGON_COORDINATES,
            [],
        ];
    }
}
