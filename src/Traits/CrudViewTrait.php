<?php

namespace WKasunSampath\LaravelAutocrud\Traits;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;

trait CrudViewTrait
{
    /**
     * Middlewares which are applied to view route
     */
    public function viewMiddlewares(): array
    {
        return [];
    }

    /**
     * View to redirect after view operation
     * (This is applicable for web routes only.)
     *
     * Ex: For Users, it will be "users.view". Response will redirect to
     * "resources/views/users/view.blade.php"
     */
    public function afterViewPage(): string
    {
        return $this->routeName().'.view';
    }

    /**
     * Set view route availability (View route will be available if true)
     */
    public function makeViewRoute(): bool
    {
        return config('autocrud.autocrud_setup.view.is_available');
    }

    /**
     * Method of the view route.
     * Ex: get
     */
    public function viewMethod(): string
    {
        return strtolower(config('autocrud.autocrud_setup.view.method'));
    }

    /**
     * Eager load relationships with view query
     */
    public function viewEagerLoad(): array
    {
        return [];
    }

    /**
     * Do things before fetch data from the DB.
     */
    public function beforeView(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Do things before send response.
     */
    public function afterView(Model $model): JsonResponse | JsonResource | View
    {
        if ($this->isApi && ! empty($this->resource())) {
            return new ($this->resource())($model);
        } elseif ($this->isApi) {
            return response()->json($model, JsonResponse::HTTP_OK);
        } else {
            return view($this->afterViewPage(), ['data' => $model]);
        }
    }

    /**
     * View method
     */
    public function view(): Route|array
    {
        if (! $this->makeViewRoute()) {
            return [];
        }

        return $this->getRoute()
            ->{$this->viewMethod()}($this->routeName().'/{id}', function ($id) {
                $resource = $this->beforeView($this->modelInstance->with($this->viewEagerLoad()))
                    ->findOrFail($id);

                if (Auth::check() && Auth::user()->cannot('view', $resource)) {
                    return response()->json(null, JsonResponse::HTTP_NOT_FOUND);
                }

                return $this->afterView($resource);
            })
            ->middleware(array_merge($this->commonMiddlewares(), $this->viewMiddlewares()))
            ->name($this->routeName().'_view');
    }
}
