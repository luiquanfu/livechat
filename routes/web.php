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

// website
Route::get('', 'Website\Home@index');

// visitor
Route::post('visitor/initialize', 'Visitor\Home@initialize');
Route::post('visitor/chat/open', 'Visitor\Chat@open');
Route::post('visitor/chat/message', 'Visitor\Chat@message');
Route::post('visitor/chat/close', 'Visitor\Chat@close');
Route::post('visitor/chat/rating', 'Visitor\Chat@rating');
Route::get('visitor/{website_token}', 'Visitor\Home@index');

// admin general
Route::get('admin', 'Admin\Home@index');
Route::post('admin/initialize', 'Admin\Home@initialize');
Route::post('admin/login', 'Admin\Home@login');
Route::post('admin/logout', 'Admin\Home@logout');
Route::post('admin/test', 'Admin\Home@test');

// admin chat_display
Route::post('admin/admin/listing', 'Admin\Admin@listing');
Route::post('admin/admin/add', 'Admin\Admin@add');
Route::post('admin/admin/edit', 'Admin\Admin@edit');
Route::post('admin/admin/update', 'Admin\Admin@update');
Route::post('admin/admin/destroy', 'Admin\Admin@destroy');

// admin chat_display
Route::post('admin/chat_display/listing', 'Admin\ChatDisplay@listing');
Route::post('admin/chat_display/add', 'Admin\ChatDisplay@add');
Route::post('admin/chat_display/edit', 'Admin\ChatDisplay@edit');
Route::post('admin/chat_display/update', 'Admin\ChatDisplay@update');
Route::post('admin/chat_display/destroy', 'Admin\ChatDisplay@destroy');

// admin website
Route::post('admin/website/listing', 'Admin\Website@listing');
Route::post('admin/website/add', 'Admin\Website@add');
Route::post('admin/website/edit', 'Admin\Website@edit');
Route::post('admin/website/update', 'Admin\Website@update');
Route::post('admin/website/destroy', 'Admin\Website@destroy');
Route::post('admin/operating_hour/destroy', 'Admin\OperatingHour@destroy');