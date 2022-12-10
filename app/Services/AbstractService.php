<?php


namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class AbstractService
{
    protected function makeCollection(mixed $items)
    {
        if ($items instanceof Collection) {
            return $items;
        }

        if (is_array($items)) {
            return collect($items);
        }

        return collect()->add($items);
    }

    public function importFile(string $filePath, array $primaryKey, string $modelClass, array $requiredFields = [], array $adapters = [], ?callable $notify = null): void
    {
        //проверка заголовков
        $resource = Storage::readStream($filePath);
        $headers = fgetcsv($resource);

        //обработка файла
        $row = 1;
        $upsertArray = [];
        $primaryKeys = [];
        while ($values = fgetcsv($resource)) {
            try {
                $row++;

                if ($row % 100 === 0 && $notify) {
                    $notify('Обрабатывается ряд ' . $row);
                }

                if (count($upsertArray) === 100) {
                    $modelClass::upsert($upsertArray, $primaryKey);
                    $upsertArray = [];
                    $primaryKeys = [];
                }

                //если число столбцов не совпадает, то пропускаем
                if (count($values) !== count($headers)) {
                    continue;
                }

                $rowData = [];
                for ($i = 0; $i < count($values); $i++) {
                    $field = Str::lower($headers[$i]);
                    $value = $values[$i];

                    //проверка обязательных полей
                    if (in_array($field, $requiredFields) && $value === '') {
                        continue 2;
                    }

                    //проверка повторов в текущем массиве
                    if (in_array(collect(Arr::only($rowData, $primaryKey))->join(''), $primaryKeys)) {
                        continue 2;
                    }

                    //преобразование полей
                    if (isset($adapters[$field]) && is_callable($adapters[$field])) {
                        $rowData[$field] = $adapters[$field]($value);
                    }
                    else {
                        $rowData[$field] = $value;
                    }

                    $primaryKeys[] = collect(Arr::only($rowData, $primaryKey))->join('');
                }

                $upsertArray[] = $rowData;
            }
            catch (\Exception $e) {
                dd($e, $row);
            }
        }

        $modelClass::upsert($upsertArray, $primaryKey);

    }
}
