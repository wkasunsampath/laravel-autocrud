<?php

namespace WKasunSampath\LaravelAutocrud;

use Illuminate\Support\ServiceProvider;
use WKasunSampath\LaravelAutocrud\Commands\LaravelAutocrudGeneratorCommand;
use WKasunSampath\LaravelAutocrud\Commands\LaravelAutocrudInstallCommand;
use WKasunSampath\LaravelAutocrud\Commands\LaravelAutocrudInstallFilesCommand;

class LaravelAutocrudServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/autocrud.php', 'autocrud');
    }

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                LaravelAutocrudGeneratorCommand::class,
                LaravelAutocrudInstallCommand::class,
                LaravelAutocrudInstallFilesCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/autocrud.php' => config_path('autocrud.php'),
            ], 'config');
        }
    }
}
