<?php

namespace LaravelServiceRepositoryGenerator\Providers;

use Illuminate\Support\ServiceProvider;
use LaravelServiceRepositoryGenerator\Commands\MakeServiceRepository;

class ServiceRepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/service-repository.php', 'service-repository');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // Register artisan command
            $this->commands([
                MakeServiceRepository::class,
            ]);

            // Publish config file
            $this->publishes([
                __DIR__ . '/../Config/service-repository.php' => config_path('service-repository.php'),
            ], 'service-repository-config');

            // Publish stub files
            $this->publishes([
                __DIR__ . '/../Stubs/' => base_path('stubs/service-repository-generator/'),
            ], 'service-repository-stubs');
        }
    }
}
