<?php

namespace WKasunSampath\LaravelAutocrud;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class LaravelAutocrudRouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->routes(function () {
            if (
                class_exists(config('autocrud.autocrud_file.namespace').'\\'.config('autocrud.autocrud_file.name'))
            ) {
                $autocrudClass = config('autocrud.autocrud_file.namespace').'\\'.config('autocrud.autocrud_file.name');
                (new $autocrudClass())->registerRoutes();
            }
        });
    }
}
