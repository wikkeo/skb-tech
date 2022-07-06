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
    return view('index');
});
Route::post('/list', 'Front\CategoryController@list');
Route::post('/create', 'Front\CategoryController@create');
Route::post('/update', 'Front\CategoryController@update');
Route::post('/delete', 'Front\CategoryController@delete');