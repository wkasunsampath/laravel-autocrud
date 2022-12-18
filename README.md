# Laravel Autocrud - Automate CRUD operations in Laravel applications.

When Laravel apps are developed, developers have to write CRUD operations for several models. Some of them are just regular CRUD operations without any special logic. This package is to automate these CRUD operations. Therefore, developers can save most of their development time.

To create a fully working CRUD for User model, just run below command,

```bash
php artisan autocrud:create User
```

All you have to do is that. :) Now your Laravel app have below routes regitsred. To see routes, run `php artisan route:list`.

| Method | Route       | Route Name   | Operation |
| ------ | ----------- | ------------ | --------- |
| Get    | /users      | users_index  | Index     |
| Get    | /users/{id} | users_view   | View      |
| Post   | /users      | users_create | Create    |
| Put    | /users/{id} | users_update | Update    |
| Delete | /users/{id} | users_delete | Delete    |

Seems interesting? :D Ok!, let's continue.

## Installation

You can install the package via composer:

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

All autocrud classes must be registered in `Autocruds.php` file inside `App\Http\Autocruds` folder. However, this process is done automatically when you run `autocrud:create` command.

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

Autocrud config file contains some usefull configuration options for your app. Normally, this file is getting published when you run `php artisan autocrud:install` command.

| Config Option      | Description                                                                                                                                    |
| ------------------ | ---------------------------------------------------------------------------------------------------------------------------------------------- |
| app_type           | Value should be either "api" or "web". If app is primarily designed for APIs it should be "api". It should be "web" otherwise.                 |
| autocrud_file      | Here, you can change the Autocrud folder location and name. When you change anything here, rerun `autocrud:install` command.                   |
| autocrud_setup     | You can set availability and method for each CRUD route here. This global setting can override individually in Autocrud classes for each CRUD. |
| model_namespace    | Models location                                                                                                                                |
| resource_namespace | Resources location                                                                                                                             |
| request_namespace  | Requests location                                                                                                                              |

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

## Index Route

There are several useful methods related to index route which you can override inside the autocrud class.

`public function indexMiddlewares(): array`: You can add index route specific middlewares here.
`public function afterIndexPage(): string`: View to redirect after index operation in web apps, (Not available in APIs.)
`public function makeIndexRoute(): bool`: If this is set to false, index route will not be available for that particular model.
`public function indexMethod(): string`: Method for that specific index route. Default is "GET".
`public function indexEagerLoad(): array`: If you need to eager load any relationship data with index, add those relationships here.

```php
/**
 * Eager load relationships with index query
 */
public function indexEagerLoad(): array
{
    return ['locations'];
}
```

`public function beforeIndex(Builder $query): Builder`: If you need to modify inde query, you may do it here. (Don't include `->get()`)
`public function afterIndex(Collection $data): mixed`: Modify fetched index data before send back to the user.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
