<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});

Route::group(['middleware' => 'web'], function () {
	Route::get('/dashboard','Admin\AdminController@index');
	Route::controllers(
		[
		'dashboard/translations'	=>	'Admin\TranslationController',	
		'dashboard/image'			=>	'Admin\ImageController'	
		]
	);
	Route::get('/dashboard/test','Admin\AdminController@test');
    Route::auth();
    Route::get('/','Guest\GuestController@index');
    Route::get('/d','Guest\GuestController@dump');
    Route::get('/home', 'HomeController@index');
});