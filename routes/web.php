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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home/', 'HomeController@index')->name('home');
Route::get('/home/tabs/{tab}', 'HomeController@index')->name('home');
Route::post('/home/process', 'HomeController@process')->name('home.process');
Route::get('/home/detail/{id}', 'HomeController@detail')->name('home.detail');
Route::post('/home/detail/process', 'HomeController@detailProcess')->name('home.detail.process');
Route::get('/home/detail/delete/{id}', 'HomeController@destroy')->name('home.detail.delete');
Route::get('/home/detach/{file}/{id}', 'HomeController@detachFile');
Route::get('/home/download/{filename}', 'HomeController@downloadFile');



Route::get('/profile/change_password', 'HomeController@changePassword')->name('profile.change_password');
Route::post('/profile/password/process', 'HomeController@passwordProcess')->name('profile.password.process');
