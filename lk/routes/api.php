<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Здесь Get Запросы
 */

//EMPTY

/**
 * Здесь Post запросы
 */
Route::group([
    'middleware' => 'throttle:20,5'
], function () {
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');
    Route::post('/authorize', 'AuthController@authorizeToken');
});

/**
 * Миграции
 */
Route::get('migrate', function () {
    dd(\Artisan::call('migrate'));
});

Route::get('migrate/fresh', function () {
    dd(\Artisan::call('migrate:fresh'));
});

Route::get('migrate/seed', function () {
    dd(\Artisan::call('migrate --seed'));
});

Route::get('migrate/fresh/seed', function () {
    dd(\Artisan::call('migrate:fresh --seed'));
});

/**
 * Ver 1.0
 */

Route::get('/deal/{id}/storage/{storage_id}', "DealController@storage"); //В далнейшем поменять на безопаснее
Route::get('/payment/{id}/doc', "PaymentController@paymentDocument");