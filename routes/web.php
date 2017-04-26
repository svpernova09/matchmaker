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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');
Route::get('/@{username}', 'ProfilesController@show');
Route::get('/profile/edit', 'ProfilesController@edit');
Route::post('/profile/edit', 'ProfilesController@update');
Route::get('/profile/photos', 'PhotosController@index');
Route::post('/photos', 'PhotosController@store');
Route::delete('/photos/{id}', 'PhotosController@destroy');
