<?php


namespace App\Services;

use App\Contracts\Services\SolarInsolationServiceContract;
use App\Models\SolarInsolation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SolarInsolationService extends AbstractService implements SolarInsolationServiceContract
{

    const LATITUDE_FIELD = 'lat';
    const LONGITUDE_FIELD = 'lon';
    const TOTAL_FIELD = 'total';
    const DIRECT_FIELD = 'direct';

    public function getHeatmap(array $params)
    {
        $point = [
            'x' => 100,
            'y' => 100,
            'value' => 50,
        ];
        $data = [
            $point,
            $point,
        ];

        return [
            'max' => 20,
            'min' => 0,
            'data' => $data,
        ];
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

            if (!isset(array_flip($headers)[self::DIRECT_FIELD])) {
                throw new \Exception('Не найдено поле ' . self::DIRECT_FIELD);
            }
            $directKey = array_flip($headers)[self::DIRECT_FIELD];

            //обработка файла
            $row = 1;
            DB::beginTransaction();
            while ($values = fgetcsv($resource)) {
                $row++;

                if ($row % 100 === 0 && $notify) {
                    $notify('Обрабатывается ряд ' . $row);
                }

                if ($row % 100 === 0) {
                    DB::commit();
                    DB::beginTransaction();
                }

                $latitude = doubleval($values[$latitudeKey]);
                $longitude = doubleval($values[$longitudeKey]);
                $total = doubleval($values[$totalKey] ?? -1);
                $direct = doubleval($values[$directKey] ?? rand(100, 800));

                if ($total < 0 || $direct < 0) {
                    continue;
                }

                SolarInsolation::updateOrCreate(
                    [
                        'lat' => $latitude,
                        'lon' => $longitude,
                    ],
                    [
                        'full' => $total,
                        'direct' => $direct,
                        'diffuse' => $total - $direct,
                    ]);
            }

            DB::commit();
        }
        catch (\Exception $e) {
            dd($e);
        }
    }
}
