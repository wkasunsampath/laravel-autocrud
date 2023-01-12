<?php

namespace WKasunSampath\LaravelAutocrud\Traits;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

trait CrudCreateTrait
{
    /**
     * Middlewares which are applied to create route
     */
    public function createMiddlewares(): array
    {
        return [];
    }

    /**
     * View to redirect after create operation
     * (This is applicable for web routes only.)
     *
     * Ex: For Users, it will be "users.create". Response will redirect to
     * "resources/views/users/create.blade.php"
     */
    public function afterCreatePage(): string
    {
        return $this->routeName() . '.create';
    }

    /**
     * Set create route availability (Create route will be available if true)
     */
    public function makeCreateRoute(): bool
    {
        return config('autocrud.autocrud_setup.create.is_available');
    }

    /**
     * Method of the create route.
     * Ex: post
     */
    public function createMethod(): string
    {
        return strtolower(config('autocrud.autocrud_setup.create.method'));
    }

    /**
     * Request class name for create requests
     */
    public function createRequest(): string|null
    {
        return $this->request();
    }

    /**
     * Get validated content (Only if form request class is available)
     */
    public function getValidatedContentForCreate(FormRequest $request): array
    {
        return $request->validated();
    }

    /**
     * Do things before add data to the DB.
     */
    public function beforeCreate(array $data): array
    {
        if (array_key_exists('password', $data)) {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }

    /**
     * Do things before send response.
     */
    public function afterCreate(Model $model): JsonResponse | JsonResource | View
    {
        if ($this->isApi && !empty($this->resource())) {
            return new ($this->resource())($model);
        } elseif ($this->isApi) {
            return response()->json($model, JsonResponse::HTTP_CREATED);
        } else {
            return view($this->afterCreatePage(), ['data' => $model])
                ->with('success', 'Record was created successfully');
        }
    }

    /**
     * Save data to DB
     */
    public function store(array $requestData): Model
    {
        return $this->modelInstance->create($requestData);
    }

    /**
     * Create method
     */
    public function create(): Route|array
    {
        if (!$this->makeCreateRoute()) {
            return [];
        }

        return $this->getRoute()
            ->{$this->createMethod()}($this->routeName(), function () {
                if (Auth::check() && Auth::user()->cannot('create', $this->model)) {
                    return response()->json(null, JsonResponse::HTTP_NOT_FOUND);
                }

                if (!empty($this->createRequest())) {
                    try {
                        $request = app($this->createRequest());
                    } catch (\Illuminate\Validation\ValidationException $ex) {
                        return $this->isApi
                            ? response()->json($ex->getMessage(), JsonResponse::HTTP_FORBIDDEN)
                            : back()->with('error', $ex->getMessage())->withInput();
                    }

                    $requestData = $this->getValidatedContentForCreate($request);
                } else {
                    $request = app(Request::class);
                    $requestData = $request->all();
                }

                $requestData = $this->beforeCreate($requestData);

                return $this->afterCreate($this->store($requestData));
            })
            ->middleware(array_merge($this->commonMiddlewares(), $this->createMiddlewares()))
            ->name($this->routeName() . '_create');
    }
}
