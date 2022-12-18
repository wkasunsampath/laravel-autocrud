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

Let's assume that there is a model called `Company` at `\App\Models\Company.php`. To create a fully working CRUD for that model, just run,

```bash
php artisan autocrud:create Company
```

All done. Your CRUD is working now. Not sure? Ok!, just run `php artisan route:list`.

Let's go deeper.

### Docs

-   [Autocrud Classes](#autocrud-classes)
-   [Configuration](#configuration)
-   [Folder Structure](#generators)
-   [Index Route](#translations)
-   [View Route](#slugs)
-   [Create Route](#usage)
-   [Update Route](#functions)
-   [Delete Route](#exceptions)
-   [Best Practices](#laravel-compatibility)

## Autocrud Classes

Autocrud classes are the files generated when runs `php artisan autocrud:create {class_name}`. Normally, these files are generated in `\App\Http\Autocruds` folder.

Autocruds classes extend the BaseCrud class. Most important setting of this class is `protected string $model`. You can bind the model which you need to create CRUD here. It is recommanded to use the model name as the Autocrud class name.

```
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

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
