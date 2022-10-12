<?php


namespace App\Services;

use App\Contracts\Repositories\SettingRepositoryContract;
use App\Contracts\Services\SettingServiceContract;
use Illuminate\Support\Facades\DB;

class SolarInsolation extends AbstractService implements SettingServiceContract
{
    public function getByUser($userId): array
    {
        return app(SettingRepositoryContract::class)->getByUser($userId);
    }

    public function updateUserSettings($params)
    {
        try {
            DB::beginTransaction();
            foreach ($params['parameters'] as $type => $parameter) {
                foreach ($parameter as $key => $settings) {
                    app(SettingRepositoryContract::class)->updateCreate($params['user_id'], $type, $key, $settings);
                }
            }
            DB::commit();
        }
        catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
