<?php


namespace App\Services;

use App\Contracts\Repositories\SolarInsolationRepositoryContract;
use App\Contracts\Services\CoordinateServiceContract;
use App\Contracts\Services\MapDataServiceContract;
use App\Enums\SolarParamTypeEnum;
use App\Models\SolarInsolation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class MapDataService extends AbstractService implements MapDataServiceContract
{

    //,lat,lon,full,full_optimal,direct,diffuse,altitude,created_at,updated_at,elevation_pvgis,winter_solstice,summer_solstice,horizon_profile
    const LATITUDE_FIELD = 'lat';
    const LONGITUDE_FIELD = 'lon';
    const TOTAL_FIELD = 'full';
    const DIRECT_FIELD = 'direct';
    const DIFFUSE_FIELD = 'diffuse';
    const TOTAL_OPTIMAL_FIELD = 'full_optimal';
    const ALTITUDE_FIELD = 'elevation_pvgis';
    const WINTER_SOLSTICE = 'winter_solstice';
    const SUMMER_SOLSTICE = 'summer_solstice';
    const HORIZON_PROFILE = 'horizon_profile';


    const IMPORT_FILE_FIELDS = [
        self::LATITUDE_FIELD,
        self::LONGITUDE_FIELD,
        self::TOTAL_FIELD,
        self::TOTAL_OPTIMAL_FIELD,
        self::DIRECT_FIELD,
        self::DIFFUSE_FIELD,
        self::ALTITUDE_FIELD,
        self::WINTER_SOLSTICE,
        self::SUMMER_SOLSTICE,
        self::HORIZON_PROFILE,
    ];

    public function getHeatmap(array $params)
    {
        $insolationParams = $this->getInsolationParams($params);
        $items = app(SolarInsolationRepositoryContract::class)->getForHeatmap($params['bounds']['min'], $params['bounds']['max'], $params['limit'], $insolationParams);

        $data = $items->map(function ($item) use ($params, $insolationParams) {
            $value = $this->getValueFromItem($item, $insolationParams);
            $coordinates = app(CoordinateServiceContract::class)->getLocalPixelCoordinates($item->lat, $item->lon, $params);
            return [
                ...$coordinates,
                'value' => $value,
            ];
        });

        $heatmapDiapason = $this->getHeatmapDiapason($items, $insolationParams);

        return [
            ...$heatmapDiapason,
            'data' => $data,
        ];
    }

    /**получение списка параметров в БД по запросу с фронта
     * @param  array  $params
     * @return array
     */
    private function getInsolationParams(array $params): array
    {
        $fields[] = $params[SolarParamTypeEnum::Full->value] ? SolarParamTypeEnum::Full->value : null;
        $fields[] = $params[SolarParamTypeEnum::Direct->value] ? SolarParamTypeEnum::Direct->value : null;
        $fields[] = $params[SolarParamTypeEnum::Diffuse->value] ? SolarParamTypeEnum::Diffuse->value : null;
        return collect($fields)->filter()->values()->toArray();
    }

    /**Получение динамического диапазона для тепловой карты. Поиск максимального и минимального значения в кВт/м2 в выборке
     * @param  Collection  $items
     * @param  array       $solarParams
     * @return array
     */
    private function getHeatmapDiapason(Collection $items, array $solarParams): array
    {
        $powers = $items->map(function ($item) use ($solarParams) {
            return $this->getValueFromItem($item, $solarParams);
        });
        return
            [
                'min' => $powers->min(),
                'max' => $powers->max(),
            ];
    }

    /**получение значения из записи в БД для вывода на тепловую карту, кВт/м2
     * @param  SolarInsolation  $item
     * @param  array            $solarParams
     * @return float|int
     */
    private function getValueFromItem(SolarInsolation $item, array $solarParams): float|int
    {
        if (in_array(SolarParamTypeEnum::Full->value, $solarParams)) {
            return +$item->full;
        }
        $result = 0;
        if (in_array(SolarParamTypeEnum::Diffuse->value, $solarParams)) {
            $result += +$item->diffuse;
        }
        if (in_array(SolarParamTypeEnum::Direct->value, $solarParams)) {
            $result += +$item->direct;
        }
        return $result;
    }


    /**Возвращает данные в заданной точке (ближайшие к заданной)
     * @param  float  $lat
     * @param  float  $lon
     * @return SolarInsolation|null
     */
    public function getPointData(float $lat, float $lon): ?SolarInsolation
    {
        $latFrom = round($lat, 2) - 0.02;
        $latTo = round($lat, 2) + 0.02;
        $lonFrom = round($lon, 2) - 0.02;
        $lonTo = round($lon, 2) + 0.02;

        $data = app(SolarInsolationRepositoryContract::class)->getDiapason($latFrom, $latTo, $lonFrom, $lonTo);

        return app(CoordinateServiceContract::class)->getNearestPoint($data, $lat, $lon);
    }

    public function importFile(string $filePath, ?callable $notify = null)
    {
        try {
            //проверка заголовков
            $resource = Storage::readStream($filePath);
            $headers = fgetcsv($resource);

            //проверка полей в файле
            $headerKeys = array_flip($headers);
            foreach (self::IMPORT_FILE_FIELDS as $field) {
                if (!isset($headerKeys[$field])) {
                    throw new \Exception('Не найдено поле ' . $field);
                }
            }

            $latitudeKey = $headerKeys[self::LATITUDE_FIELD];
            $longitudeKey = $headerKeys[self::LONGITUDE_FIELD];
            $totalKey = $headerKeys[self::TOTAL_FIELD];
            $totalOptimalKey = $headerKeys[self::TOTAL_OPTIMAL_FIELD];
            $directKey = $headerKeys[self::DIRECT_FIELD];
            $diffuseKey = $headerKeys[self::DIFFUSE_FIELD];
            $altitudeKey = $headerKeys[self::ALTITUDE_FIELD];
            $winterSolsticeKey = $headerKeys[self::WINTER_SOLSTICE];
            $summerSolsticeKey = $headerKeys[self::SUMMER_SOLSTICE];
            $horizonProfileKey = $headerKeys[self::HORIZON_PROFILE];

            //обработка файла
            $row = 1;
            $upsertArray = [];
            while ($values = fgetcsv($resource)) {
                $row++;

                if ($row % 100 === 0 && $notify) {
                    $notify('Обрабатывается ряд ' . $row);
                }

                if (count($upsertArray) === 100) {
                    SolarInsolation::upsert($upsertArray, ['lat', 'lon']);
                    $upsertArray = [];
                }

                $latitude = doubleval($values[$latitudeKey]);
                $longitude = doubleval($values[$longitudeKey]);
                $total = doubleval($values[$totalKey] ?? -999);
                $totalOptimal = doubleval($values[$totalOptimalKey] ?? -999);
                $direct = doubleval($values[$directKey] ?? -999);
                $diffuse = doubleval($values[$diffuseKey] ?? -999);
                $altitude = doubleval($values[$altitudeKey] ?? -999);
                $winterSolstice = str_replace('\'', '"', $values[$winterSolsticeKey] ?? '[]');
                $summerSolstice = str_replace('\'', '"', $values[$summerSolsticeKey] ?? '[]');
                $horizonProfile = str_replace('\'', '"', $values[$horizonProfileKey] ?? '[]');


                if ($total < 0 || $direct < 0 || $totalOptimal < 0 || $diffuse < 0) {
                    continue;
                }

                $upsertArray[] = [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'full' => $total,
                    'full_optimal' => $totalOptimal,
                    'direct' => $direct,
                    'diffuse' => $diffuse,
                    'altitude' => $altitude,
                    'winter_solstice' => $winterSolstice,
                    'summer_solstice' => $summerSolstice,
                    'horizon_profile' => $horizonProfile,
                    'random' => random_int(0, 2147483647),
                ];

            }

            SolarInsolation::upsert($upsertArray, ['lat', 'lon']);
        }
        catch (\Exception $e) {
            dd($e);
        }
    }
}
