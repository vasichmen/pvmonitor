<?php


namespace App\Services;


use App\Contracts\Services\PVGisServiceContract;
use Illuminate\Support\Str;
use Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PVGisService extends AbstractService implements PVGisServiceContract
{
    public function exportPvgisData($lat, $lon): StreamedResponse
    {
        $targetFile = "pv-output-$lat-$lon.csv";
        $targetFilePath = 'app/tmp/' . $targetFile;
        $link = "https://re.jrc.ec.europa.eu/api/seriescalc?lat=$lat&lon=$lon";
        Storage::makeDirectory(Str::before($targetFilePath, $targetFile));
        if (!Storage::fileExists($targetFilePath)) {
            Storage::put($targetFilePath, file_get_contents($link));
        }
        return Storage::download($targetFilePath, $targetFile);
    }
}
