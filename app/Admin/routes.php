<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('/groups/get_max_order/', 'GroupController@getMaxOrder')->name('admin.groups.getMaxOrder');
    $router->resource('groups', GroupController::class);
    $router->get('/categories/get_max_order/', 'CategoryController@getMaxOrder')->name('admin.categories.getMaxOrder');
//    $router->get('/categories/categories', 'CategoryController@index')->name('admin.categories.index');
//    $router->get('/categories/{category}', 'CategoryController@category')->name('admin.categories.category');
//    $router->get('/categories/{category}/edit', 'CategoryController@edit')->name('admin.categories.edit');
//    $router->put('/categories/{category}', 'CategoryController@update')->name('admin.categories.update');
    $router->resource('categories', CategoryController::class);
});

/**
 * redefined route
 */
$attributes = [
    'prefix'     => config('admin.route.prefix'),
    'middleware' => config('admin.route.middleware'),
];
app('router')->group($attributes, function ($router) {

    /* @var \Illuminate\Support\Facades\Route $router */
    $router->namespace('\App\Admin\Controllers')->group(function ($router) {
        /* @var \Illuminate\Routing\Router $router */
        $router->resource('auth/users', 'UserController')->names('admin.auth.users');
    });
});

