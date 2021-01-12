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
    return redirect()->route('login');
});

Auth::routes();

Route::get('translation/{locale}', 'Controller@translations');

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {

    Route::post('user/exists', 'UserController@exists');
    Route::resource('user', 'UserController');

    Route::resource('phone-type', 'PhoneTypeController');
    Route::resource('phone-brand', 'PhoneBrandController');
});

Route::group(['prefix' => 'seller', 'middleware' => ['auth']], function () {

    Route::get('user/config', 'UserController@config')->name('user.config');
    Route::put('user/config', 'UserController@updateConfig')->name('user.updateConfig');

    Route::get('country', 'CountryController@index')->name('country.index');

    Route::resource('customer', 'CustomerController');
});
