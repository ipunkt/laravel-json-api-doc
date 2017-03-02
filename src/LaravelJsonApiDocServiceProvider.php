<?php

namespace Ipunkt\LaravelJsonApiDoc;

use Illuminate\Support\ServiceProvider;
use Ipunkt\LaravelJsonApiDoc\Commands\GenerateDocCommand;

class LaravelJsonApiDocServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $configFile = realpath(__DIR__ . '/../config/api-documentation.php');

        $this->mergeConfigFrom($configFile, 'json-api-doc');
        $this->publishes([
            $configFile => config_path('api-documentation.php'),
        ]);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-json-api-doc');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(GenerateDocCommand::class);
    }
}