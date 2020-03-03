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
    });
