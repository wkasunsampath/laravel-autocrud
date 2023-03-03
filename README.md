# Laravel Autocrud - Automate CRUD operations in Laravel applications.

[![Latest Stable Version](http://poser.pugx.org/wkasunsampath/laravel-autocrud/v)](https://packagist.org/packages/wkasunsampath/laravel-autocrud) [![License](http://poser.pugx.org/wkasunsampath/laravel-autocrud/license)](https://packagist.org/packages/wkasunsampath/laravel-autocrud) [![PHP Version Require](http://poser.pugx.org/wkasunsampath/laravel-autocrud/require/php)](https://packagist.org/packages/wkasunsampath/laravel-autocrud)

When Laravel apps are developed, developers have to write CRUD operations for several models. Some of them are just regular CRUD operations without any special logic. This package is to automate these CRUD operations. Therefore, developers can save most of their development time.

To create a fully working CRUD for User model, just run below command,
```bash
php artisan autocrud:create User
```
All you have to do is that. :) Now your Laravel app have below routes registered. To see routes, run `php artisan route:list`.

| Method | Route       | Route Name   | Operation |
|--------|-------------|--------------|-----------|
| Get    | /users      | users_index  | Index     |
| Get    | /users/{id} | users_view   | View      |
| Post   | /users      | users_create | Create    |
| Put    | /users/{id} | users_update | Update    |
| Delete | /users/{id} | users_delete | Delete    |

Seems interesting? :D Ok!, let's continue.

## Installation

You can install the package via composer: (Currently supports only for Laravel 8, 9 & 10)

```bash
composer require wkasunsampath/laravel-autocrud
```

You can publish assets and config with:

```bash
php artisan autocrud:install
```

All done. Now you can see there is a `autocrud.php` file in config folder. Also, there is a new folder called Autocruds in `\App\Http` folder.

## Usage

Let's assume that there is a model called `Office` at `\App\Models\Company\Office.php`. To create a fully working CRUD for that model, just run, 

```bash
php artisan autocrud:create Company/Office
```
All done. Your CRUD is working now. Not sure? Ok!, just run `php artisan route:list`.


> **_NOTE:_** It is recommanded to maintain the same structure inside Autocruds folder as inside models folder.


Let's go deeper.

### Docs
-   [Autocrud Classes](#autocrud-classes)
-   [Configuration](#configuration)
-   [Folder Structure](#folder-structure)
-   [Index Route](#index-route)
-   [View Route](#view-route)
-   [Create Route](#create-route)
-   [Update Route](#update-route)
-   [Delete Route](#delete-route)
-   [Best Practices](#best-practices)

## Autocrud Classes
Autocrud classes are the files generated when runs `php artisan autocrud:create {class_name}`. Normally, these files are stored in `\App\Http\Autocruds` folder. 

Autocruds classes extend the BaseCrud class. Most important setting of this class is `protected string $model`. You can bind the model which you need to create CRUD here. It is recommanded to use the model name as the Autocrud class name.

```php
<?php

namespace App\Http\Autocruds;

use WKasunSampath\LaravelAutocrud\BaseCrud;

class User extends BaseCrud
{
    /**
     * Set whether CRUD operations are related to API or not.
     */
    protected bool $isApi = true;

    /**
     * Model instance which CRUD operations relate.
     */
    protected string $model = \App\Models\User::class;

    /**
     * Middlewares which are applied to all routes
     *
     * Ex: ['auth:sanctum']
     */
    public function commonMiddlewares(): array
    {
        return [];
    }
}
```
You can set `protected bool $isApi` to `false` if particular CRUD is related to a Laravel Web App. Also, If `protected bool $isApi` is set to true, all CRUD routes will be prefixed with "api/".

Inside `public function commonMiddlewares()`, you can define middlewares which are applied to all CRUD routes.

All autocrud classes must be registered in  `Autocruds.php` file inside `App\Http\Autocruds` folder. However, this process is done automatically when you run `autocrud:create` command.

```php
<?php

namespace App\Http\Autocruds;

use WKasunSampath\LaravelAutocrud\AutocrudRouter;

class Autocruds extends AutocrudRouter
{
    /**
     * Register all autocrud classes here.
     */
    protected array $autocruds = [
        \App\Http\Autocruds\User::class,
    ];
}
```

## Configuration
Autocrud config file contains some useful configuration options for your app. Normally, this file is getting published when you run `php artisan autocrud:install` command.

| Config Option      | Description                                                                                                                                    |
|--------------------|------------------------------------------------------------------------------------------------------------------------------------------------|
| app_type           | Value should be either "api" or "web". If app is primarily designed for APIs it should be "api". It should be "web" otherwise.                 |
| autocrud_file      | Here, you can change the Autocrud folder location and name. When you change anything here, rerun `autocrud:install` command.                   |
| autocrud_setup     | You can set availability and method for each CRUD route here. This global setting can override individually in Autocrud classes for each CRUD. |
| model_namespace    | Models location                                                                                                                                |
| resource_namespace | Resources location                                                                                                                             |
| request_namespace  | Requests location                                                                                                                               |
## Folder Structure
It is recommended to follow below convention when using autocrud library.

```
.
└── Laravel App/
    ├── App/
    │   ├── Http/
    │   │   ├── Autocruds/
    │   │   │   ├── Autocruds.php
    │   │   │   ├── User.php
    │   │   │   └── Company/
    │   │   │       └── Office.php
    │   │   ├── Requests/
    │   │   │   ├── UserCreateRequest.php
    │   │   │   ├── UserUpdateRequest.php
    │   │   │   └── Company/
    │   │   │       ├── OfficeCreateRequest.php
    │   │   │       └── OfficeUpdateRequest.php
    │   │   └── Resources/
    │   │       ├── UserResource.php
    │   │       ├── UserCollection.php
    │   │       └── Company/
    │   │           ├── OfficeResource.php
    │   │           └── OfficeCollection.php
    │   ├── Models/
    │   │   ├── User.php
    │   │   └── Company/
    │   │       └── Office.php
    │   └── Policies/
    │       ├── UserPolicy.php
    │       └── OfficePolicy.php
    ├── config/
    │   └── autocrud.php
    └── resources/
        └── views/
            └── users/
                ├── index.blade.php
                ├── view.blade.php
                ├── create.blade.php
                ├── update.blade.php
                └── delete.blade.php
```
As you may have noticed, creating five different files for CRUD in views folder is little bit abnormal and most of the time it is not neccessary. (This will applicable only in web apps.) When you are creating autocrud classes for a web app, there will be functions to override this behaviour.

There is an easy way to create Model, Controller, Policy, Request, Resources, Migrations & Observers with the Autocrud class by passing some options to the artisan command.

```bash
php artisan autocrud:create Company/Office --m --mg --c --p --req --r --o --s
```

1. --m: Model
2. --mg: Migration
3. --c: Controller
4. --p: Policy
5. --req: Form Request
6. --r: Resource
7. --o: Observer
8. --s: Seeder

This command will create files according to the above folder structure.

## Index Route
There are several useful methods related to index route which you can override inside the autocrud class.

1. `public function indexMiddlewares(): array`: You can add index route specific middlewares here.

2. `public function afterIndexPage(): string`: View to redirect after index operation in web apps, (Not available in APIs.) 

3. `public function makeIndexRoute(): bool`: If this is set to false, index route will not be available for that particular model.

4. `public function indexMethod(): string`: Method for that specific index route. Default is "GET".

5. `public function indexEagerLoad(): array`: If you need to eager load any relationship data with index, add those relationships here.
```php
/**
 * Eager load relationships with index query
 */
public function indexEagerLoad(): array
{
    return ['locations'];
}
```
6. `public function beforeIndex(Builder $query): Builder`: If you need to modify index query, you may do it here. (Don't include `->get()`)

7. `public function afterIndex(Collection $data): JsonResponse | ResourceCollection | View`: Modify fetched index data before send back to the user.

## View Route
There are several useful methods related to view route which you can override inside the autocrud class.

1. `public function viewMiddlewares(): array`: You can add view route specific middlewares here.

2. `public function afterViewPage(): string`: View to redirect after view operation in web apps, (Not available in APIs.)

3. `public function makeViewRoute(): bool`: If this is set to false, view route will not be available for that particular model.

4. `public function viewMethod(): string`: Method for that specific view route. Default is “GET”.

5. `public function viewEagerLoad(): array`: If you need to eager load any relationship data with view, add those relationships here.

6. `public function beforeView(Builder $query): Builder`: If you need to modify view query, you may do it here. (Don’t include ->get())

7. `public function afterView(Model $model): JsonResponse | JsonResource | View`: Modify fetched view data before send back to the user.

## Create Route
There are several useful methods related to create route which you can override inside the autocrud class.

> **_NOTE:_** Form data variable names must be equal to the DB column names.

1. `public function createMiddlewares(): array`: You can add create route specific middlewares here.

2. `public function afterCreatePage(): string`: View to redirect after create operation in web apps, (Not available in APIs.)

3. `public function makeCreateRoute(): bool`: If this is set to false, create route will not be available for that particular model.

4. `public function createMethod(): string`: Method for that specific create route. Default is “POST”.

5. `public function createRequest(): string|null`: Here you can set the name of form request class. Normally, autocrud will get the form request class automatically when you are following the convention in [folder structure](#folder-structure) section. Otherwise you can specifically give that class here.
You can skip form request validation by returning null.
```php
/**
 * Request class name for create requests
 */
public function createRequest(): string|null
{
    return "\App\UserModule\Requests\UserRequest.php";
}
```

6. `public function beforeCreate(array $data): array`: You can change data before save in the DB here.

```php
/**
 * Do things before add data to the DB.
 */
public function beforeCreate(array $data): array
{
    if (array_key_exists('password', $data)) {
        $data['password'] = Hash::make($data['password']);
    }
    
    if (array_key_exists('profile_picture', $data)) {
        //Perform file saving here & get $fileName.
        $data['profile_picture'] = $fileName;
    }

    return $data;
}
```

7. `public function afterCreate(Model $model): JsonResponse | JsonResource | View`: Modify created data before send back to the user.

## Update Route
There are several useful methods related to update route which you can override inside the autocrud class.

> **_NOTE:_** Form data variable names must be equal to the DB column names.

1. `public function updateMiddlewares(): array`: You can add update route specific middlewares here.

2. `public function afterUpdatePage(): string`: View to redirect after update operation in web apps, (Not available in APIs.)

3. `public function makeUpdateRoute(): bool`: If this is set to false, update route will not be available for that particular model.

4. `public function updateMethod(): string`: Method for that specific update route. Default is “PUT”.

5. `public function updateRequest(): string|null`: Here you can set the name of form request class. Normally, autocrud will get the form request class automatically when you are following the convention in [folder structure](#folder-structure) section. Otherwise you can specifically give that class here.
You can skip form request validation by returning null.

6. `public function beforeUpdate(array $data): array`: You can change data before save in the DB here.

7. `public function afterUpdate(Model $model): JsonResponse | JsonResource | View`: Modify created data before send back to the user.

## Delete Route
There are several useful methods related to delete route which you can override inside the autocrud class.

1. `public function deleteMiddlewares(): array`: You can add delete route specific middlewares here.

2. `php public function afterDeletePage(): string`: View to redirect after delete operation in web apps, (Not available in APIs.)

3. `public function makeDeleteRoute(): bool`: If this is set to false, delete route will not be available for that particular model.

4. `public function deleteMethod(): string`: Method for that specific create route. Default is “DELETE”.

5. `public function beforeDelete(Model $model): void`: Do stuff before delete the model.

6. `public function afterDelete(): JsonResponse | View`: Do stuff before sending response to user.

## Best Practices
1. Follow the [forlder structure](#folder-structure) as much as possible. So, you have not to override any function.
2. It is not needed to create Form Request classes, Response classes and Policies for each. Autocrud will use them if they are available. Otherwise it will proceed according to normal Laravel way. But, it is recommanded to user Requests, Responses and Policies as much as possible to avoid security risks.
3. To auto discover the Requests, Responses and Policies by the Autocrud, they should have followed the [forlder structure](#folder-structure) conventions.
4. For create & update requests, incoming form data variable names must be equal to the corresponding DB column name. Otherwise, it will not be identified by the Autocrud.
5. This package is not a total replacement for routes. Still you will have to use typical Laravel routes and controllers when you are performing some complex actions. This package is just a helper package to speed up your development by automating normal CRUD operations. 

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
