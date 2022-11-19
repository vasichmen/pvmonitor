<?php


namespace App\Repositories;


use App\Contracts\Repositories\SolarInsolationRepositoryContract;
use Illuminate\Support\Collection;

class SolarInsolationRepository extends AbstractRepository implements SolarInsolationRepositoryContract
{
    public function getForHeatmap(array $min, array $max, int $limit, array $insolationParams): Collection
    {
        return $this->model
            ->select([...$insolationParams, ...['lat', 'lon']])
            ->where('lat', '<=', $max['lat'])
            ->where('lon', '<=', $max['lon'])
            ->where('lat', '>=', $min['lat'])
            ->where('lon', '>=', $min['lon'])
            ->take($limit)
//            ->orderBy('random')
            ->inRandomOrder()
            ->get();
    }

    public function getDiapason(float $latFrom, float $latTo, float $lonFrom, float $lonTo)
    {
        return $this->model
            ->where('lat', '<=', $latTo)
            ->where('lon', '<=', $lonTo)
            ->where('lat', '>=', $latFrom)
            ->where('lon', '>=', $lonFrom)
            ->get();
    }
}
