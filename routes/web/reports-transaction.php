<?php

/*
|--------------------------------------------------------------------------
| Conference Reports Routes
|--------------------------------------------------------------------------
|
| This file defines what routes should handle program management
|
*/

$settings = [
    'middleware' => 'auth',
    'prefix'     => 'reports/transaction',
    'namespace'  => 'Reports\Transaction',
    'as'         => 'reports.transaction.',
];

Route::group($settings, function($route){
    # Report Methods will
    # index: display the filter form and list report history
    # store: create new report and send it back to browser
    $reportMethods = ['only'=>array('index', 'store')];

    $route->resource('list-report', 'TransactionListController', $reportMethods);

    $route->get('/', 'IndexController');
});
