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


Route::group([
    'prefix' => 'runon'
], function() {
    Route::match(['get', 'post'], '{hash}/append', "RunOnController@append");
});

Route::group([
    'prefix' => 'app'
], function() {
    Route::match(['get', 'post'], '{hash}/push', "RunOnController@append");
});


// Route::group([
//     'prefix' => 'app',
//     'middleware' => ['app.auth']
// ], function () {
//     Route::post('{hash}/push', "AppController@push");
// });


/**
 * Миграции
 */
// Route::get('migrate', function () {
//     dd(\Artisan::call('migrate'));
// });

// Route::get('migrate/fresh', function () {
//     dd(\Artisan::call('migrate:fresh'));
// });

// Route::get('migrate/seed', function () {
//     dd(\Artisan::call('migrate --seed'));
// });

// Route::get('migrate/fresh/seed', function () {
//     dd(\Artisan::call('migrate:fresh --seed'));
// });

/**
 * Ver 1.0
 */
Route::post('/payment', "PaymentController@payment");
Route::get('/deal/{id}/storage/{storage_id}', "DealController@storage"); //В далнейшем поменять на безопаснее
Route::get('/payment/{id}/doc', "PaymentController@paymentDocument");
