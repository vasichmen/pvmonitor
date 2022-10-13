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

    public function getCountryBounds(): array
    {
        $lats = collect(self::COUNTRY_POLYGON_COORDINATES)->pluck(0);
        $lons = collect(self::COUNTRY_POLYGON_COORDINATES)->pluck(1);
        return [
            [$lats->max(), $lons->min()],
            [$lats->min(), $lons->max()],
        ];
    }
}
