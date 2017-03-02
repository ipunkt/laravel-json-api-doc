<?php

namespace Ipunkt\LaravelJsonApiDoc;

use Ipunkt\LaravelJsonApiDoc\Commands\GenerateDocCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/api-documentation.php' => config_path('api-documentation.php'),
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