<?php

namespace WKasunSampath\LaravelAutocrud\Traits;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;

trait CrudIndexTrait
{
    /**
     * Middlewares which are applied to index route
     */
    public function indexMiddlewares(): array
    {
        return [];
    }

    /**
     * View to redirect after index operation
     * (This is applicable for web routes only.)
     *
     * Ex: For Users, it will be "users.index". Response will redirect to
     * "resources/views/users/index.blade.php"
     */
    public function afterIndexPage(): string
    {
        return $this->routeName() . '.index';
    }

    /**
     * Set index route availability (Index route will be available if true)
     */
    public function makeIndexRoute(): bool
    {
        return config('autocrud.autocrud_setup.index.is_available');
    }

    /**
     * Method of the index route.
     * Ex: get
     */
    public function indexMethod(): string
    {
        return strtolower(config('autocrud.autocrud_setup.index.method'));
    }

    /**
     * Eager load relationships with index query
     */
    public function indexEagerLoad(): array
    {
        return [];
    }

    /**
     * Do things before fetch data from the DB.
     */
    public function beforeIndex(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Do things before send response.
     */
    public function afterIndex(Collection $data): JsonResponse | ResourceCollection | View
    {
        if ($this->isApi && !empty($this->resource(true))) {
            return new ($this->resource(true))($data);
        } elseif ($this->isApi) {
            return response()->json($data, JsonResponse::HTTP_OK);
        } else {
            return view($this->afterIndexPage(), ['data' => $data]);
        }
    }

    /**
     * Index method
     */
    public function index(): Route|array
    {
        if (!$this->makeIndexRoute()) {
            return [];
        }

        return $this->getRoute()
            ->{$this->indexMethod()}($this->routeName(), function () {
                if (Auth::check() && Auth::user()->cannot('viewAny', $this->model)) {
                    return response()->json(null, JsonResponse::HTTP_NOT_FOUND);
                }

                if ($this->paginate && is_int($this->paginate)) {
                    $data = $this->beforeIndex($this->modelInstance->with($this->indexEagerLoad()))
                        ->paginate($this->paginate);
                } else {
                    $data = $this->beforeIndex($this->modelInstance->with($this->indexEagerLoad()))->get();
                }

                return $this->afterIndex($data);
            })
            ->middleware(array_merge($this->commonMiddlewares(), $this->indexMiddlewares()))
            ->name($this->routeName() . '_index');
    }
}
