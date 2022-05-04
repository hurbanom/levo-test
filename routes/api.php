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

Route::post('/crearCuenta', 'App\Http\Controllers\AccountController@crearCuenta');
Route::post('/depositar', 'App\Http\Controllers\AccountController@depositar');
Route::post('/retirar', 'App\Http\Controllers\AccountController@retirar');
