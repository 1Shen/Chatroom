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

// 首页
Route::get('/', function () {
    return view('chatroom.index');
});

// 登录退出
Route::post('login', 'UserController@login');
Route::get('logout', 'UserController@logout');

// 房间
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

// 进入房间后的初始化
Route::get('init', 'RoomController@init');