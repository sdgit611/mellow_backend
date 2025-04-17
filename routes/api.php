<?php

use App\Http\Controllers\API\EmployeeApiController;
use App\Http\Controllers\API\EmployerRegsitController;
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

ApiRoute::group(['namespace' => 'App\Http\Controllers'], function () {
    ApiRoute::get('purchased-module', ['as' => 'api.purchasedModule', 'uses' => 'HomeController@installedModule']);
});

// Route::post('employees', [EmployeeApiController::class, 'store'])->middleware('auth:sanctum');
Route::post('employees', [EmployeeApiController::class, 'store']);
Route::post('/employer-register', [EmployerRegsitController::class, 'employerRegister']);


