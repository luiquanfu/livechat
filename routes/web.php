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

Route::get('', 'Website\Home@index');

Route::get('admin', 'Admin\Home@index');
Route::post('admin/initialize', 'Admin\Home@initialize');
Route::post('admin/login', 'Admin\Home@login');
Route::post('admin/logout', 'Admin\Home@logout');
Route::post('admin/test', 'Admin\Home@test');

Route::post('admin/chat_display/listing', 'Admin\ChatDisplay@listing');
Route::post('admin/chat_display/add', 'Admin\ChatDisplay@add');
Route::post('admin/chat_display/edit', 'Admin\ChatDisplay@edit');
Route::post('admin/chat_display/update', 'Admin\ChatDisplay@update');
Route::post('admin/chat_display/destroy', 'Admin\ChatDisplay@destroy');

Route::post('admin/website/listing', 'Admin\Website@listing');
Route::post('admin/website/add', 'Admin\Website@add');
Route::post('admin/website/edit', 'Admin\Website@edit');
Route::post('admin/website/update', 'Admin\Website@update');
Route::post('admin/website/destroy', 'Admin\Website@destroy');
Route::post('admin/operating_hour/destroy', 'Admin\OperatingHour@destroy');