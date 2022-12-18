<?php

namespace WKasunSampath\LaravelAutocrud\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use WKasunSampath\LaravelAutocrud\LaravelAutocrudRouteServiceProvider;
use WKasunSampath\LaravelAutocrud\LaravelAutocrudServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'WKasunSampath\\LaravelAutocrud\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelAutocrudServiceProvider::class,
            LaravelAutocrudRouteServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-autocrud_table.php.stub';
        $migration->up();
        */
    }
}
