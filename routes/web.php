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

Route::get('/', 'DeviceController@create');
Route::get('device/getList','DeviceController@getList');

Route::post('device/getLocation','DeviceController@getLocation');

\Illuminate\Support\Facades\Route::resource('device', 'DeviceController');



Auth::routes();


