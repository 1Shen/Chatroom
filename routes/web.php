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
    return view('chatroom.index');
});

Route::post('login', 'UserController@login');
Route::get('logout', 'UserController@logout');

Route::get('lounge', function () {
    return view('chatroom.lounge');
});
Route::get('list', 'RoomController@list');
Route::post('enter', 'RoomController@enter');
Route::get('exit', 'RoomController@exit');
Route::post('create', 'RoomController@create');
Route::get('create', function () {
    return view('chatroom.create');
});

Route::get('room', function () {
    // return view('chatroom.client');
    return view('chatroom.room');
});

Route::get('init', 'RoomController@init');