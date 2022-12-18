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

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
