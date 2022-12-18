<?php

namespace WKasunSampath\LaravelAutocrud\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

trait CrudUpdateTrait
{
    /**
     * Middlewares which are applied to update route
     */
    public function updateMiddlewares(): array
    {
        return [];
    }

    /**
     * View to redirect after update operation
     * (This is applicable for web routes only.)
     *
     * Ex: For Users, it will be "users.update". Response will redirect to
     * "resources/views/users/update.blade.php"
     */
    public function afterUpdatePage(): string
    {
        return $this->routeName().'.update';
    }

    /**
     * Set update route availability (Update route will be available if true)
     */
    public function makeUpdateRoute(): bool
    {
        return config('autocrud.autocrud_setup.update.is_available');
    }

    /**
     * Method of the update route.
     * Ex: put
     */
    public function updateMethod(): string
    {
        return strtolower(config('autocrud.autocrud_setup.update.method'));
    }

    /**
     * Request class name for update requests
     */
    public function updateRequest(): string|null
    {
        return $this->request(false);
    }

    /**
     * Do things before update data to the DB.
     */
    public function beforeUpdate(array $data): array
    {
        if (array_key_exists('password', $data)) {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }

    /**
     * Do things before send response.
     */
    public function afterUpdate(Model $model): mixed
    {
        if ($this->isApi && ! empty($this->resource())) {
            return new ($this->resource())($model);
        } elseif ($this->isApi) {
            return response()->json($model, JsonResponse::HTTP_OK);
        } else {
            return view($this->afterUpdatePage(), ['data' => $model])
                ->with('success', 'Record was updated successfully');
        }
    }

    /**
     * Update method
     */
    public function update(): Route|array
    {
        if (! $this->makeUpdateRoute()) {
            return [];
        }

        return $this->getRoute()
            ->{$this->updateMethod()}($this->routeName().'/{id}', function ($id) {
                $resource = $this->modelInstance->findOrFail($id);

                if (Auth::check() && Auth::user()->cannot('update', $resource)) {
                    return response()->json(null, JsonResponse::HTTP_NOT_FOUND);
                }

                if (! empty($this->updateRequest())) {
                    try {
                        $request = app($this->updateRequest());
                    } catch (\Illuminate\Validation\ValidationException $ex) {
                        return $this->isApi
                            ? response()->json($ex->getMessage(), JsonResponse::HTTP_FORBIDDEN)
                            : back()->with('error', $ex->getMessage())->withInput();
                    }

                    $requestData = $request->validated();
                } else {
                    $request = app(Request::class);
                    $requestData = $request->all();
                }

                $requestData = $this->beforeUpdate($requestData);
                $resource->update($requestData);

                return $this->afterUpdate($resource);
            })
            ->middleware(array_merge($this->commonMiddlewares(), $this->updateMiddlewares()))
            ->name($this->routeName().'_update');
    }
}
