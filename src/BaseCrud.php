<?php

namespace WKasunSampath\LaravelAutocrud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use WKasunSampath\LaravelAutocrud\Traits\CrudCreateTrait;
use WKasunSampath\LaravelAutocrud\Traits\CrudDeleteTrait;
use WKasunSampath\LaravelAutocrud\Traits\CrudIndexTrait;
use WKasunSampath\LaravelAutocrud\Traits\CrudUpdateTrait;
use WKasunSampath\LaravelAutocrud\Traits\CrudViewTrait;
use WKasunSampath\LaravelAutocrud\Traits\HelperMethodsTrait;

class BaseCrud
{
    use CrudIndexTrait,
        CrudViewTrait,
        CrudCreateTrait,
        CrudUpdateTrait,
        CrudDeleteTrait,
        HelperMethodsTrait;

    /**
     * Model class that CRUD operations relate.
     */
    protected string $model;

    /**
     * Set whether CRUD operations are related to API or not.
     */
    protected bool $isApi = true;

    /**
     * Model instance
     */
    protected Model $modelInstance;

    public function __construct()
    {
        $this->modelInstance = new ($this->model)();
    }

    /**
     * Get base route register
     */
    private function getRoute(): RouteRegistrar
    {
        if ($this->isApi) {
            return Route::middleware('api')->prefix('api');
        } else {
            return Route::middleware('web');
        }
    }

    /**
     * Get the name of the model class.
     * Ex: For "Models\LoginHistory", it will be "LoginHistory".
     */
    public function getClassName(): string
    {
        $name = explode('\\', $this->model);

        return array_pop($name);
    }

    /**
     * Get name for route URLs.
     * Ex: For User routes, it will be users. https://localhost:8000/users
     */
    public function routeName(): string
    {
        return Str::plural(Str::snake($this->getClassName()));
    }

    /**
     * Middlewares which are applied to all routes
     *
     * Ex: ['auth:sanctum']
     */
    public function commonMiddlewares(): array
    {
        return [];
    }

    /**
     * Get model resource class
     */
    public function resource(bool $isCollection = false): string|null
    {
        $suffix = $isCollection ? 'Collection' : 'Resource';
        $resource = $this->getClassName().$suffix;

        if (class_exists(config('autocrud.resource_namespace').'\\'.$resource)) {
            return config('autocrud.resource_namespace').'\\'.$resource;
        } elseif (
            class_exists(config('autocrud.resource_namespace').'\\'.$this->getRelativeFolder().$suffix)
        ) {
            return config('autocrud.resource_namespace').'\\'.$this->getRelativeFolder().$suffix;
        }

        return null;
    }

    /**
     * Get model request class
     */
    public function request(bool $isCreate = true): string|null
    {
        $type = $isCreate ? 'Create' : 'Update';
        $request = $this->getClassName().$type.'Request';

        if (class_exists(config('autocrud.request_namespace').'\\'.$request)) {
            return config('autocrud.request_namespace').'\\'.$request;
        } elseif (
            class_exists(config('autocrud.request_namespace').'\\'.$this->getRelativeFolder().$type.'Request')
        ) {
            return config('autocrud.request_namespace').'\\'.$this->getRelativeFolder().$type.'Request';
        }

        return null;
    }
}
