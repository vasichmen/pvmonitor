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

    /**перевод георграфических координат в пиксельные относительно левого верхнего угла карты
     * @param  float  $lat     широта точки наблюдения
     * @param  float  $lon     долгота точки наблюлдения
     * @param  array  $params  параметры запроса с фронта (границы карты и координаты в глобальных пикселах)
     * @return array
     */
    public function getLocalPixelCoordinates(float $lat, float $lon, array $params): array
    {
        //размеры карты в градусах
        $heightDeg = $params['bounds']['max']['lat'] - $params['bounds']['min']['lat'];
        $widthDeg = $params['bounds']['max']['lon'] - $params['bounds']['min']['lon'];

        //пикселей в одном градусе по широте
        $pixIn1DegLat = $params['height'] / $heightDeg;
        $pixIn1DegLon = $params['width'] / $widthDeg;

        //географические координаты верхнего левого угла карты
        $topLeft = [
            'lat' => $params['bounds']['max']['lat'],
            'lon' => $params['bounds']['min']['lon'],
        ];

        //расстояния от верхнего левого угла карты до точки в грудусах
        $latDiffDeg = $topLeft['lat'] - $lat;
        $lonDiffDeg = $lon - $topLeft['lon'];

        //расстояния в локальных пикселях до точки от левого верхнего угла карты
        $x = $pixIn1DegLon * $lonDiffDeg;
        $y = $pixIn1DegLat * $latDiffDeg;

        //проверка: результат не должен выходить за рамки высоты и ширины карты
        if ($x > $params['width'] || $y > $params['height']) {
            throw new \Exception('Ошибка в рассчетах пиксельных координат');
        }

        return [
            'x' => intval(round($x)), //расстояние по горизонтали от верхнего левого угла по OX
            'y' => intval(round($y)), //расстояние по горизонтали от верхнего левого угла по OY
        ];
    }
}
