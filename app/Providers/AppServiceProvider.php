<?php


namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private array $modelList = [
        'SolarInsolation',
    ];

    private array $serviceList = [
        'MapData',
        'Coordinate',
        'PVGis',
    ];


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerRepositories();
        $this->registerServices();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    private function registerRepositories()
    {
        foreach ($this->modelList as $model) {
            $modelClass = "App\\Models\\$model";
            $repositoryClass = "App\\Repositories\\{$model}Repository";
            $contractClass = "App\\Contracts\\Repositories\\{$model}RepositoryContract";
            $this->app->bind($contractClass, function () use ($modelClass, $repositoryClass) {
                return new $repositoryClass(new $modelClass());
            });
        }
    }

    private function registerServices()
    {
        foreach ($this->serviceList as $service) {
            $serviceClass = "App\\Services\\{$service}Service";
            $contractClass = "App\\Contracts\\Services\\{$service}ServiceContract";
            $this->app->bind($contractClass, function () use ($serviceClass) {
                return new $serviceClass();
            });
        }
    }
}
