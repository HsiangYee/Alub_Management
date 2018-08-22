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

Route::post('/account', 'AccountController@create');
Route::get('/account/{accountID}', 'AccountController@show');

Route::post('/album', 'AlbumController@create');
Route::post('/album/{albumID}/image', 'AlbumController@upload');
Route::patch('/album/{albumID}', 'AlbumController@update');
Route::get('/album/{albumID}', 'AlbumController@info');
Route::get('/i/{imageID}{imageSuffix}.jpg', 'AlbumController@image');
//Route::patch('/album/{albumID}/images/{imageID}', 'AlbumController@update_image');
Route::post('/internal/move-image', 'AlbumController@move');
Route::get('/album/{albumID}/latest', 'AlbumController@latest');

Route::delete('/album/{albumID}/images/{imageID}', 'AlbumController@delete_image');
Route::post('/internal/undelete-image', 'AlbumController@recovery');

Route::get('/test', 'AlbumController@test');