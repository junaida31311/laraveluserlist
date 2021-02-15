<?php

use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });


Route::resource('user/','UserListController');
Route::get('user/edit/{id}','UserListController@edit');
Route::post('user/update/','UserListController@update');
Route::get('user/destroy/{id}', 'UserListController@destroy');
Route::post('user/exportData/', 'UserListController@exportData');
