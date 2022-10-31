<?php


namespace App\Services;

use App\Contracts\Repositories\SolarInsolationRepositoryContract;
use App\Contracts\Services\CoordinateServiceContract;
use App\Contracts\Services\SolarInsolationServiceContract;
use App\Enums\SolarParamTypeEnum;
use App\Models\SolarInsolation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class SolarInsolationService extends AbstractService implements SolarInsolationServiceContract
{

    const LATITUDE_FIELD = 'lat';
    const LONGITUDE_FIELD = 'lon';
    const TOTAL_FIELD = 'ghi';
    const DIRECT_FIELD = 'dni';
    const DIFFUSE_FIELD = 'dif';
    const TOTAL_OPTIMAL_FIELD = 'gti';
    const ALTITUDE_FIELD = 'alt';

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

    public function importFile(string $filePath, ?callable $notify = null)
    {
        try {
            //проверка заголовков
            $resource = Storage::readStream($filePath);
            $headers = fgetcsv($resource);

            if (!isset(array_flip($headers)[self::LATITUDE_FIELD])) {
                throw new \Exception('Не найдено поле ' . self::LATITUDE_FIELD);
            }
            $latitudeKey = array_flip($headers)[self::LATITUDE_FIELD];


            if (!isset(array_flip($headers)[self::LONGITUDE_FIELD])) {
                throw new \Exception('Не найдено поле ' . self::LONGITUDE_FIELD);
            }
            $longitudeKey = array_flip($headers)[self::LONGITUDE_FIELD];

            if (!isset(array_flip($headers)[self::TOTAL_FIELD])) {
                throw new \Exception('Не найдено поле ' . self::TOTAL_FIELD);
            }
            $totalKey = array_flip($headers)[self::TOTAL_FIELD];

            if (!isset(array_flip($headers)[self::TOTAL_OPTIMAL_FIELD])) {
                throw new \Exception('Не найдено поле ' . self::TOTAL_OPTIMAL_FIELD);
            }
            $totalOptimalKey = array_flip($headers)[self::TOTAL_OPTIMAL_FIELD];

            if (!isset(array_flip($headers)[self::DIRECT_FIELD])) {
                throw new \Exception('Не найдено поле ' . self::DIRECT_FIELD);
            }
            $directKey = array_flip($headers)[self::DIRECT_FIELD];

            if (!isset(array_flip($headers)[self::DIFFUSE_FIELD])) {
                throw new \Exception('Не найдено поле ' . self::DIFFUSE_FIELD);
            }
            $diffuseKey = array_flip($headers)[self::DIFFUSE_FIELD];

            if (!isset(array_flip($headers)[self::ALTITUDE_FIELD])) {
                throw new \Exception('Не найдено поле ' . self::ALTITUDE_FIELD);
            }
            $altitudeKey = array_flip($headers)[self::ALTITUDE_FIELD];

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
                $total = doubleval($values[$totalKey] ?? -1);
                $totalOptimal = doubleval($values[$totalOptimalKey] ?? -1);
                $direct = doubleval($values[$directKey] ?? -1);
                $diffuse = doubleval($values[$diffuseKey] ?? -1);
                $altitude = doubleval($values[$altitudeKey] ?? -1);

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
                ];
            }

            SolarInsolation::upsert($upsertArray, ['lat', 'lon']);
        }
        catch (\Exception $e) {
            dd($e);
        }
    }
}
