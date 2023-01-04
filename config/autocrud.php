<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Primary Type of The App
    |--------------------------------------------------------------------------
    |
    | Value should be either "api" or "web". If app is primarily designed for
    | APIs it should be "api". It should be "web" otherwise.
    |
    */
    'app_type' => 'api',

    /*
    |--------------------------------------------------------------------------
    | Autocrud Base File Location
    |--------------------------------------------------------------------------
    |
    | This file is the place where all autocrud classes are getting registered.
    | You are free to store this file anywhere and just update the file location
    | here.
    | This setting will be used when running "php artisan autocrud:install" and
    | "php artisan autocrud:create {name}" commands.
    |
    */
    'autocrud_file' => [
        'name' => 'Autocruds',
        'namespace' => 'App\Http\Autocruds',
    ],

    /*
    |--------------------------------------------------------------------------
    | Setup global CRUD Methods & Availability
    |--------------------------------------------------------------------------
    |
    | If "is_available" is set to false, that type of routes will not be
    | registered for all models. Method is the request method.
    | These settings can be override individually in autocrud classes.
    |
    */
    'autocrud_setup' => [
        'index' => [
            'is_available' => true,
            'method' => 'get',
        ],
        'view' => [
            'is_available' => true,
            'method' => 'get',
        ],
        'create' => [
            'is_available' => true,
            'method' => 'post',
        ],
        'update' => [
            'is_available' => true,
            'method' => 'put',
        ],
        'delete' => [
            'is_available' => true,
            'method' => 'delete',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Models Folder
    |--------------------------------------------------------------------------
    |
    */
    'model_namespace' => 'App\Models',

    /*
    |--------------------------------------------------------------------------
    | Resources Folder
    |--------------------------------------------------------------------------
    |
    */
    'resource_namespace' => 'App\Http\Resources',

    /*
    |--------------------------------------------------------------------------
    | Requests Folder
    |--------------------------------------------------------------------------
    |
    */
    'request_namespace' => 'App\Http\Requests',

    /*
    |--------------------------------------------------------------------------
    | Override the Autocrud BaseCrud class
    |--------------------------------------------------------------------------
    |
    | There can be instances that you need to override the BaseCrud class and add
    | overridden class to all autocrud classes when generate. Here you can register
    | the pathe of custom BaseCrud class you created. (Without leading \)
    |
    */
    'basecrud_path' => 'WKasunSampath\LaravelAutocrud\BaseCrud',
];
