<?php

/*
|--------------------------------------------------------------------------
| Generic Reports Routes
|--------------------------------------------------------------------------
|
| All reports shall be registered within their respective reprots-* domain file
|
*/

$settings = [
    'middleware' => 'auth',
    'prefix'     => 'reports',
    'namespace'  => 'Reports',
];

Route::group($settings, function($route){
    $route->get('/', 'HomeController@index')->name('reports');
});
