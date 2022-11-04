<?php


namespace App\Services;


use App\Contracts\Services\PVGisServiceContract;
use Illuminate\Support\Str;
use Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PVGisService extends AbstractService implements PVGisServiceContract
{
    /**
     * Сколько секунда хранить временные файлы
     */
    const TEMP_FILE_TTL = 24 * 60 * 60; //seconds

    /**Загрузка файла данных с pvgis
     * @param $lat
     * @param $lon
     * @return StreamedResponse
     */
    public function exportPvgisData($lat, $lon): StreamedResponse
    {
        $this->removeOldFiles();

        $targetFile = "pv-output-$lat-$lon.csv";
        $targetFilePath = 'app/tmp/' . $targetFile;
        $link = "https://re.jrc.ec.europa.eu/api/seriescalc?lat=$lat&lon=$lon";
        Storage::makeDirectory(Str::before($targetFilePath, $targetFile));
        if (!Storage::fileExists($targetFilePath)) {
            Storage::put($targetFilePath, file_get_contents($link));
        }
        return Storage::download($targetFilePath, $targetFile);
    }

    /**Метод удаления старых временных файлов. Время жизни берется из TEMP_FILE_TTL
     * @return void
     */
    private function removeOldFiles()
    {
        $files = Storage::allFiles('app/tmp');
        foreach ($files as $file) {
            $fileTime = Storage::lastModified($file);
            if (time() - $fileTime > self::TEMP_FILE_TTL) {
                Storage::delete($file);
            }
        }
    }
}
