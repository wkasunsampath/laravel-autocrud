<?php

namespace WKasunSampath\LaravelAutocrud;

class AutocrudRouter
{
    /**
     * Register all autocrud classes here.
     */
    protected array $autocruds = [];

    /**
     * Register all autocrud routes in app
     */
    public function registerRoutes(): array
    {
        $routes = [];

        foreach ($this->autocruds as $autocrud) {
            $autocrudInstance = new $autocrud();

            array_push(
                $routes,
                $autocrudInstance->index(),
                $autocrudInstance->view(),
                $autocrudInstance->create(),
                $autocrudInstance->update(),
                $autocrudInstance->delete(),
            );
        }

        return $routes;
    }
}
