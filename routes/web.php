<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
Route::get('', function () {
    return view('welcome');
});*/
Route::get('/', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group( ['middleware' => ['auth']], function() {
    Route::resource('users', 'UserController');
    Route::resource('user.projects', 'UserProjectController');
    Route::resource('roles', 'RoleController');
    Route::resource('posts', 'PostController');
	Route::resource('permissions','PermissionController');
	Route::resource('projects', 'ProjectController');
	Route::resource('user.list', 'UserController');
});

foreach (File::allFiles(__DIR__.'/web') as $partial) {
    # require each file
    require $partial->getPathname();
}