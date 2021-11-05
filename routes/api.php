<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('users/login', 'UserController@login')->name('login');
Route::post('users/register', 'UserController@register')->name('register');

Route::middleware('auth:api')->group(function() {
    Route::prefix('users')->group(function() {
        Route::get('index', 'UserController@index');
        Route::get('{id}', 'UserController@show');
        Route::put('update/{id}', 'UserController@update');
        Route::delete('delete/{id}', 'UserController@delete');
    });

    Route::prefix('transactions')->group(function() {
        Route::get('index', 'TransactionController@index');
        Route::post('store', 'TransactionController@store');
        Route::get('history/{id}/{product_id}', 'TransactionController@history');
    });

    Route::prefix('resources')->group(function() {
        Route::get('index', 'ResourceController@index');
        Route::post('store', 'ResourceController@store');
        Route::get('{id}', 'ResourceController@show');
        Route::put('update/{id}', 'ResourceController@update');
        Route::delete('delete/{id}', 'ResourceController@delete');
    });
});