<?php

namespace WKasunSampath\LaravelAutocrud\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;

trait CrudDeleteTrait
{
    /**
     * Middlewares which are applied to delete route
     */
    public function deleteMiddlewares(): array
    {
        return [];
    }

    /**
     * View to redirect after delete operation
     * (This is applicable for web routes only.)
     *
     * Ex: For Users, it will be "users.delete". Response will redirect to
     * "resources/views/users/delete.blade.php"
     */
    public function afterDeletePage(): string
    {
        return $this->routeName().'.delete';
    }

    /**
     * Set delete route availability (Delete route will be available if true)
     */
    public function makeDeleteRoute(): bool
    {
        return config('autocrud.autocrud_setup.delete.is_available');
    }

    /**
     * Method of the delete route.
     * Ex: delete
     */
    public function deleteMethod(): string
    {
        return strtolower(config('autocrud.autocrud_setup.delete.method'));
    }

    /**
     * Do things before delete data from the DB.
     */
    public function beforeDelete(Model $model): void
    {
        //
    }

    /**
     * Do things before send response.
     */
    public function afterDelete(): mixed
    {
        if ($this->isApi) {
            return response()->json(null, JsonResponse::HTTP_OK);
        } else {
            return view($this->afterDeletePage())->with('success', 'Record was deleted successfully');
        }
    }

    /**
     * Delete method
     */
    public function delete(): Route|array
    {
        if (! $this->makeDeleteRoute()) {
            return [];
        }

        return $this->getRoute()
            ->{$this->deleteMethod()}($this->routeName().'/{id}', function ($id) {
                $resource = $this->modelInstance->findOrFail($id);

                if (Auth::check() && Auth::user()->cannot('delete', $resource)) {
                    return response()->json(null, JsonResponse::HTTP_NOT_FOUND);
                }

                $this->beforeDelete($resource);
                $resource->delete();

                return $this->afterDelete();
            })
            ->middleware(array_merge($this->commonMiddlewares(), $this->deleteMiddlewares()))
            ->name($this->routeName().'_delete');
    }
}
